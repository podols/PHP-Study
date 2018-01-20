<?php
  session_start();
  include '../connect/dbConnect.php';

  $flag = $_REQUEST['flag'];
  switch($flag){
    case 1: insert_writing(); break;
    case 2: update_writing(); break;
    case 3: delete_writing(); break;
    case 4: update_hits(); break;
    case 5: update_recommend(); break;
    case 6: insert_comment(); break;
    case 7: delete_comment(); break;
    case 8: temporary_storage(); break;
    case 9: select_writing_list(); break;
  }

  // 작성중인 글 임시 저장
  function temporary_storage(){
    $cur = $_POST['cur'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    if($cur == 1){
      setcookie('boardInsertTitle', $title, time()+(86400*3));   // 3일간 유지
      setcookie('boardInsertContent', $content, time()+(86400*3));   // 3일간 유지
      setcookie('boardInsertCategory', $category, time()+(86400*3));   // 3일간 유지
    }
    else if($cur == 2){
      setcookie('boardUpdateTitle', $title, time()+(86400*3));   // 3일간 유지
      setcookie('boardUpdateContent', $content, time()+(86400*3));   // 3일간 유지
      setcookie('boardUpdateCategory', $category, time()+(86400*3));   // 3일간 유지
    }
  }

  // 댓글 삭제
  function delete_comment(){
    global $mysqli;
    $commentSeq = $_POST['commentSeq'];
    $sql = "DELETE FROM board_comment
            WHERE seq = $commentSeq";

    $mysqli->query($sql);
    mysqli_close($mysqli);
  }

  // 댓글 조회
  function select_comment_list($boardSeq){
    global $mysqli;
    $sql = "SELECT seq, content, insertDate, id,
              (SELECT nickName FROM member WHERE id=board_comment.id) as nickName
            FROM board_comment
            WHERE boardSeq = $boardSeq
            ORDER BY seq";
    $result = $mysqli->query($sql);
    mysqli_close($mysqli);
    return $result;
  }

  // 댓글 등록
  function insert_comment(){
    global $mysqli;
    $memberId = $_POST['memberId'];
    $boardSeq = $_POST['boardSeq'];
    $content = $_POST['commentContent'];

    $sql = "INSERT INTO board_comment(content, insertDate, id, boardSeq)
            VALUES('$content', now(), '$memberId', '$boardSeq')";
    $mysqli->query($sql);
    mysqli_close($mysqli);
  }

  // 추천수 증가
  function update_recommend(){
    global $mysqli;
    $seq = $_POST['boardSeq'];
    $sql = "UPDATE board
            SET recommend = recommend + 1
            WHERE seq = $seq";
    $mysqli->query($sql);

    $sql = "SELECT recommend
            FROM board
            WHERE seq = $seq";
    $result = $mysqli->query($sql);
    mysqli_close($mysqli);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    echo $row['recommend'];
  }

  // 조회수 증가
  function update_hits(){
    global $mysqli;
    $seq = $_POST['seq'];

    $sql = "UPDATE board
            SET hits = hits + 1
            WHERE seq = $seq";
    $mysqli->query($sql);
    mysqli_close($mysqli);
  }

  // 글 삭제
  function delete_writing(){
    global $mysqli;
    $searchKind = $_GET['searchKind'];
    $searchTxt = $_GET['searchTxt'];
    $page = $_GET['page'];
    $boardSeq = $_GET['writingNo'];
    $category = $_GET['category'];
    $sql1 = "DELETE FROM board
            WHERE seq = $boardSeq";   // 본글 삭제

    $sql2 = "DELETE FROM board_comment
            WHERE boardSeq = $boardSeq";    // 본글에 붙어있는 댓글 삭제

    $mysqli->query($sql1);
    $mysqli->query($sql2);
    mysqli_close($mysqli);

    if(empty($searchTxt)) echo "<script>location.replace('boardList.php?page=$page&category=$category');</script>";
    else echo "<script>location.replace('boardList.php?page=$page&category=$category&searchKind=$searchKind&searchTxt=$searchTxt');</script>";

  }

  // 글 수정
  function update_writing(){
    global $mysqli;
    setcookie('boardUpdateTitle');
    setcookie('boardUpdateContent');
    $page = $_POST['page'];
    $seq = $_POST['seq'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $searchKind = $_POST['searchKind'];
    $searchTxt = $_POST['searchTxt'];
    $category = $_POST['category'];
    $sql = "UPDATE board
            SET category='$category', title='$title', content='$content'
            WHERE seq=$seq";
    $mysqli->query($sql);
    mysqli_close($mysqli);

    if(empty($searchTxt)) echo "<script> location.replace('detailBoard.php?writingNo=$seq&page=$page&category=$category'); </script>";
    else echo "<script> location.replace('detailBoard.php?writingNo=$seq&page=$page&category=$category&searchKind=$searchKind&searchTxt=$searchTxt'); </script>";

  }

  // 글 세부내용 조회 (상세보기)
  function select_detail_writing(){
    global $mysqli;
    $seq = $_GET['writingNo'];
    $sql = "SELECT category, title, content, insertDate, hits, recommend, id,
              (SELECT nickName FROM member WHERE id=board.id) as nickName
            FROM board
            WHERE seq=$seq";
    $result = $mysqli->query($sql);
    // mysqli_close($mysqli);   // 댓글 조회에서 db를 사용함으로 close하지 않는다.
    return $result;
  }

  // 글 목록 리스트 조회
  function select_writing_list(){
    global $mysqli;
    $page = $_REQUEST['page'];
    $page = ($page-1) * 10;

    $category = $_GET['category'];
    $searchKind = $_GET['searchKind'];
    $searchTxt = $_GET['searchTxt'];

    if($category == '전체') $category = '';
    if(empty($searchKind)) $searchKind = 'title';

    // if(!empty($searchTxt)){ // 검색을 했을 때
    if($searchKind == 'nickName'){
      $sql1 = "SELECT * FROM board
              WHERE id IN (SELECT id FROM member WHERE nickName LIKE '%$searchTxt%') AND category LIKE '%$category%'";

      $sql2 = "SELECT seq, category, title, date(insertDate) as insertDate, hits, recommend,
                (SELECT nickName FROM member WHERE id=board.id) as nickName,
                (SELECT COUNT(*) FROM board_comment WHERE boardSeq = board.seq) as commentCount
              FROM board
              WHERE id IN (SELECT id FROM member WHERE nickName LIKE '%$searchTxt%')
                AND category LIKE '%$category%'
              ORDER BY seq desc
              LIMIT $page, 10";
    }
    else{
      $sql1 = "SELECT * FROM board WHERE $searchKind LIKE '%$searchTxt%' AND category LIKE '%$category%'";

      $sql2 = "SELECT seq, category, title, date(insertDate) as insertDate, hits, recommend,
                (SELECT nickName FROM member WHERE id=board.id) as nickName,
                (SELECT COUNT(*) FROM board_comment WHERE boardSeq = board.seq) as commentCount
              FROM board
              WHERE $searchKind LIKE '%$searchTxt%'
                AND category LIKE '%$category%'
              ORDER BY seq desc
              LIMIT $page, 10";
    }
    // }
    // else{ // 검색을 안했을 때
    //   $sql1 = "SELECT * FROM board";
    //
    //   $sql2 = "SELECT seq, category, title, date(insertDate) as insertDate, hits, recommend,
    //             (SELECT nickName FROM member WHERE id=board.id) as nickName,
    //             (SELECT COUNT(*) FROM board_comment WHERE boardSeq = board.seq) as commentCount
    //           FROM board
    //           ORDER BY seq desc
    //           LIMIT $page, 10";
    // }

    $result1 = $mysqli->query($sql1);
    $result2 = $mysqli->query($sql2);
    mysqli_close($mysqli);
    return array($result1, $result2);
  }

  // 글 등록
  function insert_writing(){
    global $mysqli;
    setcookie('boardInsertTitle');
    setcookie('boardInsertContent');
    $category = $_POST['category'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $id = $_SESSION['id'];
    $sql = "INSERT INTO board(category, title, content, insertDate, id)
            VALUES('$category', '$title', '$content', now(), '$id')";

    if($mysqli->query($sql)){
      echo "<script>
              location.replace('boardList.php?page=1&category=$category');
            </script>";
    }
    else{
      echo "<script>
              location.replace('createBoard.php');
            </script>";
    }
    mysqli_close($mysqli);

  }

?>
