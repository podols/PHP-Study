<!DOCTYPE html>
<html>
  <head>
    <title>ID,PW찾기</title>

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
    <div class="container" style="margin-top:50px;">
      <h3>아이디 찾기</h3>
      <form id="findIdFrm" method="post">
        <table>
          <tr height="50px">
            <td><font size="3">이름</font></td>
            <td><input type="text" id="name1" name="name"></td>
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
        </table>
        <input type="button" value="ID 찾기" onclick="find_id();">
        <input type="button" value="취소" onclick="history.back();">
        <input type="hidden" name="flag" value="2">
      </form>
      <br><br><br>

      <!-- 비밀번호 찾기 -->
      <h3>비밀번호 찾기</h3>
      <form id="findPwFrm" action="editPw.php" method="post">
        <table>
          <tr height="50px">
            <td><font size="3">아이디</font></td>
            <td><input type="text" id="id" name="id"></td>
          </tr>
          <tr height="50px">
            <td><font size="3">이름</font></td>
            <td><input type="text" id="name2" name="name"></td>
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
        <input type="button" value="PW 찾기" onclick="find_pw();">
        <input type="button" value="취소" onclick="history.back();">
        <input type="hidden" value="3" name="flag">
      </form>
    </div>

    <script>
      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

      // pw 찾기
      function find_pw(){
        var id = $('#id').val();
        var name = $('#name2').val();
        var question = $('#question').val();
        var answer = $('#answer').val();

        if(!id) alert('아이디를 입력하세요.');
        else if(!name || name.replace(blankPattern,"") == "") alert('이름을 입력하세요.');
        else if(question == 'default') alert('질문을 선택하세요.');
        else if(!answer || answer.replace(blankPattern, "") == "") alert('질문에 해당하는 답을 입력하세요.');
        else {
          $.ajax({
            url:'mainDAO.php',
            type:'post',
            data:$('#findPwFrm').serialize(),
            success:function(result){
              if(result == 0) {
                alert('입력한 정보와 일치하는 회원이 없습니다.');
                location.reload();
              }
              else {
                $('#findPwFrm').submit();
              }
            }
          });
        }
      }

      // id찾기
      function find_id(){
        var name = $('#name1').val();
        var emailId = $('#emailId').val();
        var emailAddress = $('#emailAddress').val();

        if(!name || name.replace(blankPattern,"") == "") alert('이름을 입력하세요.');
        else if(!emailId || !emailAddress || emailId.replace(blankPattern, "") == "" || emailAddress.replace(blankPattern, "") == "") alert('이메일을 입력하세요.');
        else{
          $.ajax({
            url:'mainDAO.php',
            type:'post',
            data:$('#findIdFrm').serialize(),
            success:function(result){
              if(result) alert('찾으시는 아이디는 '+result+' 입니다.');
              else alert('입력한 정보와 일치하는 아이디가 없습니다.');
              location.reload();
            }
          });
        }
      }

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
    </script>
  </body>
</html>
