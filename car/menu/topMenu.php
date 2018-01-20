<? session_start();
  $logout = $_POST['logout'];
  if($logout){
    session_destroy();
  }
?>
<!-- 상단 메뉴 -->
<div class="container" style="margin-bottom:30px;">
  <div class="row">
    <!-- <div class="col-md-8">
      <a href="/Car/main.php"><h3>차차차</h3></a>
    </div> -->
    <div id="memberState" class="col-md-4 col-md-offset-8" align="right" style="padding-right:80px;">
      <?
        $nickName = $_SESSION['nickName'];
        if(!empty($nickName)){
          echo "안녕하세요! ".$nickName."님";
          echo " <a href='javascript:logout();'>로그아웃</a> | ";
          echo " <a href='/Car/mypage/mypage.php'>내정보</a>";
        }
        else{
          echo "<a href='/Car/loginPage.php'>로그인</a>";
        }
      ?>
    </div>
  </div>
  <div class="row" style="margin:0px; padding:0px;">
    <div class="col-md-2">
      <a href="/Car/main.php"><h3>차차차</h3></a>
    </div>
    <div class="col-md-10">
      <ul class="nav nav-pills nav-justified">
        <li id="galleryMenu"> <a href="/Car/gallery/galleryList.php?page=1">갤러리</a> </li>
        <li id="boardMenu"> <a href="/Car/board/boardList.php?page=1&category=전체">게시판</a> </li>
        <!-- <li id="mypageMenu"> <a href="/Car/mypage/mypage.php">마이페이지</a> </li> -->
      </ul>
    </div>
  </div>
  <hr></hr>
</div>

<script>
  // 로그아웃
  function logout(){
    $.ajax({
      type: 'post',
      data: {logout:true},
      success: function(data){
        if(data){
          location.reload();
          alert('로그아웃 하였습니다.');
        }
      },
      error: function(){
        alert('ajax error');
      }
    });
  }
</script>
