<? session_start();?>
<!DOCTYPE html>
<html>
  <head>
    <title>회원정보확인</title>

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
    <?
      $loginId = $_SESSION['id'];
    ?>
    <h1 align="center" style="margin-top:150px;">회원정보 확인</h1>
    <!-- 로그인 -->
    <form method="post" action="mypageDAO.php" onsubmit="return check_exception();">
      <div align="center" style="margin-top:50px;">
        <div style="width:500px; height:300px; display:table-cell; text-align:left">
          <font size="4">아이디 : <? echo $loginId; ?> </font><br><br>
          <font size="4">비밀번호 : </font><input type="password" id="password" name="password" style="height:40px; width:300px"> <br><br><br>
          <center>
            <input type="submit" value="확인">
            <input type="button" value="취소" onclick="history.back();">
          </center>
          <input type="hidden" name="flag" value="2">
        </div>
      </div>
    </form>
    <script>
      // 유효성 검사
      function check_exception(){
        var pw = $('#password').val();
        if(!pw) {
          alert('비밀번호를 입력하세요.');
          return false;
        }
        return true;
      }
    </script>
  </body>
</html>
