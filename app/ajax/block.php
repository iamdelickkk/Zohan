<?php
$ForLoggedIn = true;
include '../init.php';
if(isset($_POST['block'])){
	$block = $_POST['block'];
	if($Follow->checkFollow($block, $userInfo->username) === true){
		$stmt = $pdo->prepare("DELETE FROM followers WHERE followTo = :followTo AND followBy = :followBy");
		$stmt->bindValue(':followTo', $block);
		$stmt->bindValue(':followBy', $userInfo->username);
		$stmt->execute();
	}else if($Follow->checkFollow($userInfo->username, $block) === true){
		$stmt = $pdo->prepare("DELETE FROM followers WHERE followTo = :followTo AND followBy = :followBy");
		$stmt->bindValue(':followTo', $userInfo->username);
		$stmt->bindValue(':followBy', $block);
		$stmt->execute();
	}
	$User->block($block, $userInfo->username);
}
?>