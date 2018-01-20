<? session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <title>갤러리</title>
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
    <style>
      #imgList{
        list-style:none;    /* li에서 . 표시 지움 */
        /*border:1px solid red;*/
        padding:0px;
        /*width:100%;*/
      }
      #imgList li{
        width:390px;
        height:250px;
        border:1px solid;
        padding: 0 10px 10px 10px;    /* 세로 0, 가로 10 */
        /*display: inline;*/
      }
      img{
        /*border:1px solid;*/
      }
    </style>
  </head>
  <body>
    <?php
      include('../menu/topMenu.php');
      include('galleryDAO.php');

      $result = select_gallery_list();
    ?>

    <!-- <hr class="container"></hr> -->
    <div class="container" style="margin-bottom:30px; padding:0px;">
      <!-- 이미지 출력 -->
      <ul id="imgList" class="nav-justified">
      <?
      $cnt = 1;
      $i = 1;
      while($row = $result[1]->fetch_array(MYSQLI_ASSOC)) { ?>
        <li>
          <a href="javascript:update_hits(<? echo $row['seq']; ?> , <? echo $_GET['page']; ?>);">
            <img src="<? echo $row['imgPath1']; ?>" style="width:100%; height:100%;">
            <?
            echo $row['title']." [".$row['commentCount']."]<br>";
            echo $row['nickName']." ".$row['insertDate'];
            ?>
          </a>
        </li>
      <?
        if($cnt == 3*$i){
          $i++;
          echo "<br>";
        }
        $cnt++;
      }
      ?>
      </ul>

      <!-- 글쓰기 -->
      <?
        $id = $_SESSION['id'];
        if(!empty($id)){ ?>
          <span style="float:right"><input type="button" onclick="location.href='createGallery.php'" value="글쓰기"></span>
      <? } ?>

      <!-- 페이징 -->
      <nav align="center">
        <ul class="pagination">
          <?
            $rowCount = mysqli_num_rows($result[0]);    // select 한 결과 행의 개수
            $firstPage = $_GET['page'];
            $firstPage = ceil($firstPage/5);
            $firstPage = $firstPage+(4*($firstPage-1));   // 6페이지가 되면 변수 값을 6으로 만들어줌
            $lastPage = ceil($rowCount/12);
            for($i=$firstPage; $i<=$lastPage; $i++){
              // 이전 페이지
              if($firstPage != 1 && $i == $firstPage){
                echo "<li>
                        <a href='javascript:change_page($firstPage-1);' aria-label='Previous'>
                          <span aria-hidden='true'>&laquo;</span>
                        </a>
                      </li>";
              }
              // 다음 페이지
              if($i >= $firstPage+5){
                echo "<li>
                        <a href='javascript:change_page($i);' aria-label='Next'>
                          <span aria-hidden='true'>&raquo;</span>
                        </a>
                      </li>";
                break;
              }
              // 페이지
              echo "<li id='page$i'><a href='javascript:change_page($i);'> $i </a></li>";
          } ?>
        </ul>
      </nav>

      <center>
        <select id="searchKind" style="height:25px">
          <option value="title">제목</option>
          <option value="content">내용</option>
          <option value="nickName">작성자</option>
        </select>
        <input type="text" id="searchTxt" value="<? echo $_GET['searchTxt']; ?>" style="width:300px">
        <input type="button" onclick="search();" value="검색">
      </center>
    </div>

    <script>
      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

      // 페이지 온로드
      window.onload = function(){
        $('#galleryMenu').addClass("active"); // 상단 메뉴 활성화
        <?
          $searchKind = $_GET['searchKind'];
          if(empty($searchKind)) $searchKind = 'title';
        ?>
        $('#searchKind').val('<? echo $searchKind; ?>');      // 검색 후 검색종류(제목,내용,작성자)를 검색할 때 기준으로 설정
        $('#page<? echo $_GET['page']; ?>').addClass("active");   // 이동한 페이지의 번호 버튼에 디자인을 주어 활성화 시킴
      }

      // 조회수 증가 시키고, 갤러리 세부 페이지로 이동 (상세조회)
      function update_hits(seq, page){
        var searchKind = $('#searchKind').val();
        var searchTxt = $('#searchTxt').val();
        $.ajax({
          url: 'galleryDAO.php',
          type: 'post',
          data: {flag:5, seq:seq},
          success: function(){
            if(searchTxt) location.href="detailGallery.php?writingNo="+seq+"&page="+page+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
            else location.href="detailGallery.php?writingNo="+seq+"&page="+page;
          },
          error: function(){
            alert('ajax error');
          }
        });
      }

    // 페이지 이동
      function change_page(page){
        var searchKind = $('#searchKind').val();
        var searchTxt = $('#searchTxt').val();
        if(searchTxt) location.href="galleryList.php?page="+page+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
        else location.href="galleryList.php?page="+page;
      }

    // 검색
      function search(){
        var searchKind = $('#searchKind').val();
        var searchTxt = $('#searchTxt').val();
        if(!searchTxt || searchTxt.replace(blankPattern,'') == '') alert('검색어를 입력하세요');
        else location.href="galleryList.php?page=1&searchKind="+searchKind+"&searchTxt="+searchTxt;
      }
    </script>
  </body>
</html>
