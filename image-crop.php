<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['img']) && isset($_GET['w']) && isset($_GET['h']) && isset($_GET['x']) && isset($_GET['y'])){
  if($userInfo->profileImage != '/img/account.png'){
    if(file_exists($_SERVER['DOCUMENT_ROOT'].$userInfo->profileImage)){
      unlink($_SERVER['DOCUMENT_ROOT'].$userInfo->profileImage);
    }
  }
  $jpgname = md5($_GET['img']).".jpeg";
  $Image->convertImage($_GET['img'], $_SERVER['DOCUMENT_ROOT']."/uploads/".$jpgname, 100);
  $img_r = imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT']."/uploads/".$jpgname);
  $dst_r = ImageCreateTrueColor( $_GET['w'], $_GET['h'] );
  $imgPath = $_SERVER['DOCUMENT_ROOT']."/uploads/cropped/uploads/".$jpgname;
  imagecopyresampled($dst_r, $img_r, 0, 0, $_GET['x'], $_GET['y'], $_GET['w'], $_GET['h'], $_GET['w'],$_GET['h']);

  imagejpeg($dst_r, $imgPath, 100);

  $img = "/uploads/cropped/uploads/".$jpgname;
  $stmt = $pdo->prepare("UPDATE users SET profileImage = :img WHERE token = :token");
  $stmt->bindValue(":img", $img);
  $stmt->bindValue(":token", TOKEN);
  $stmt->execute();
  unlink($_SERVER['DOCUMENT_ROOT'].'/'.$_GET['img']);
  unlink($_SERVER['DOCUMENT_ROOT']."/uploads/".$jpgname);
  header("Location: /personalize");
}else{
  header("Location: /personalize?pfp=true");
}
?>