<?php
$ForLoggedIn = true;
include '../init.php';
if(isset($_POST['followTo'])){
	$followTo = $_POST['followTo'];
	if($followTo != $userInfo->username){$Follow->FollowUser($followTo, $userInfo->username);}
}
?>