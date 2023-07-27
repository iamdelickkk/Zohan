<?php
$ForLoggedIn = true;
include '../init.php';
if(isset($_POST['like'])){
	$statusIdent = $_POST['like'];
	$Status->LikeStatus($statusIdent, $userInfo->username);
}
?>