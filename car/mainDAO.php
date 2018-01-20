<?php
  session_start();
  include('connect/dbConnect.php');

  $flag = $_POST['flag'];
  switch($flag){
    case 1: check_login(); break;   // 로그인 확인
    case 2: find_id(); break;   // id찾기
    case 3: find_pw(); break; // pw찾기
    case 4: update_pw(); break; // pw 변경
  }

  // pw 변경
  function update_pw(){
    global $mysqli;
    $id = $_POST['id'];
    $pw1 = $_POST['pw1'];
    $sql = "UPDATE member SET pw = '$pw1' WHERE id = '$id'";
    $mysqli->query($sql);
    mysqli_close($mysqli);
    echo "<script>location.replace('main.php');</script>";
  }

  // pw 찾기
  function find_pw(){
    global $mysqli;
    $id = $_POST['id'];
    $name = $_POST['name'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $sql = "SELECT id FROM member WHERE id='$id' AND name='$name' AND question='$question' AND answer='$answer'";
    $result = $mysqli->query($sql);
    $num = $result->num_rows;

    mysqli_close($mysqli);
    echo $num;
  }

  // id 찾기
  function find_id(){
    global $mysqli;
    $name = $_POST['name'];
    $emailId = $_POST['emailId'];
    $emailAddress = $_POST['emailAddress'];

    $sql = "SELECT id FROM member WHERE name='$name' AND emailId='$emailId' AND emailAddress='$emailAddress'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    mysqli_close($mysqli);
    echo $row['id'];
  }

  // 게시판 최신, 조회best, 추천best 목록
  function select_board_list(){
    global $mysqli;
    // 최신글 10개
    $sql1 = "SELECT seq, category, title
            FROM board
            ORDER BY seq DESC
            LIMIT 0, 10";

    // 조회수 best 10개
    $sql2 = "SELECT seq, category, title
            FROM board
            ORDER BY hits DESC
            LIMIT 0, 10";

    // 추천수 best 10개
    $sql3 = "SELECT seq, category, title
            FROM board
            ORDER BY recommend DESC
            LIMIT 0, 10";

    $result1 = $mysqli->query($sql1);
    $result2 = $mysqli->query($sql2);
    $result3 = $mysqli->query($sql3);
    mysqli_close($mysqli);
    return array($result1, $result2, $result3);
  }

  // 최신 갤러리 조회
  function select_latest_gallery(){
    global $mysqli;
    $sql = "SELECT seq, title, imgPath1
            FROM gallery
            ORDER BY seq DESC
            LIMIT 0, 5";

    $result = $mysqli->query($sql);
    // mysqli_close($mysqli);
    return $result;
  }

  // 로그인 확인
  function check_login(){
    global $mysqli;

    $id = $_POST['loginId'];
    $pw = $_POST['loginPw'];

    $sql = "SELECT * FROM member WHERE id='$id' AND pw='$pw'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);

    mysqli_close($mysqli);

    if($row != null){
      $_SESSION['id'] = $row['id'];
      // $_SESSION['pw'] = $row['pw'];
      $_SESSION['name'] = $row['name'];
      $_SESSION['nickName'] = $row['nickName'];
      echo $_SESSION['nickName'];
    }
    else{
      echo false;
    }
  }
?>
