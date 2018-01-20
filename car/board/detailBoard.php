<? session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <title>게시판</title>
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
      include('boardDAO.php');

      $writingResult = select_detail_writing();  // 글 세부내용 조회
      $commentResult = select_comment_list($_GET['writingNo']);   // 댓글 조회

      $wtRow = $writingResult->fetch_array(MYSQLI_ASSOC);

      $loginId = $_SESSION['id'];
      $nickName = $_SESSION['nickName'];
      $writingId = $wtRow['id'];   // 글을 작성한 id
    ?>

<!-- 글 세부내용 -->
    <div class="container">
      <form name="detailWritingFrm" method="post" action="editBoard.php">
        제목
        <input type="text" name="title" value="<? echo $wtRow['title']; ?>" style="width:1105px;" readOnly> <br/>
        분류 : <b><? echo $wtRow['category']; ?></b> &nbsp;&nbsp; | &nbsp;&nbsp;
        작성자: <b><? echo $wtRow['nickName']; ?></b> &nbsp;&nbsp; | &nbsp;&nbsp;
        작성일: <b><? echo $wtRow['insertDate']; ?></b>
        <span style="float:right; margin-right:15px">
          조회 <b><? echo $wtRow['hits']; ?></b> &nbsp;|&nbsp;
          추천 <b id="recommend"><? echo $wtRow['recommend']; ?></b>
        </span>
        <textarea name="content" rows="20" cols="155" readOnly><? echo $wtRow['content']; ?></textarea> <br/>
        <input type="hidden" id="boardSeq" name="boardSeq" value="<? echo $_GET['writingNo']; ?>">
        <input type="hidden" id="recommendFlag" value="0">
        <input type="hidden" id="memberId" value="<? echo $loginId; ?>">
        <input type="hidden" name="page" value="<? echo $_GET['page']; ?>">
        <input type="hidden" name="searchKind" value="<? echo $_GET['searchKind']; ?>">
        <input type="hidden" name="searchTxt" value="<? echo $_GET['searchTxt']; ?>">
        <input type="hidden" name="category" value="<? echo $wtRow['category']; ?>">
      </form>
<? if(!empty($loginId)){ ?>
      <center><input type="button" onclick="update_recommend();" value="추천"></center> <br/>
<? } ?>
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
          <!-- <tr style="height:30px;">
            <td><? echo $cmRow['insertDate']; ?></td>
          </tr> -->
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
        $('#boardMenu').addClass("active");
      }

    // 목록으로 이동
      function move_list(){
        var searchKind = "<? echo $_GET['searchKind']; ?>";
        var searchTxt = "<? echo $_GET['searchTxt']; ?>";
        var page = "<? echo $_GET['page']; ?>";
        var category = "<? echo $_GET['category']; ?>";

        if(searchTxt) location.href='boardList.php?page='+page+"&category="+category+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
        else location.href='boardList.php?page='+page+"&category="+category;
      }

    // 댓글 삭제
      function delete_comment(commentSeq){
        var result = confirm("정말 삭제하시겠습니까?");
        if(result) {
          $.ajax({
            url: 'boardDAO.php',
            type: 'post',
            data: {flag:7, commentSeq:commentSeq},
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
        var boardSeq = $('#boardSeq').val();
        var commentContent = $('#commentContent').val();
        if(commentContent.replace(blankPattern, "") != ""){
          $.ajax({
            url:'boardDAO.php',
            type:'post',
            data:{flag:6, memberId:memberId, boardSeq:boardSeq, commentContent:commentContent},
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
        var boardSeq = $('#boardSeq').val();
        var page = <? echo $_GET['page']; ?>;
        var searchKind = "<? echo $_GET['searchKind']; ?>";
        var searchTxt = "<? echo $_GET['searchTxt']; ?>";
        var category = "<? echo $_GET['category']; ?>";
        if(result) {
          if(searchTxt){
            location.replace('boardDAO.php?flag=3&writingNo='+boardSeq+'&page='+page+"&category="+category+"&searchKind="+searchKind+"&searchTxt="+searchTxt);
          }
          else location.replace('boardDAO.php?flag=3&writingNo='+boardSeq+'&page='+page+'&category='+category);
        }
        else return;
      }

      // 추천 기능
      function update_recommend(){
        var recommendFlag = $('#recommendFlag').val();
        if(recommendFlag == 0){
          $.ajax({
            url: 'boardDAO.php',
            type: 'post',
            data: {flag:5, boardSeq:$('#boardSeq').val()},
            success: function(result){
              $('#recommendFlag').val('1');
              $('#recommend').html(result);
              alert('추천하였습니다.');
            },
            error: function(result){
              alert('ajax error');
            }
          });
        }
        else{
          alert('이미 추천을 했습니다.');
        }
      }

      // 수정화면으로 이동
      function submit_editBoard(){
        document.detailWritingFrm.submit();
      }
    </script>
  </body>
</html>
