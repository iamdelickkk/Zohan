<?php
$ForLoggedIn = true;
include '../init.php';
if(isset($_POST['delete'])){
	$commentIdent = $_POST['delete'];
	$Status->RemoveComment($commentIdent, $userInfo->username);
}
?>