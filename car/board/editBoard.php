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
      $cookieTitle = $_COOKIE['boardUpdateTitle'];
      $cookieContent = $_COOKIE['boardUpdateContent'];
      $cookieCategory = $_COOKIE['boardUpdateCategory'];

      $category = $_POST['category'];
    ?>
    <!-- 글 수정 -->
    <div class="container">
      <form method="post" action="boardDAO.php" onsubmit="return check_exception();">
        카테고리
        <select id="category" name="category" style="width:110px;">
          <option value="리뷰">리뷰</option>
          <option value="질문">질문</option>
          <option value="자유">자유</option>
        </select> <br>
        제목 <input type="text" id="title" name="title" value="<? echo $_POST['title']; ?>" style="width:900px; height:30px">
        <?
          if(!empty($cookieTitle) || !empty($cookieContent)){
            echo "<a href='javascript:callTempWriting();' id='callWriting'>수정중이던 글 불러오기</a>";
          }
        ?>
        <br/><br/>
        내용 <textarea id="content" name="content" rows="20" cols="155"><? echo $_POST['content']; ?></textarea> <br/>
        <!-- 이미지 첨부 <input type="file"> <br/> -->
        <span style="float:right">
          <input type="submit" value="수정완료">
          <input type="button" onclick="history.back();" value="취소">
        </span>
        <input type="hidden" name="flag" value="2">
        <input type="hidden" name="seq" value="<? echo $_POST['boardSeq']; ?>">
        <input type="hidden" name="page" value="<? echo $_POST['page']; ?>">
        <input type="hidden" name="searchKind" value="<? echo $_POST['searchKind']; ?>">
        <input type="hidden" name="searchTxt" value="<? echo $_POST['searchTxt']; ?>">
        <!-- <input type="hidden" name="category" value="<? echo $_POST['category']; ?>"> -->
      </form>
    </div>

    <script>

      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

      window.onload = function(){
        $('#boardMenu').addClass("active");
        setInterval("temporary_storage()", 30000);    // 30초 마다 게시글 자동저장
        $('#category').val("<? echo $category; ?>");  // 해당 글의 카테고리 selected
      }

      // 로그아웃
      function logout(){
        $(window).unbind('beforeunload');   // 페이지 벗어날때 경고창 띄우지 않기
        var result = confirm('작성중인 글은 저장되지 않고, 목록화면으로 돌아갑니다.');
        if(result){
          $.ajax({
            type: 'post',
            data: {logout:true},
            success: function(data){
              if(data){
                history.back();
                alert('로그아웃 하였습니다.');
              }
            },
            error: function(){
              alert('ajax error');
            }
          });
        }
      }

      // 게시글 수정 유효성 검사
      function check_exception(){
        var title = $('#title').val();
        var content = $('#content').val();
        if(title.replace(blankPattern, "") == ""){
          alert('제목을 입력하세요.');
        }
        else if(content.replace(blankPattern, "") == ""){
          alert('내용을 입력하세요.');
        }
        else{
          return true;
        }
        return false;
      }

      // 작성중인 글(임시저장 글) 불러오기
      function callTempWriting(){
        $('#title').val('<? echo $cookieTitle; ?>');
        $('#content').val('<? echo $cookieContent; ?>');
        $('#category').val('<? echo $cookieCategory; ?>');
        $('#callWriting').hide();
      }

      // 페이지 벗어나기 방지
      $(window).on("beforeunload", function(){
        if(temporary_storage()) return "페이지를 벗어나면 작성중인 글이 등록되지 않습니다.";
      });
      $('form').submit(function(){         // submit은 경고창 띄우지 않기
  	    $(window).unbind('beforeunload');
    	});


      // 수정중인 게시글 임시 저장
      function temporary_storage(){
        var title = $('#title').val();
        var content = $('#content').val();
        var category = $('#category').val();

        if(title.replace(blankPattern, "") != "" || content.replace(blankPattern, "") != ""){
          $.ajax({
            url:'boardDAO.php',
            type:'post',
            data:{flag:8, cur:2, title:title, content:content, category:category},
            success: function(){
              // alert('ajax success');
            },
            error: function(){
              alert('ajax error');
            }
          });
        }
        return true;
      }
    </script>
  </body>
</html>
