<?php
  session_start();
  include '../connect/dbConnect.php';
  $flag = $_REQUEST['flag'];
  switch($flag){
    case 1: image_upload(); break;    // 이미지 업로드
    case 2: insert_comment(); break;  // 댓글 등록
    case 3: delete_comment(); break;  // 댓글 삭제
    // case 4: update_writing(); break;  // 게시글 수정
    case 5: update_hits(); break;     // 조회수 증가
    case 6: delete_writing(); break;  // 게시글 삭제
    case 7: temporary_storage(); break; // 작성중인 글 임시저장
  }

  // 작성중인 글 임시 저장
  function temporary_storage(){
    $cur = $_POST['cur'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    if($cur == 1){
      setcookie('galleryInsertTitle', $title, time()+(86400*3));   // 3일간 유지
      setcookie('galleryInsertContent', $content, time()+(86400*3));   // 3일간 유지
    }
    else if($cur == 2){
      setcookie('galleryUpdateTitle', $title, time()+(86400*3));   // 3일간 유지
      setcookie('galleryUpdateContent', $content, time()+(86400*3));   // 3일간 유지
    }
  }

  // 게시글 삭제
  function delete_writing(){
    // 이미지도 서버에서 삭제
    $imgPath1 = $_POST['imgPath1'];
    $imgPath2 = $_POST['imgPath2'];
    $imgPath3 = $_POST['imgPath3'];
    if(is_file($imgPath1)) unlink($imgPath1);
    if(is_file($imgPath2)) unlink($imgPath2);
    if(is_file($imgPath3)) unlink($imgPath3);

    global $mysqli;
    $searchKind = $_POST['searchKind'];
    $searchTxt = $_POST['searchTxt'];
    $page = $_POST['page'];
    $gallerySeq = $_POST['writingNo'];

    $sql1 = "DELETE FROM gallery
            WHERE seq = $gallerySeq";   // 본글 삭제

    $sql2 = "DELETE FROM gallery_comment
            WHERE gallerySeq = $gallerySeq";    // 본글에 붙어있는 댓글 삭제

    $mysqli->query($sql1);
    $mysqli->query($sql2);
    mysqli_close($mysqli);

  }

  // 게시글 수정
  function update_writing($imgPath1, $imgPath2, $imgPath3){
    global $mysqli;
    $page = $_POST['page'];
    $searchKind = $_POST['searchKind'];
    $searchTxt = $_POST['searchTxt'];
    $seq = $_POST['seq'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    setcookie('galleryUpdateTitle');
    setcookie('galleryUpdateContent');

    // 수정페이지 이동 후 DB에서 불러온 이미지를 미리보기에서 삭제한 이미지를 정렬
    if(empty($imgPath1) && !empty($imgPath2)){
      $imgPath1 = $imgPath2;
      $imgPath2 = '';
    }
    else if(empty($imgPath1) && !empty($imgPath3)) {
      $imgPath1 = $imgPath3;
      $imgPath3 = '';
    }
    if(empty($imgPath2) && !empty($imgPath3)){
      $imgPath2 = $imgPath3;
      $imgPath3 = '';
    }

    $sql = "UPDATE gallery
            SET title = '$title', content = '$content', imgPath1 = '$imgPath1', imgPath2 = '$imgPath2', imgPath3 = '$imgPath3'
            WHERE seq = $seq";

    $mysqli->query($sql);
    mysqli_close($mysqli);

    if(empty($searchTxt)) echo "<script> location.replace('detailGallery.php?writingNo=$seq&page=$page'); </script>";
    else echo "<script> location.replace('detailGallery.php?writingNo=$seq&page=$page&searchKind=$searchKind&searchTxt=$searchTxt'); </script>";
  }

  // 댓글 삭제
  function delete_comment(){
    global $mysqli;
    $commentSeq = $_POST['commentSeq'];
    $sql = "DELETE FROM gallery_comment WHERE seq = $commentSeq";
    $mysqli->query($sql);
    mysqli_close($mysqli);
  }

  // 댓글 등록
  function insert_comment(){
    global $mysqli;
    $memberId = $_POST['memberId'];
    $gallerySeq = $_POST['gallerySeq'];
    $content = $_POST['commentContent'];

    $sql = "INSERT INTO gallery_comment(content, insertDate, id, gallerySeq)
            VALUES('$content', now(), '$memberId', '$gallerySeq')";
    $mysqli->query($sql);
    mysqli_close($mysqli);
  }

  // 댓글 조회
  function select_comment_list($gallerySeq){
    global $mysqli;
    $sql = "SELECT seq, content, insertDate, id,
              (SELECT nickName FROM member WHERE id=gallery_comment.id) as nickName
            FROM gallery_comment
            WHERE gallerySeq = $gallerySeq
            ORDER BY seq";
    $result = $mysqli->query($sql);
    mysqli_close($mysqli);
    return $result;
  }

  // 조회수 증가
  function update_hits(){
    global $mysqli;
    $seq = $_GET['writingNo'];

    $sql = "UPDATE gallery
            SET hits = hits + 1
            WHERE seq = $seq";
    $mysqli->query($sql);
    mysqli_close($mysqli);
  }

  // 게시글 상세보기
  function select_detail_writing(){
    global $mysqli;
    $writingSeq = $_GET['writingNo'];
    $sql = "SELECT seq, title, content, insertDate, hits, imgPath1, imgPath2, imgPath3, id,
            (SELECT nickName FROM member WHERE id = gallery.id) AS nickName
            FROM gallery
            WHERE seq = $writingSeq";
    $result = $mysqli->query($sql);
    // mysqli_close($mysqli);   // 바로 댓글 조회가 있어서 연결 끊으면 안됨
    return $result;
  }

  // 갤러리 목록 조회 (리스트), 검색
  function select_gallery_list(){
    global $mysqli;
    $page = $_GET['page'];
    $page = ($page-1)*12;
    $searchKind = $_GET['searchKind'];
    $searchTxt = $_GET['searchTxt'];


    if(empty($searchKind)) $searchKind = 'title';

    if($searchKind == 'nickName'){
      $sql1 = "SELECT * FROM gallery WHERE id IN (SELECT id FROM member WHERE nickName LIKE '%$searchTxt%')";

      $sql2 = "SELECT seq, title, date_format(insertDate, '%y.%m.%d') AS insertDate, hits, imgPath1, imgPath2, imgPath3,
              (SELECT nickName FROM member WHERE id=gallery.id) AS nickName,
              (SELECT COUNT(*) FROM gallery_comment WHERE gallerySeq = gallery.seq) AS commentCount
              FROM gallery
              WHERE id IN (SELECT id FROM member WHERE nickName LIKE '%$searchTxt%')
              ORDER BY seq DESC
              LIMIT $page, 12";
    }
    else{
      $sql1 = "SELECT * FROM gallery WHERE $searchKind LIKE '%$searchTxt%'";

      $sql2 = "SELECT seq, title, date_format(insertDate, '%y.%m.%d') AS insertDate, hits, imgPath1, imgPath2, imgPath3,
              (SELECT nickName FROM member WHERE id=gallery.id) AS nickName,
              (SELECT COUNT(*) FROM gallery_comment WHERE gallerySeq = gallery.seq) AS commentCount
              FROM gallery
              WHERE $searchKind LIKE '%$searchTxt%'
              ORDER BY seq DESC
              LIMIT $page, 12";
    }

    $result1 = $mysqli->query($sql1);
    $result2 = $mysqli->query($sql2);
    mysqli_close($mysqli);
    return array($result1, $result2);
  }

  // 게시글 등록
  function insert_writing($imgPath){
    global $mysqli;
    setcookie('galleryInsertTitle');
    setcookie('galleryInsertContent');
    $title = $_POST['title'];
    $content = $_POST['content'];
    $id = $_SESSION['id'];

    // 배열 순서 정렬 (DB imgPath 컬럼에 이미지 경로를 순서대로 삽입)
    if(empty($imgPath[0]) && !empty($imgPath[1])){
      $imgPath[0] = $imgPath[1];
      $imgPath[1] = '';
    }
    else if(empty($imgPath[0]) && !empty($imgPath[2])) {
      $imgPath[0] = $imgPath[2];
      $imgPath[2] = '';
    }
    if(empty($imgPath[1]) && !empty($imgPath[2])){
      $imgPath[1] = $imgPath[2];
      $imgPath[2] = '';
    }

    $sql = "INSERT INTO gallery(title, content, insertDate, imgPath1, imgPath2, imgPath3, id)
            VALUES ('$title', '$content', now(), '$imgPath[0]', '$imgPath[1]', '$imgPath[2]', '$id')";
    if($mysqli->query($sql)){
      echo "<script>
              location.replace('galleryList.php?page=1');
            </script>";
    }
    else{
      echo "<script>
              location.replace('createGallery.php');
            </script>";
    }
    mysqli_close($mysqli);
  }

  // 서버에 이미지 업로드
  function image_upload(){
    $cur = $_POST['cur'];   // 수정페이지에서 이 펑션으로 왔는지 확인, 수정페이지에서 오더라도 새로운 파일을 선택해서 오면 빈값을 들고옴
    // index값들은 미리보기에서 선택한 이미지를 삭제했다는 표시 (index0은 등록이나 수정페이지에서 0번째 이미지파일을 close했다는 뜻)
    $index0 = $_POST['index0'];
    $index1 = $_POST['index1'];
    $index2 = $_POST['index2'];

    if($cur==1){    // 수정페이지에서 이 펑션으로 왔다면
      $imgPath1 = $_POST['priviewImgPath1'];
      $imgPath2 = $_POST['priviewImgPath2'];
      $imgPath3 = $_POST['priviewImgPath3'];
      // close한 이미지를 서버에서 삭제
      if(isset($index0)){
        if(is_file($imgPath1)){
          unlink($imgPath1);
          $imgPath1 = '';
        }
      }
      if(isset($index1)){
        if(is_file($imgPath2)){
          unlink($imgPath2);
          $imgPath2 = '';
        }
      }
      if(isset($index2)){
        if(is_file($imgPath3)){
          unlink($imgPath3);
          $imgPath3 = '';
        }
      }
      update_writing($imgPath1, $imgPath2, $imgPath3);
      return;
    }


    $uploads_dir = './uploads';
    $allowed_ext = array('JPG','JPEG','PNG','GIF');
    $imgPath = array();
    $existFile = false;

    // 다중 업로드
    foreach ($_FILES["imageFile"]["error"] as $key => $error) {
      // 등록페이지에서 이미지 미리보기에서 close한 이미지는 업로드하지 않고 패스한다.
      if(isset($index0) && $key == $index0) continue;
      else if(isset($index1) && $key == $index1) continue;
      else if(isset($index2) && $key == $index2) continue;

      if($error != UPLOAD_ERR_OK) {
        switch($error) {
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
            echo "<script>alert('5MB 이하 이미지를 업로드 하세요.');</script>";
            echo "<script>history.back();</script>";
            exit;
          case UPLOAD_ERR_NO_FILE:
          // echo "<script>alert('노파일!.');</script>";
          break;
            // if($key == 0){
            //   echo "<script>alert('1개 이상 이미지 파일을 업로드 하세요');</script>";
            //   echo "<script>history.back();</script>";
            //   exit;
            // }
            // else break;
          default:
            echo "<script>alert('이미지 파일을 업로드 하세요.');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        // echo "<script>history.back();</script>";
      }
      else{
        $existFile = true;
        $tmp_name = $_FILES["imageFile"]["tmp_name"][$key];
        $name = $_FILES["imageFile"]["name"][$key];
        $ext = array_pop(explode('.', $name));
        if( !in_array($ext, $allowed_ext) ) {
          echo "<script>alert('업로드 실패: gif, jpg, jpeg, png 확장자만 사용하세요.');</script>";
          echo "<script>history.back();</script>";
          exit;
        }
        move_uploaded_file($tmp_name, "$uploads_dir/".date('YmdHis')."$name");
        $imgPath[$key] = $uploads_dir."/".date('YmdHis').$name;
      }
    }
    if (!$existFile) {
      echo "<script>alert('1개 이상 이미지 파일을 업로드 하세요.');</script>";
      echo "<script>history.back();</script>";
      exit;
    }
    else if($cur == 2) {
      $imgPath1 = $_POST['priviewImgPath1'];
      $imgPath2 = $_POST['priviewImgPath2'];
      $imgPath3 = $_POST['priviewImgPath3'];
      // 이미 서버에 있는 이미지를 삭제함 (모든 이미지가 새로운 이미지로 업로드 되기때문)
      if(is_file($imgPath1)) unlink($imgPath1);
      if(is_file($imgPath2)) unlink($imgPath2);
      if(is_file($imgPath3)) unlink($imgPath3);

      update_writing($imgPath[0], $imgPath[1], $imgPath[2]);
    }
    else insert_writing($imgPath);
  }

?>
