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
  </head>
  <body>
    <?
      include('../menu/topMenu.php');
      $cookieTitle = $_COOKIE['galleryInsertTitle'];
      $cookieContent = $_COOKIE['galleryInsertContent'];
    ?>

    <!-- 글 작성 -->
    <div class="container">
      <form method="post" action="galleryDAO.php" enctype="multipart/form-data" onsubmit="return check_exception();">
        제목 <input type="text" id="title" name="title" style="width:900px; height:30px">
        <?
          if(!empty($cookieTitle) || !empty($cookieContent)){
            echo "<a href='javascript:callTempWriting();' id='callWriting'>작성중이던 글 불러오기</a>";
          }
        ?>
        <br/><br/>
        내용 <textarea id="content" name="content" rows="10" cols="155"></textarea> <br/><br/><br/>

        <!-- 이미지 미리보기 -->
        <div id="imgPreviewBox" class="container" style="float:right; width:850px; margin-bottom:30px; border:1px solid">
        </div>

        <!-- 이미지 첨부 -->
        이미지 첨부
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">  <!-- MAX_FILE_SIZE 값은 file 요소 바로 앞에 와야 함-->
        <input type="file" id="imgFile" name="imageFile[]" accept="image/*" style="display:none;" multiple>
        <input type="button" id="selectImg" value="내PC"><br><br><br><br>
        <input type="submit" name="submit" value="등록">
        <input type="button" value="취소" onclick="history.back();">
        <input type="hidden" name="flag" value="1">
      </form>
    </div>


    <script>
      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

      // 페이지 온로드
      window.onload = function(){
        $('#galleryMenu').addClass("active");
        // 파일업로드 에러 시 history.back() 하기에 등록한 파일을 초기화시킴
        $('#imgFile').val('');
        setInterval("temporary_storage()", 30000);    // 30초 마다 게시글 자동저장
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

      // 이미지 업로드 시 내PC 버튼 클릭하면 input file을 띄움
      $('#selectImg').click(function (e) {
        e.preventDefault();     // selectImg(내PC) 버튼의 온클릭 이벤트를 막는 함수인듯?
        $('#imgFile').click();
      });
      $('#imgFile').on('change', preview_img);

      // 이미지 선택 시 미리보기
      var sel_files = [];
      function preview_img(e){  // e를 받기 때문에 onchange할 때 this를 보내줘야할듯?
        var files = e.target.files;
        var filesArr = Array.prototype.slice.call(files);
        var filesLength = filesArr.length;
        if(filesLength >= 4) {    // 한번에 4개이상 불가
          alert('이미지는 최대 3개까지 추가가 가능합니다.');
          $('#imgFile').val('');
          return;
        }
        // 선택한 파일들을 배열에서 꺼냄
        filesArr.forEach(function(item, i){
          if(!item.type.match("image.*")){
            alert('이미지 파일만 업로드 가능합니다.');
            return;
          }
          sel_files.push(item);
          var reader = new FileReader();
          reader.onload = function(e){
            // 선택한 파일들을 append 해준다.
            var imgElement = "<img src='" + e.target.result + "' id='loadImg"+(i+1)+"' style='max-width:100%; height:auto;'> <br>";
            var btnElement = "<input type='button' id='close"+(i+1)+"' onclick='close_img("+(i+1)+");' value='close'> <br>";
            $('#imgPreviewBox').append(imgElement+btnElement);
          }
          reader.readAsDataURL(item);
        });
        var imgPreviewBox = $('#imgPreviewBox').append();
        $('#imgPreviewBox').html(imgPreviewBox);
      }

      // 이미지 미리보기 닫기
      function close_img(flag){
        if(flag==1) {
          $('#loadImg1').remove();
          $('#close1').remove();
          // 삭제한 이미지 경로는 dao에서 받는 이미지 경로 배열의 인덱스의 값을 업로드 하지 않는다.
          var closeIndex = "<input type='hidden' name='index0' value='0'>";
          $('#imgPreviewBox').append(closeIndex);
        }
        else if(flag==2) {
          $('#loadImg2').remove();
          $('#close2').remove();

          var closeIndex = "<input type='hidden' name='index1' value='1'>";
          $('#imgPreviewBox').append(closeIndex);
        }
        else if(flag==3) {
          $('#loadImg3').remove();
          $('#close3').remove();

          var closeIndex = "<input type='hidden' name='index2' value='2'>";
          $('#imgPreviewBox').append(closeIndex);
        }
      }

      // 게시글 등록 유효성 검사
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
        $('#title').val('<? echo $cookieTitle ?>');
        $('#content').val('<? echo $cookieContent ?>');
        $('#callWriting').hide();
      }

      // 페이지 벗어나기 방지
      $(window).on("beforeunload", function(){
        if(temporary_storage()) return "페이지를 벗어나면 작성중인 글이 등록되지 않습니다.";
      });
      $('form').submit(function(){         // submit은 경고창 띄우지 않기
  	    $(window).unbind('beforeunload');
    	});


      // 작성중인 게시글 임시 저장
      function temporary_storage(){
        var title = $('#title').val();
        var content = $('#content').val();

        if(title.replace(blankPattern, "") != "" || content.replace(blankPattern, "") != ""){
          $.ajax({
            url:'galleryDAO.php',
            type:'post',
            data:{flag:7, cur:1, title:title, content:content},
            success: function(){
            },
            error: function(){
              // alert('임시저장 실패');
            }
          });
        }
        return true;
      }
    </script>
  </body>
</html>
