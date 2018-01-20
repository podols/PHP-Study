<?php
  $hostname = "127.0.0.1";
  $username= "root";
  $password = "패스워드";
  $dbname = "car";
  $mysqli = new mysqli($hostname, $username, $password, $dbname);
  if(mysqli_connect_errno()){
      // printf("DB Connect 실패!!");
      echo "<script>alert('DB Connect 실패');</script>";   // 요건 주석되여잇으면 주석풀기
      exit();
  }else{
      // printf("DB Connect 성공!!");
  }
  // mysqli_close($mysqli);
?>
