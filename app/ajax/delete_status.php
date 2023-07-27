<?php
$ForLoggedIn = true;
include '../init.php';
if(isset($_POST['delete'])){
	$statusIdent = $_POST['delete'];
	$Status->RemoveStatus($statusIdent, $userInfo->username);
}
?>