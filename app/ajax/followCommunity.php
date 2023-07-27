<?php
$ForLoggedIn = true;
include '../init.php';
if(isset($_POST['followTo'])){
	$followTo = $_POST['followTo'];
	$community = $Community->getCommunityData($communityIdent);
	if($Community->checkCommunity($followTo) === true && $community->communityBy != $userInfo->username){
		$Follow->FollowCommunity($followTo, $userInfo->username);
	}
}
?>