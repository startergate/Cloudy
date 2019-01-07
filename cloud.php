<?php
  require './lib/sidUnified.php';
  $SID = new SID('cloudy');
  $SID->loginCheck('./');

  // Select Profile Image
  $profileImg = $SID->profileGet($_SESSION['pid'], '.');
?>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="./css/Cloud.css">
    <title>Cloudy</title>
    <script type="text/javascript">
      var root = '/';
    </script>
  </head>
  <body>
    <div class="bar">
      <h1>
        <a class="Cloudy" href="cloud.php">
          <span class="blind"><img class="indexImage" src="./static/img/common/cloudy_logo.png" alt=""><span class="titleText">Cloudy</span></span>
        </a>
      </h1>
      <li id = "gnb-my-layer"  class="gnb-my-li, profile" style="display: inline-block;">
        <div id = "gnb-my-namebox" class = "gnb-my-namebox">
          <a class="gnb-my" href="javascript:;" onclick="gnbUserLayer.click.Toggle(); return false">
            <img id="gnb-profile-img" src="<?=$profileImg?>" alt="내 프로필 이미지" style="display: line-block;" width="25" height="25">
            <span id="gnb-profile-filter-mask" class="filter-mask" style="display: inline-block;"></span>
            <span id ="gnb-name1" class="gnb-name" style="font-size: 15px; color: white"><?=$_SESSION['nickname']?></span>
            <em class="blind" style="display: none;">내정보 보기</em>
          </a>
        </div>
      </li>
    </div>
    <!-- <div id="gnb-my-lyr" class="gnb-my-lyr">
      <div class="gnb-my-cont">
        <div class="gnb-my-area">
          <span class="gnb-mask"></span>
          <img src="https://ssl.pstatic.net/static/common/myarea/myInfo.gif" alt="프로필 이미지" width="50" height="50">
        </div>
        <div class="gnb-text-area">
          <div>
            <p class="gnb-cont">
              <span>asdfasdf</span>
            </p>
            <a id="gnb-text-logout" class="gnb-log-button">
              <span class="gnb-logout-text">로그아웃</span>
            </a>
          </div>
        </div>
      </div>
    </div> -->
    <div class="barPlaceholder"></div>
    <div class="container">
      <div class="nav whiteBack">
        <!-- 화면 작아졌을때 버튼 나오도록 추가할 것(onedrive.live.com 참고) -->
        <div class="dropdown">
          <p onclick="myFunction()" class="dropbtn" style="font-size: 25px;">사진</p>
          <div id="myDropdown" class="dropdown-content">
            <a href="#" style="font-size: 15px;">모든사진</a>
            <a href="#" style="font-size: 15px;">폴더</a>
          </div>
          <p class="dropbtn" style="font-size: 25px;">문서</p>
        </div>
      </div>
      <div class="optionSelector whiteBack">
        <input type="button" id="uploadBtn" class="cloudyBtn" name="올리기" onclick="uploadPopup()" value="업로드" style="width: 63; height: 30;">
        <input type="button" id="downloadBtn" class="cloudyBtn" name="내리기" value="다운로드" style="width: 79; height: 30;">
        <input type="button" id="newFolderBtn" class="cloudyBtn" name="새폴더" onclick="newFolderPopup()" value="새 폴더" style="width: 66; height: 30;">
        <input type="button" id="deleteBtn" class="cloudyBtn" name="삭제" onclick="deleteSelectedPopup()" value="선택 항목 삭제" style="width: 112; height: 30">
      </div>
      <div class="file">
        <div class="fileV">
          <p>파일<span class="root"></span></p>

        </div>
        <div class="filelist" id="listChanger"></div>
      </div>
      <div class="popup">
        <div class="deletePopup">
            <div class="popupRelative">
                <span class="Xbutton" onclick="closePopup()">X </span>
            </div>
            파일/폴더 (파일명) 을(를) 삭제합니다.
            <div class="popupRelative" style="text-align:right;bottom:0px">
              <button type="button" onclick="button">가즈아아아아아아</button>
            </div>
        </div>
      </div>
    </div>
    <iframe id="downloader" src="" style="display:none; visibility:hidden;"></iframe>
    <script>
      window.onload = function () {
        listSetter(root)
      }

      var listSetter = function (roots) {
        root = roots
        document.getElementsByClassName("root")[0].innerHTML = roots;
        $.ajax({
          url: './function/getFileList.php',
          type: 'POST',
          dataType: 'json',
          data: {folderName: roots},
          success: function (data) {
            var output = '';
            var trigger = 0;
            if (root !== '/') {
              var upperRoot = ''
              var forcontrol = 1;
              var currentRootData = root.split('/')
              console.log(currentRootData)
              console.log(currentRootData.length-trigger)
              if (currentRootData[currentRootData.length-1] === '') {
                forcontrol++;
              }
              for (var i = 0; i < currentRootData.length-forcontrol; i++) {
                upperRoot += currentRootData[i]+'/'
              }
              output += `<div class="fileSelector" id="file0" onclick="listSetter('${upperRoot}')" target="../">
                <div class="fileIcon dir"></div>
                <br>
                <p class="fileName">상위 폴더로</p>
              </div>`
              trigger++;
            }
            var i;
            for (i = trigger; i < data.data.length+trigger; i++) {
              if (data.data[i-trigger].type === 'dir') {
                output += `<div class="fileSelector" id="file${i}" onclick="listSetter('${roots}${data.data[i-trigger].name}/')" target="${data.data[i-trigger].name}">
                  <div class="fileIcon ${data.data[i-trigger].type}"></div>
                  <br>
                  <p class="fileName">${data.data[i-trigger].name}</p>
                </div>`
              }
            }
            var save = i;
            for (; i < data.data.length+save; i++) {
              if (data.data[i-save].type !== 'dir') {
                var onclicker
                switch (data.data[i-save].type) {
                  case 'png':
                  case 'jpg':
                  case 'jpeg':
                    onclicker = `showImg('file${i-save}')`
                    break
                  default:
                    onclicker = `fileSelect('file${i-save}')`
                    break
                }
                output += `<div class="fileSelector" id="file${i-save}" onclick="${onclicker}" target="${data.data[i-save].name}">
                  <div class="fileIcon ${data.data[i-save].type}"></div>
                  <br>
                  <p class="fileName">${data.data[i-save].name}</p>
                </div>`
              }
            }
            document.getElementById("listChanger").innerHTML = output;
          }
        });
      }

      var folderAdder = function(dir, name) {
        $.ajax({
          url: './function/newFolder.php',
          type: 'POST',
          dataType: 'json',
          data: {folderName: dir+name},
          success: function (data) {
            console.log(data);
          }
        })
        .done(function() {
          console.log("success");
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
      }

      var downloadClicked = function() {
        var target = root + document.getElementsByClassName('selectedFile')[0].getAttribute("target");
        $("#downloader").attr("src","./function/download.php"+"?fileName="+target);
      }

      document.getElementById("downloadBtn").addEventListener('click', function() {
        downloadClicked();
      });

      document.getElementById("uploadBtn").addEventListener('click', function() {
        uploadPopup();
      });

      document.getElementById("newFolderBtn").addEventListener('click', function() {
        newFolderPopup();
      });

      document.getElementById("deleteBtn").addEventListener('click', function() {
        deletePopup();
      });

      var pictureDropdownStatement;
      /* When the user clicks on the button,
      toggle between hiding and showing the dropdown content */
      function myFunction() {
        var dd = document.getElementById("myDropdown")
        if (pictureDropdownStatement != "open") {
          dd.classList.toggle("show")
          dd.style.animationName = "dropdownOpen"
          dd.style.animationDuration = "1s"
          pictureDropdownStatement = "open";
        } else {
          dropdownClose(dd)
          pictureDropdownStatement = "close";
        }
      }

      // Close the dropdown menu if the user clicks outside of it
      window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
          var dropdowns = document.getElementsByClassName("dropdown-content");
          var i;
          for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
              dropdownClose(openDropdown)
              pictureDropdownStatement = "close";
            }
          }
        }
      }

      var dropdownClose = function(openDropdown) {
        openDropdown.style.animationName = ""
        openDropdown.style.animationDuration = ""
        setTimeout(() => {
          openDropdown.style.animationName = "dropdownOpen"
          openDropdown.style.animationDuration = "1s"
          openDropdown.style.animationDirection = "reverse"

          setTimeout(() => {
            openDropdown.classList.remove('show');
            openDropdown.style.animationName = ""
            openDropdown.style.animationDuration = ""
            openDropdown.style.animationDirection = ""
          }, 1000);
        }, 10);
      }

      var fileSelect = function(fileName) {
        document.getElementById(fileName).classList.toggle("selectedFile");
      }
    </script>
    <script src="./lib/jquery-3.3.1.min.js"></script>
  </body>
</html>
