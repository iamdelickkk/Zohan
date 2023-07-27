<?php
include 'db/connection.php';
include 'classes/user.php';
include 'classes/text.php';
include 'classes/follow.php';
include 'classes/status.php';
include 'classes/img.php';
include 'classes/community.php';
$url = 'https://zohan.fun/'; //url сайта (пример - http(s)://example.com/)
define('BASE_URL', $url);
$User = new User($pdo);
$Text = new Text();
$Image = new Image();
$Follow = new Follow($pdo);
$Status = new Status($pdo);
$Community = new Community($pdo);
if(isset($_COOKIE['token'])){
	define('TOKEN', $_COOKIE['token']);
	$userInfo = $User->getUserDataToken(TOKEN);
	if($ForLoggedIn === false){
		header("Location: /home");
		die();
	}
	if($User->checkToken(TOKEN) === false){
		unset($_COOKIE['token']);
	    setcookie("token", "", time()-7000000);
	    header("Location: /");
	    die();
	}
}else{
	if($ForLoggedIn === true){
		header("Location: /");
		die();
	}
}
?>