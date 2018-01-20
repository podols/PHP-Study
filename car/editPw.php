<!DOCTYPE html>
<html>
  <head>
    <title>비밀번호 변경</title>
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
    <form method="post" action="mainDAO.php" onsubmit="return check_pw();">
      <div style="border:1px solid; margin-top:50px; width:800px; height:450px; padding-top:20px; margin-left:auto; margin-right:auto">
          <h1 style="text-align:center"> 새 비밀번호 설정 </h1>
          <table align="center" style="margin-top:50px">
            <tr height="50px">
                <td>비밀번호</td>
                <td><input type="password" id="pw1" name="pw1" style="width:230px;" placeholder="6~20자 영문, 숫자"></td>
            </tr>
            <tr height="50px">
                <td>비밀번호 확인</td>
                <td><input type="password" id="pw2" name="pw2" style="width:230px;"></td>
            </tr>
          </table>
          <input type="hidden" name="flag" value="4">
          <center>
            <input type="submit" value="수정완료" style="margin-top:30px">
            <input type="button" value="취소" onclick="location.replace('findInfo.php');">
          </center>
      </div>
      <input type="hidden" value="<? echo $_POST['id']; ?>" name="id">
    </form>

    <script>
      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

      // 회원정보 수정 유효성 검사
      function check_pw(){
        var pw1 = $('#pw1').val();
        var pw2 = $('#pw2').val();

        if(!pw1) alert('비밀번호를 입력하세요.');
        else if(pw1.length <= 5 || pw1.length >= 21) alert('비밀번호는 6~20자리로 입력하세요.');
        else if(!pw2) alert('비밀번호를 확인하세요.');
        else if(pw1 != pw2) alert('비밀번호를 다시 확인하세요.');
        else return true;
        return false;
      }
    </script>

  </body>
</html>
