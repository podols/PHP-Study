<? session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <title>게시판</title>

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
      include('boardDAO.php');
      $result = select_writing_list();
      $rowCount = mysqli_num_rows($result[0]);
    ?>

<!-- 게시글 목록 -->
    <div class="container">
      <input type="button" id="categoryAll" value="전체" class="btn btn-default" onclick="change_category('전체');">
      <input type="button" id="categoryReview" value="리뷰" class="btn btn-default" onclick="change_category('리뷰');">
      <input type="button" id="categoryQuestion" value="질문" class="btn btn-default" onclick="change_category('질문');">
      <input type="button" id="categoryFree" value="자유" class="btn btn-default" onclick="change_category('자유');"> <br>
      <input type="hidden" id="category">
      <table class="table table-hover" width="800" align="right">
        <tr>
          <th width="60px"></th>
          <th width="80px">분류</th>
          <th style="text-align:center;">제목</th>
          <th width="130px" style="text-align:center;">작성자</th>
          <th width="160px" style="text-align:center;">작성일</th>
          <th width="60px" style="text-align:center;">조회</th>
          <th width="60px" style="text-align:center;">추천</th>
        </tr>
    <?php
      while($row = $result[1]->fetch_array(MYSQLI_ASSOC)){
    ?>
        <tr onclick="update_hits(<? echo $row['seq'] ?> , <? echo $_GET['page']; ?>);">
            <td><? echo $row['seq']; ?></td>
            <td><? echo $row['category']; ?></td>
            <td><? echo $row['title']." &nbsp;&nbsp;&nbsp;[".$row['commentCount']."]"; ?></td>
            <td style="text-align:center;"><? echo $row['nickName']; ?></td>
            <td style="text-align:center;"><? echo $row['insertDate']; ?></td>
            <td style="text-align:center;"><? echo $row['hits']; ?></td>
            <td style="text-align:center;"><? echo $row['recommend']; ?></td>
        </tr>
    <? } ?>
      </table>
<!-- 글쓰기 버튼 -->
  <?
    $id = $_SESSION['id'];
    if(!empty($id)){ ?>
      <span style="float:right"><input type="button" onclick="writing();" value="글쓰기"></span>
  <? } ?>

<!-- 페이징 -->
      <?
      $lastPage = ceil($rowCount/10);                // 마지막 페이지 번호, ceil은 올림함수
      $firstPage = $_GET['page'];                   // 누른 페이지 번호
      $firstPage = ceil($firstPage/5);              // 1~5 페이지를 누르면 1이 됨
      $firstPage = $firstPage+(4*($firstPage-1));   // 6페이지가 되면 변수 값을 6으로 만들어줌
      ?>
      <nav align="center">
        <ul class="pagination">
          <?
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
            // 페이지 번호
            echo "<li id='page$i'><a href='javascript:change_page($i);'>$i</a></li>";
          }
          ?>
        </ul>
      </nav>
      <!-- 검색 -->
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
        $('#boardMenu').addClass("active"); // 메뉴 활성화
        $('#page<? echo $_GET['page']; ?>').addClass("active");   // 이동한 페이지의 번호 버튼에 디자인을 주어 활성화 시킴
        // 검색 분류 유지
        <?
          $searchKind = $_GET['searchKind'];
          if(empty($searchKind)) $searchKind = 'title';
        ?>
        $('#searchKind').val('<? echo $searchKind; ?>');   // 검색 후 검색종류(제목,내용,작성자)를 검색할 때 기준으로 설정
        // 현재 보여주는 카테고리 활성화
        <?
          $category =  $_GET['category'];
          if($category == '전체') {
            echo "$('#categoryAll').attr('class','btn btn-primary');
                  $('#category').val('전체');";
          }
          else if($category == '리뷰'){
            echo "$('#categoryReview').attr('class','btn btn-primary');
                  $('#category').val('리뷰');";
          }
          else if($category == '질문'){
            echo "$('#categoryQuestion').attr('class','btn btn-primary');
                  $('#category').val('질문');";
          }
          else if($category == '자유'){
            echo "$('#categoryFree').attr('class','btn btn-primary');
                  $('#category').val('자유');";
          }
        ?>
      }

      // 글쓰기 버튼 클릭
      function writing(){
        var category = $('#category').val();
        location.href='createBoard.php?category='+category;
      }

      // 카테고리 전환 (분류에 맞는 데이터 조회)
      function change_category(category){
        location.href = "boardList.php?page=1&category="+category;
      }

      // 검색
      function search(){
        var searchKind = $('#searchKind').val();
        var searchTxt = $('#searchTxt').val();
        var category = $('#category').val();
        if(!searchTxt || searchTxt.replace(blankPattern,'') == '') alert('검색어를 입력하세요');
        else location.href="boardList.php?page=1&category="+category+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
      }

      // 페이지 전환
      function change_page(page){
        var searchKind = $('#searchKind').val();
        var searchTxt = $('#searchTxt').val();
        var category = $('#category').val();

        if(searchTxt) location.href="boardList.php?page="+page+"&category="+category+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
        else location.href='boardList.php?page='+page+'&category='+category;
      }

      // 조회수 증가
      function update_hits(seq, page){
        var searchKind = $('#searchKind').val();
        var searchTxt = $('#searchTxt').val();
        var category = $('#category').val();
        $.ajax({
          url: 'boardDAO.php',
          type: 'post',
          data: {flag:4, seq:seq},
          success: function(){
            if(searchTxt) location.href="detailBoard.php?writingNo="+seq+"&page="+page+"&category="+category+"&searchKind="+searchKind+"&searchTxt="+searchTxt;
            else location.href="detailBoard.php?writingNo="+seq+"&page="+page+"&category="+category;
          },
          error: function(){
            alert('ajax error');
          }
        });
      }
    </script>
  </body>
</html>
