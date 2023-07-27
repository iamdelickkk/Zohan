<?php
$ForLoggedIn = true;
include '../init.php';
if(isset($_POST['imagename'])){
	$imagename = $_POST['imagename'];
	$imagePath = $_SERVER['DOCUMENT_ROOT']."/uploads/".$imagename;
	if(file_exists($imagePath)){
		$newImagePath = "/uploads/".bin2hex(random_bytes(72)).".jpeg";
		$Image->convertImage($imagePath, $_SERVER['DOCUMENT_ROOT'].$newImagePath, 100);

		$stmt = $pdo->prepare("UPDATE users SET profileImage = :img WHERE token = :token");
	  	$stmt->bindValue(":img", $newImagePath);
	  	$stmt->bindValue(":token", TOKEN);
	  	$stmt->execute();
	}
}
?>