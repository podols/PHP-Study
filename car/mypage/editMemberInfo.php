<? session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <title>회원정보수정</title>
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
      include('mypageDAO.php');
      $result = select_mem_info();
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $loginId = $_SESSION['id'];
    ?>
    <form method="post" action="mypageDAO.php" onsubmit="return check_mem_info();">
      <div style="border:1px solid; margin-top:50px; width:800px; height:600px; padding-top:20px; margin-left:auto; margin-right:auto">
          <h1 style="text-align:center"> 회원정보 수정 </h1>
          <table align="center" style="margin-top:50px">
            <tr height="50px">
                <td width="150px">아이디</td>
                <td><? echo $loginId; ?></td>
            </tr>
            <tr height="50px">
                <td>비밀번호</td>
                <td><input type="password" id="pw1" name="pw1" style="width:230px;" placeholder="6~20자 영문, 숫자"></td>
            </tr>
            <tr height="50px">
                <td>비밀번호 확인</td>
                <td><input type="password" id="pw2" name="pw2" style="width:230px;"></td>
            </tr>
            <tr height="50px">
                <td>이름</td>
                <td><? echo $row['name']; ?></td>
            </tr>
            <tr height="50px">
                <td>닉네임</td>
                <td><input type="text" id="nickName" name="nickName" value="<? echo $row['nickName']; ?>" style="width:230px;"></td>
            </tr>
            <tr height="50px">
                <td>이메일</td>
                <td><input type="text" id="emailId" name="emailId" value="<? echo $row['emailId']; ?>" style="width:230px;"> @ </td>
                <td><input type="text" id="emailAddress" name="emailAddress" value="<? echo $row['emailAddress']; ?>" style="width:230px;" readonly></td>
                <td>
                  <select id="emailChoice" style="width:120px">
                    <option value="default" selected>선택하세요.</option>
                    <option>naver.com</option>
                    <option>daum.net</option>
                    <option>gmail.com</option>
                    <option>hanmail.net</option>
                    <option>yahoo.com</option>
                    <option>nate.com</option>
                    <option>empas.com</option>
                    <option value="self">직접 입력</option>
                  </select>
                </td>
            </tr>
            <tr height="50px">
                <td>질문</td>
                <td>
                  <select id="question" name="question" style="width:230px">
                    <option value="default" selected>원하는 질문을 선택하세요.</option>
                    <option>가장 기억에 남는 장소는?</option>
                    <option>나의 좌우명은?</option>
                    <option>나의 보물 제 1호는?</option>
                    <option>가장 기억에 남는 선생님 성함은?</option>
                    <option>가장 생각나는 친구 이름은?</option>
                    <option>내가 존경하는 인물은?</option>
                    <option>오래도록 기억하고 싶은 날짜는? </option>
                    <option>나만의 신체 비밀은?</option>
                    <option>가장 감명깊게 본 영화는?</option>
                  </select>
                </td>
            </tr>
            <tr height="50px">
                <td>답</td>
                <td><input type="text" id="answer" name="answer" style="width:230px;"></td>
            </tr>
          </table>
          <input type="hidden" name="flag" value="3">
          <center>
            <input type="submit" value="수정완료" style="margin-top:30px">
            <input type="button" value="취소" onclick="location.replace('mypage.php');">
          </center>
      </div>
    </form>

    <script>

      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

    // 이메일 선택
      $('#emailChoice').change(function(){
        var emailChoice = $('#emailChoice').val();
        if(emailChoice == 'default'){
          $('#emailAddress').val("");
          return;
        }
        else if(emailChoice == 'self'){
          $('#emailAddress').attr("readonly", false);
          $('#emailAddress').val("");
          return;
        }
        else{
          $('#emailAddress').val(emailChoice);
        }
      });

      // 회원정보 수정 유효성 검사
      function check_mem_info(){
        var pw1 = $('#pw1').val();
        var pw2 = $('#pw2').val();
        var nickName = $('#nickName').val();
        var emailId = $('#emailId').val();
        var emailAddress = $('#emailAddress').val();
        var question = $('#question').val();
        var answer = $('#answer').val();

        if(!pw1) alert('비밀번호를 입력하세요.');
        else if(pw1.length <= 5 || pw1.length >= 21) alert('비밀번호는 6~20자리로 입력하세요.');
        else if(!pw2) alert('비밀번호를 확인하세요.');
        else if(!nickName || nickName.replace(blankPattern, "") == "") alert('닉네임을 입력하세요.');
        else if(!emailId || !emailAddress || emailId.replace(blankPattern, "") == "" || emailAddress.replace(blankPattern, "") == "") alert('이메일을 입력하세요.');
        else if(question == 'default') alert('질문을 선택하세요.');
        else if(!answer || answer.replace(blankPattern, "") == "") alert('질문에 해당하는 답을 입력하세요.');
        else if(pw1 != pw2) alert('비밀번호를 다시 확인하세요.');
        else return true;
        return false;
      }
    </script>

  </body>
</html>
