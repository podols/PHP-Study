<?php
  // DB 쓸때 추가할 것
  // + session_start();
  // include '../connect/dbConnect.php';
  // global $mysqli;
  // mysqli_close($mysqli);

  include '../connect/dbConnect.php';

  $flag = $_POST['flag'];
  switch($flag){
    case 1: signup(); break;  // 회원가입
    case 2: check_id(); break; // id 중복확인
  }

// 회원가입
  function signup(){
    global $mysqli;
    $id = $_POST['id'];
    $pw1 = $_POST['pw1'];
    $name = $_POST['name'];
    $nickName = $_POST['nickName'];
    $emailId = $_POST['emailId'];
    $emailAddress = $_POST['emailAddress'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    $sql = "INSERT INTO member(id, pw, name, nickName, emailId, emailAddress, question, answer, registrationDate)
            VALUES ('$id', '$pw1', '$name', '$nickName', '$emailId', '$emailAddress', '$question', '$answer', now())";

    if($mysqli->query($sql)){
      echo "<script>location.replace('../main.php');</script>";
    }
    else{
      echo "<script>alert('insert 실패');</script>";
      echo "<script>location.replace('signup.php');</script>";
    }
    mysqli_close($mysqli);
  }

// id 중복확인
  function check_id(){
    global $mysqli;
    $id = $_POST['id'];
    $sql = "SELECT * FROM member WHERE id='$id'";
    $result = $mysqli->query($sql);
    $count = $result->num_rows;
    mysqli_close($mysqli);
    echo $count;
  }
?>
