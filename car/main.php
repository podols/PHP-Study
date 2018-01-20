<? session_start();?>
<!DOCTYPE html>
<html>
  <head>
    <title>차차차</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  </head>
  <body>
    <?
      include('menu/topMenu.php');
      include('mainDAO.php');
      $result1 = select_latest_gallery();   // 최신 갤러리
      $result2 = select_board_list();       // 최신, 조회수best, 추천수best 게시판
      $galleryNum = mysqli_num_rows($result1);  // 최신 갤러리 개수 (0개이면 대체 이미지를 띄움)
    ?>

    <!-- 로그인 -->
    <div class="container">
      <div id="loginBox"class="col-md-3" style="border:1px solid; height:122px">
      <?
        $nickName = $_SESSION['nickName'];
        if(!empty($nickName)){
          echo $nickName."님 환영합니다. <br/>";
          echo "<a href='javascript:logout();'>로그아웃</a>";
        }
        else{ ?>
          <form method="post" id="loginFrm">
            <input type="text" name="loginId" placeholder="아이디">
            <input type="button" id="loginBtn" class="btn btn-primary" value="로그인"> <br/>
            <input type="password" id="loginPw" name="loginPw" placeholder="비밀번호"> <br/>
            <input type="hidden" name="flag" value="1">
          </form>
          <a href="member/signup.php">회원가입</a>
          <a href="findInfo.php">ID/PW 찾기</a>
      <? } ?>
      </div>

      <!-- 갤러리 이미지 -->
      <div class="col-md-9" style="padding-left:30px; height:450px;">
        최신 갤러리 <br/>
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
          <!-- Indicators -->
          <ol class="carousel-indicators">
            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
            <?
              for($i=1; $i<$galleryNum; $i++){
                echo "<li data-target='#carousel-example-generic' data-slide-to='$i'></li>";
              }
            ?>
          </ol>
          <!-- Wrapper for slides -->
          <div class="carousel-inner" role="listbox">
            <!-- 1 -->
            <div class="item active">
              <? $galleryRow = $result1->fetch_array(MYSQLI_ASSOC); ?>
              <a href="javascript: update_gallery_hits(<? echo $galleryRow['seq']; ?>, 1);">
                <img src="./gallery/<? echo $galleryRow['imgPath1']; ?>" onerror="this.src='./no_image.gif'" style="width:100%; height:420px;">
              </a>
              <div class="carousel-caption">
                <? echo $galleryRow['title']; ?>
              </div>
            </div>
            <?
              while($galleryRow = $result1->fetch_array(MYSQLI_ASSOC)) {
                echo "<div class='item'>
                        <a href='javascript: update_gallery_hits(".$galleryRow['seq'].", 1);'>
                          <img src='gallery/".$galleryRow['imgPath1']."' style='width:100%; height:420px;'>
                        </a>
                        <div class='carousel-caption'>"
                          .$galleryRow['title'].
                        "</div>
                      </div>";
              }
            ?>
          </div>
          <!-- Controls -->
    <? if($galleryNum >= 2){ ?>
          <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
    <? } ?>
        </div>
      </div>

      <!-- 게시판 글 -->
      <div style="margin-top:20px; margin-bottom:30px;" class="col-md-12">
        게시판
        <table class="table table-bordered">
          <tr>
            <th width="33%">최신글</th>
            <th width="33%">조회수 BEST</th>
            <th width="33%">추천수 BEST</th>
          </tr>
<?
  while($latestWritingRow = $result2[0]->fetch_array(MYSQLI_ASSOC)){
    $hitsBestRow = $result2[1]->fetch_array(MYSQLI_ASSOC);
    $recommendBestRow = $result2[2]->fetch_array(MYSQLI_ASSOC);
    echo "<tr>
            <td> <a href='javascript: update_board_hits(".$latestWritingRow['seq'].",\"".$latestWritingRow['category']."\", 1);'>".$latestWritingRow['title']."</a> </td>
            <td> <a href='javascript: update_board_hits(".$hitsBestRow['seq'].",\"".$hitsBestRow['category']."\", 1);'>".$hitsBestRow['title']."</a> </td>
            <td> <a href='javascript: update_board_hits(".$recommendBestRow['seq'].",\"".$recommendBestRow['category']."\", 1);'>".$recommendBestRow['title']."</a> </td>
          </tr>";
  }
?>
        </table>
      </div>
    </div>
    <script>
      // 조회수 증가 시키고, 갤러리 세부 페이지로 이동 (상세조회)
      function update_gallery_hits(seq, page){
        $.ajax({
          url: 'gallery/galleryDAO.php',
          type: 'post',
          data: {flag:5, seq:seq},
          success: function(){
            // if(searchTxt) location.href="detailGallery.php?writingNo="+seq+"&page="+page+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
            location.href="gallery/detailGallery.php?writingNo="+seq+"&page="+page;
          },
          error: function(){
            alert('ajax error');
          }
        });
      }

      // 게시판 글 조회수 증가
      function update_board_hits(seq, category, page){
        $.ajax({
          url: 'board/boardDAO.php',
          type: 'post',
          data: {flag:4, seq:seq},
          success: function(){
            // if(searchTxt) location.href="detailBoard.php?writingNo="+seq+"&page="+page+"&category="+category+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
            location.href="board/detailBoard.php?writingNo="+seq+"&page="+page+"&category="+category;
          },
          error: function(){
            alert('ajax error');
          }
        });
      }

      // 캐러셀 자동 슬라이딩
      $('.carousel').carousel({
        interval: 3000
      })

      // 로그인
      $('#loginBtn').click(function(){
        $.ajax({
          url:'mainDAO.php',
          type:'post',
          data:$('#loginFrm').serialize(),
          success:function(result){
              if(result){
                var nickName = result;
                $('#loginBox').html(nickName+"님 환영합니다. <br/>"
                                    +"<a href='javascript:logout();'>로그아웃</a>");
                $('#memberState').html("안녕하세요! "+nickName+"님 <a href='javascript:logout();'>로그아웃</a> | "
                                        +"<a href='/Car/mypage/mypage.php'>내정보</a>");
              }
              else{
                alert('아이디와 비밀번호를 다시 확인하세요.');
                $('#loginPw').val('');
              }
          },
          error:function(){
            alert('ajax error');
          }
        });
      });
    </script>
  </body>
</html>
