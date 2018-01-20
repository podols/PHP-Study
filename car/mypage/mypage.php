<? session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <title>내정보</title>
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
      include('../menu/topMenu.php');
      include('mypageDAO.php');

      $result = select_mypage();
      $row = $result[0]->fetch_array(MYSQLI_ASSOC);
      $writingNum = $result[1]->num_rows;
      $commentNum = $result[2]->num_rows;
      $id = $_SESSION['id'];
      $nickName = $_SESSION['nickName'];

    ?>
    <div style="width:800px; height:450px; padding-top:20px; margin-left:auto; margin-right:auto">
        <h1 style="text-align:center"> 내 정보 </h1>
        <table align="center" style="margin-top:50px">
          <tr height="50px">
              <td width="150px">닉네임: </td>
              <td> <? echo $nickName; ?> </td>
          </tr>
          <tr height="50px">
              <td>가입일: </td>
              <td><? echo $row['registrationDate']; ?></td>
          </tr>
          <tr height="50px">
              <td>작성한 게시글 수: </td>
              <td><? echo $writingNum; ?></td>
          </tr>
          <tr height="50px">
              <td>작성한 댓글 수: </td>
              <td><? echo $commentNum; ?></td>
          </tr>
        </table>
        <center style="margin-top:50px;">
          <input type="button" value="정보 수정" onclick="location.href='checkInfo.php'">
          <input type="button" value="회원 탈퇴" onclick="cancel_member();">
        </center>
    </div>

    <script>
      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

      // 회원탈퇴
      function cancel_member(){
        var result = confirm('탈퇴하시면 회원정보를 다시 복구할 수 없습니다.\n정말 탈퇴하시겠습니까?');
        if(result){
          location.href='mypageDAO.php?flag=1';
        }
      }

    </script>

  </body>
</html>
