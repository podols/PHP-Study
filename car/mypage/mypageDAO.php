<?php
  session_start();
  include '../connect/dbConnect.php';

  $flag = $_REQUEST['flag'];
  switch($flag){
    case 1: cancel_member(); break;
    case 2: check_info(); break;
    case 3: update_mem_info(); break;
  }

  // 회원정보 수정 완료
  function update_mem_info(){
    global $mysqli;
    $id = $_SESSION['id'];
    $pw = $_POST['pw1'];
    $nickName = $_POST['nickName'];
    $emailId = $_POST['emailId'];
    $emailAddress = $_POST['emailAddress'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    $sql = "UPDATE member
            SET pw = '$pw', nickName = '$nickName', emailId = '$emailId', emailAddress = '$emailAddress', question = '$question', answer = '$answer'
            WHERE id = '$id'";
    $mysqli->query($sql);
    mysqli_close($mysqli);
    $_SESSION['nickName'] = $nickName;
    echo "<script>location.replace('mypage.php');</script>";
  }

  // 회원정보 불러오기 (회원정보 수정 페이지)
  function select_mem_info(){
    global $mysqli;
    $id = $_SESSION['id'];
    $sql = "SELECT *
            FROM member
            WHERE id = '$id'";
    $result = $mysqli->query($sql);
    mysqli_close($mysqli);
    return $result;
  }

  // 회원정보 확인
  function check_info(){
    global $mysqli;
    $id = $_SESSION['id'];
    $pw = $_POST['password'];
    $sql = "SELECT id FROM member WHERE id='$id' AND pw='$pw'";
    $result = $mysqli->query($sql);
    $exist = $result->num_rows;
    mysqli_close($mysqli);
    if($exist == 0){
      echo "<script>
              alert('비밀번호가 틀렸습니다.');
              history.back();
            </script>";
    }
    else{
      echo "<script>location.replace('editMemberInfo.php');</script>";
    }
  }

  // 회원탈퇴 (회원정보삭제, 세션 삭제)
  function cancel_member(){
    global $mysqli;
    $loginId = $_SESSION['id'];
    $sql1 = "DELETE FROM board WHERE id = '$loginId'";
    $sql2 = "DELETE FROM board_comment WHERE id = '$loginId'";
    $sql3 = "DELETE FROM gallery WHERE id = '$loginId'";
    $sql4 = "DELETE FROM gallery_comment WHERE id = '$loginId'";
    $sql5 = "DELETE FROM member WHERE id = '$loginId'";
    $mysqli->query($sql1);
    $mysqli->query($sql2);
    $mysqli->query($sql3);
    $mysqli->query($sql4);
    $mysqli->query($sql5);
    mysqli_close($mysqli);
    session_destroy();
    echo "<script>location.replace('../main.php');</script>";
  }

  // 마이페이지 조회
  function select_mypage(){
    global $mysqli;
    $loginId = $_SESSION['id'];
    $sql1 = "SELECT nickName, registrationDate FROM member WHERE id = '$loginId'";
    $sql2 = "SELECT seq FROM board WHERE id = '$loginId' UNION ALL
            SELECT seq FROM gallery WHERE id = '$loginId'";
    $sql3 = "SELECT seq FROM board_comment WHERE id = '$loginId' UNION ALL
            SELECT seq FROM gallery_comment WHERE id = '$loginId'";
    $result1 = $mysqli->query($sql1);
    $result2 = $mysqli->query($sql2);
    $result3 = $mysqli->query($sql3);
    return array($result1, $result2, $result3);
    mysqli_close($mysqli);
  }
?>
