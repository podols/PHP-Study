<!DOCTYPE html>
<html>
  <head>
    <title>회원가입</title>
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
    <form id="signupFrm" method="post" action="memberDAO.php" onsubmit="return check_signup();">
      <div style="border:1px solid; margin-top:50px; width:800px; height:600px; padding-top:20px; margin-left:auto; margin-right:auto">
          <h1 style="text-align:center"> 회원가입 </h1>
          <table align="center" style="margin-top:50px">
            <tr height="50px">
                <td width="150px">아이디</td>
                <td> <input type="text" onkeyup="this.value=this.value.replace(/[^a-z0-9]/gi,'')" id="id" name="id" style="width:230px;" placeholder="5~20자 영문, 숫자"></td>
                <td width="100px"><input type="button" id="checkId" value="중복확인"></td>
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
                <td><input type="text" id="name" name="name" style="width:230px;"></td>
            </tr>
            <tr height="50px">
                <td>닉네임</td>
                <td><input type="text" id="nickName" name="nickName" style="width:230px;"></td>
            </tr>
            <tr height="50px">
                <td>이메일</td>
                <td><input type="text" id="emailId" name="emailId" style="width:230px;"> @ </td>
                <td><input type="text" id="emailAddress" name="emailAddress" style="width:230px;" readonly></td>
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
          <input type="hidden" id="flag" name="flag" value="1">
          <input type="hidden" id="checkIdFlag" value="0">
          <center>
            <input type="submit" value="가입하기" style="margin-top:30px">
            <input type="button" value="취소" onclick="history.back();">
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

    // id 중복확인
      $('#checkId').click(function(){
        var idLength = $('#id').val().length;
        if(idLength >= 5 && idLength <= 20){    // id 길이가 5 ~ 20 이면 중복확인 체크
          $.ajax({
            url:'memberDAO.php',
            type:'post',
            data:{id:$('#id').val(), flag:2},
            success:function(data){
              if(data == 0){
                alert("ID를 이용하실 수 있습니다.");
                $('#checkIdFlag').val('1');
              }
              else{
                alert('다른 ID를 입력해주세요.');
                $('#checkIdFlag').val('0');
              }
            },
            error:function(xhr,status,error){
              alert("error\nxhr : " + xhr + ", status : " + status + ", error : " + error);
            }
          });
        }
        else{
          alert('아이디를 5~20자로 입력해주세요.');
        }
      });

      // 회원가입 유효성 검사
      function check_signup(){
        var id = $('#id').val();
        var pw1 = $('#pw1').val();
        var pw2 = $('#pw2').val();
        var name = $('#name').val();
        var nickName = $('#nickName').val();
        var checkIdFlag = $('#checkIdFlag').val();
        var emailId = $('#emailId').val();
        var emailAddress = $('#emailAddress').val();
        var question = $('#question').val();
        var answer = $('#answer').val();

        if(!id) alert('아이디를 입력하세요.');
        else if(checkIdFlag == 0) alert('아이디 중복확인을 하세요.');
        else if(!pw1) alert('비밀번호를 입력하세요.');
        else if(pw1.length <= 5 || pw1.length >= 21) alert('비밀번호는 6~20자리로 입력하세요.');
        else if(!pw2) alert('비밀번호를 확인하세요.');
        else if(!name || name.replace(blankPattern,"") == "") alert('이름을 입력하세요.');
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
