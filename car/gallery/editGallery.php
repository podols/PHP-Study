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
      $cookieTitle = $_COOKIE['galleryUpdateTitle'];
      $cookieContent = $_COOKIE['galleryUpdateContent'];
    ?>
    <!-- 글 수정 -->
    <div class="container">
      <form method="post" action="galleryDAO.php" onsubmit="return check_exception();" enctype="multipart/form-data">
        제목 <input type="text" id="title" name="title" value="<? echo $_POST['title']; ?>" style="width:900px; height:30px">
        <?
          if(!empty($cookieTitle) || !empty($cookieContent)){
            echo "<a href='javascript:callTempWriting();' id='callWriting'>수정중이던 글 불러오기</a>";
          }
        ?>
        <br/><br/>
        내용 <textarea id="content" name="content" rows="10" cols="155"><? echo $_POST['content']; ?></textarea> <br/><br/><br/>

        <!-- 이미지 미리보기 -->
        <div id="imgPreviewBox" class="container" style="float:right; width:850px; margin-bottom:30px; border:1px solid">
          <?
            $path1 = $_POST['imgPath1'];
            $path2 = $_POST['imgPath2'];
            $path3 = $_POST['imgPath3'];
            if(!empty($path1)){
              echo "<img id='loadImg1' src='$path1' style='max-width:100%; height:auto;'> <br>
                    <input type='button' id='close1' onclick='close_img(1)' value='close' style='visibility:hidden'> <br>";
            }
            if(!empty($path2)){
              echo "<img id='loadImg2' src='$path2' style='max-width:100%; height:auto;'> <br>
                    <input type='button' id='close2' onclick='close_img(2)' value='close' style='visibility:hidden'> <br>";
            }
            if(!empty($path3)){
              echo "<img id='loadImg3' src='$path3' style='max-width:100%; height:auto;'> <br>
                    <input type='button' id='close3' onclick='close_img(3)' value='close' style='visibility:hidden'> <br>";
            }
          ?>
        </div>

        <!-- 이미지 첨부 -->
        이미지 첨부
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">  <!-- MAX_FILE_SIZE 값은 file 요소 바로 앞에 와야 함-->
        <input type="file" id="imgFile" name="imageFile[]" accept="image/*" style="display:none;" multiple>
        <input type="button" id="selectImg" value="내PC"><br><br><br><br>
        <!-- 수정완료 버튼 -->
        <input type="submit" name="submit" value="수정완료">
        <input type="button" value="취소" onclick="history.back();">
        <!-- 히든 값 -->
        <input type="hidden" name="flag" value="1">
        <input type="hidden" name="seq" value="<? echo $_POST['gallerySeq']; ?>">
        <input type="hidden" name="page" value="<? echo $_POST['page']; ?>">
        <input type="hidden" name="searchKind" value="<? echo $_POST['searchKind']; ?>">
        <input type="hidden" name="searchTxt" value="<? echo $_POST['searchTxt']; ?>">
        <input type="hidden" id="cur" name="cur" value="1">  <!-- 수정페이지에서 이미지 업로드 펑션으로 왔다는 것을 구분해주기 위함  -->
        <input type="hidden" id="priviewImgPath1" name="priviewImgPath1" value="<? echo $_POST['imgPath1']; ?>">
        <input type="hidden" id="priviewImgPath2" name="priviewImgPath2" value="<? echo $_POST['imgPath2']; ?>">
        <input type="hidden" id="priviewImgPath3" name="priviewImgPath3" value="<? echo $_POST['imgPath3']; ?>">
      </form>
    </div>

    <script>

      var blankPattern = /^\s+|\s+$/g;   // 공백 문자

      // 페이지 온로드
      window.onload = function(){
        $('#galleryMenu').addClass("active");

        // 파일업로드 에러 시 history.back() 하기에 등록한 파일을 초기화시킴
        $('#imgFile').val('');

        // 이미지 src값 읽어서 있으면, close버튼도 띄우기
        var loadImg1 = $('#loadImg1').attr('src');
        var loadImg2 = $('#loadImg2').attr('src');
        var loadImg3 = $('#loadImg3').attr('src');
        $('#close1').css('visibility', 'visible');
        if(loadImg2) $('#close2').css('visibility', 'visible');
        if(loadImg3) $('#close3').css('visibility', 'visible');

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
        $('#cur').val('2');    // 파일을 선택해버리면 기존의 파일들은 삭제되고 새로운 파일을 업로드해야하므로 cur의 값을 없애버림 (dao에서 값구분)
      }

      // 이미지 미리보기 닫기
      function close_img(flag){
        if(flag==1) {
          $('#loadImg1').remove();
          $('#close1').remove();
          // 삭제한 이미지 경로는 dao에서 받는 이미지 경로 배열의 인덱스의 값을 업로드 하지 않는다.
          var closeIndex = "<input type='hidden' id='index0' name='index0' value='0'><br>";
          $('#imgPreviewBox').append(closeIndex);
        }
        else if(flag==2) {
          var imgPath = $('#loadImg1').attr('src');

          $('#loadImg2').remove();
          $('#close2').remove();

          var closeIndex = "<input type='hidden' id='index1' name='index1' value='1'><br>";
          // var closeImg = "<input type='hidden' name='img1' value='"+imgPath+"'>";
          $('#imgPreviewBox').append(closeIndex);
        }
        else if(flag==3) {
          var imgPath = $('#loadImg1').attr('src');

          $('#loadImg3').remove();
          $('#close3').remove();

          var closeIndex = "<input type='hidden' id='index2' name='index2' value='2'><br>";
          // var closeImg = "<input type='hidden' name='img2' value='"+imgPath+"'>";
          $('#imgPreviewBox').append(closeIndex);
        }
      }



      // 게시글 수정 유효성 검사
      function check_exception(){
        var title = $('#title').val();
        var content = $('#content').val();
        // 미리보기에서 이미지가 존재하는지 존재하면 0이 아닌값이 할당도미
        var loadImg1 = $('#loadImg1').length;
        var loadImg2 = $('#loadImg2').length;
        var loadImg3 = $('#loadImg3').length;


        if(title.replace(blankPattern, "") == ""){
          alert('제목을 입력하세요.');
        }
        else if(content.replace(blankPattern, "") == ""){
          alert('내용을 입력하세요.');
        }
        else if(!loadImg1 && !loadImg2 && !loadImg3){
          alert('이미지를 1개 이상 추가하세요.');
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


      // 수정중인 게시글 임시 저장
      function temporary_storage(){
        var title = $('#title').val();
        var content = $('#content').val();

        if(title.replace(blankPattern, "") != "" || content.replace(blankPattern, "") != ""){
          $.ajax({
            url:'galleryDAO.php',
            type:'post',
            data:{flag:7, cur:2, title:title, content:content},
            success: function(){
              // alert('ajax success');
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
