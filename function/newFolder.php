<?php
  // 새폴더 생성 (Ajax)
  $dir = '../file'.$_POST['currentDir'].'/'.$_POST['folderName'];
  mkdir($dir, 0777, true);

  copy("../file/index.php", $dir."/index.php");
