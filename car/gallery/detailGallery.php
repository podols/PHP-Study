<? session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <title>갤러리</title>
    <!-- 합쳐지고 최소화된 최신 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <!-- 부가적인 테마 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <!-- 제이쿼리 압축 CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous">
    </script>
  </head>
  <body>
    <?php
      include('../menu/topMenu.php');
      include('galleryDAO.php');

      $writingResult = select_detail_writing();  // 글 세부내용 조회
      $commentResult = select_comment_list($_GET['writingNo']);   // 댓글 조회

      $wtRow = $writingResult->fetch_array(MYSQLI_ASSOC);

      $loginId = $_SESSION['id'];
      $nickName = $_SESSION['nickName'];
      $writingId = $wtRow['id'];   // 글을 작성한 id
    ?>

<!-- 글 세부내용 -->
    <div class="container">
      <form name="detailWritingFrm" method="post" action="editGallery.php">
        <font size="4"><? echo $wtRow['title']; ?></font> <br>
        <font size="2"><? echo $wtRow['nickName']; ?></font> &nbsp; | &nbsp;
        <font size="2"><? echo $wtRow['insertDate']; ?></font> &nbsp; | &nbsp;
        <font size="2">조회 <? echo $wtRow['hits']; ?></font>
        <hr></hr><br>
        <!-- 이미지 출력 -->
        <div style="width:100%; height:auto">
          <?
          $imgPath1 = $wtRow['imgPath1'];
          $imgPath2 = $wtRow['imgPath2'];
          $imgPath3 = $wtRow['imgPath3'];

          echo "<img src='$imgPath1' style='max-width:100%; height:auto;'> <br>";
          if(!empty($imgPath2)) echo "<img src='$imgPath2' style='max-width:100%; height:auto;'> <br>";
          if(!empty($imgPath3)) echo "<img src='$imgPath3' style='max-width:100%; height:auto;'> <br>";
          ?>
        </div>
        <!-- 글 내용 pre태그에 white-space는 tab은 표시안하는것이고, word-break는 가로로 넘어가면 자동 줄바꿈하는 스타일 -->
        <pre style="width:100%; background-color:#FFFFFF; white-space:pre-line; word-break:break-all; border:0px">
          <font size="2"><? echo $wtRow['content']; ?></font>
        </pre>

        <!-- 히든 값 -->
        <input type="hidden" name="title" value="<? echo $wtRow['title']; ?>">
        <input type="hidden" name="content" value="<? echo $wtRow['content']; ?>">
        <input type="hidden" name="imgPath1" value="<? echo $imgPath1; ?>">
        <input type="hidden" name="imgPath2" value="<? echo $imgPath2; ?>">
        <input type="hidden" name="imgPath3" value="<? echo $imgPath3; ?>">

        <input type="hidden" id="gallerySeq" name="gallerySeq" value="<? echo $_GET['writingNo']; ?>">
        <input type="hidden" id="memberId" value="<? echo $loginId; ?>">
        <input type="hidden" name="page" value="<? echo $_GET['page']; ?>">
        <input type="hidden" name="searchKind" value="<? echo $_GET['searchKind']; ?>">
        <input type="hidden" name="searchTxt" value="<? echo $_GET['searchTxt']; ?>">
      </form>

<!-- 댓글 조회-->
      <h3>댓글 (<? echo $commentResult->num_rows; ?>)</h3>
      <div border="1px">
        <table width="100%">
    <? while($cmRow = $commentResult->fetch_array(MYSQLI_ASSOC)){ ?>
          <tr style="border-top:1px solid;">
            <td width="180px" style="height:60px;">
              <? echo $cmRow['nickName']; ?> <br/>
              <? echo $cmRow['insertDate']; ?>
            </td>
            <td><? echo $cmRow['content']; ?></td>
            <td width="50px">
            <? if($loginId == $cmRow['id']){ ?>
              <a href="javascript:delete_comment(<? echo $cmRow['seq']; ?>);">삭제</a>
            <? } ?>
            </td>
          </tr>
    <? } ?>
        </table>
<!-- 댓글 등록 -->
    <? if(!empty($loginId)){ ?>
        <hr></hr>
        <table style="margin-top:20px;" width="100%">
          <tr>
            <td width="130px" style="text-align:center"> <? echo $nickName; ?> </td>
            <td><textarea id="commentContent" rows="4" cols="130"></textarea></td>
            <td width="50px"> <input type="button" onclick="insert_comment();" value="등록"> </td>
          </tr>
        </table>
    <? } ?>
      </div>
<!-- 댓글 끝 -->
<!-- 게시글 수정, 삭제, 목록 -->
      <span style="float:right; margin-top:30px; margin-bottom:20px">
    <? if($loginId == $writingId){  ?>
        <input type="button" onclick="submit_editBoard();" value="수정">
        <input type="button" onclick="check_delete();" value="삭제">
    <? } ?>
        <input type="button" value="목록" onclick="move_list();">
      </span>

    </div>

    <script>

      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

    // 상단 메뉴 활성화
      window.onload = function(){
        $('#galleryMenu').addClass("active");
      }

    // 목록으로 이동
      function move_list(){
        var searchKind = "<? echo $_GET['searchKind']; ?>";
        var searchTxt = "<? echo $_GET['searchTxt']; ?>";
        var page = "<? echo $_GET['page']; ?>";

        if(searchTxt) location.href='galleryList.php?page='+page+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
        else location.href='galleryList.php?page='+page;
      }

    // 댓글 삭제
      function delete_comment(commentSeq){
        var result = confirm("정말 삭제하시겠습니까?");
        if(result) {
          $.ajax({
            url: 'galleryDAO.php',
            type: 'post',
            data: {flag:3, commentSeq:commentSeq},
            success: function(){
              location.reload();
            },
            error: function(){
              alert('ajax error');
            }
          });
        }
      }

    // 댓글 등록
      function insert_comment(){
        var memberId = $('#memberId').val();
        var gallerySeq = $('#gallerySeq').val();
        var commentContent = $('#commentContent').val();
        if(commentContent.replace(blankPattern, "") != ""){
          $.ajax({
            url:'galleryDAO.php',
            type:'post',
            data:{flag:2, memberId:memberId, gallerySeq:gallerySeq, commentContent:commentContent},
            success:function(){
              location.reload();
            },
            error:function(){
              alert('ajax error');
            }
          });
        }
        else{
          alert('댓글을 입력하세요.');
        }
      }

    // 글 삭제 확인
      function check_delete(){
        var result = confirm("정말 삭제하시겠습니까?");
        var gallerySeq = $('#gallerySeq').val();
        var page = <? echo $_GET['page']; ?>;
        var searchKind = "<? echo $_GET['searchKind']; ?>";
        var searchTxt = "<? echo $_GET['searchTxt']; ?>";
        if(result) {
          // if(searchTxt){
            var imgPath1 = "<? echo $wtRow['imgPath1']; ?>";
            var imgPath2 = "<? echo $wtRow['imgPath2']; ?>";
            var imgPath3 = "<? echo $wtRow['imgPath3']; ?>";
          //   location.replace('galleryDAO.php?flag=6&writingNo='+gallerySeq+'&page='+page+"&searchKind="+searchKind+"&searchTxt="+searchTxt);
          // }
          // else location.replace('galleryDAO.php?flag=6&writingNo='+gallerySeq+'&page='+page);
          $.ajax({
            url:'galleryDAO.php',
            type:'post',
            data:{flag:6, writingNo:gallerySeq, page:page, searchKind:searchKind, searchTxt:searchTxt, imgPath1:imgPath1, imgPath2:imgPath2, imgPath3:imgPath3},
            success: function(){
              if(!searchTxt) location.replace('galleryList.php?page='+page);
              else location.replace('galleryList.php?page='+page+'&searchKind='+searchKind+'&searchTxt='+searchTxt);
            },
            error: function(){

            }
          });
        }
        else return;
      }

      // 수정화면으로 이동
      function submit_editBoard(){
        document.detailWritingFrm.submit();
      }
    </script>
  </body>
</html>
