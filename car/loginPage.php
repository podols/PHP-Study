<? session_start();?>
<!DOCTYPE html>
<html>
  <head>
    <title>로그인</title>

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
    <h1 align="center" style="position:absolute; top:25%; left:50%; margin-left:-80px;">로그인</h1>
    <!-- 로그인 -->
    <div id="loginBox" style="border:1px solid; position:absolute; top:40%; left:50%; padding:20px; margin-left:-200px;">
      <form method="post" id="loginFrm">
        <input type="text" name="loginId" style="height:50px; width:300px" placeholder="아이디">
        <input type="button" id="loginBtn" class="btn btn-primary" value="로그인"> <br/>
        <input type="password" id="loginPw" name="loginPw" style="height:50px; width:300px" placeholder="비밀번호"> <br/>
        <input type="hidden" name="flag" value="1">
      </form>
      <a href="member/signup.php">회원가입</a>
      <a href="findInfo.php">ID/PW 찾기</a>
    </div>
    <script>
      // 로그인
      $('#loginBtn').click(function(){
        $.ajax({
          url:'mainDAO.php',
          type:'post',
          data:$('#loginFrm').serialize(),
          success:function(result){
              if(result){
                history.back();
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
