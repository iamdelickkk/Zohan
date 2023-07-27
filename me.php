<?php
$ForLoggedIn = true;
include 'app/init.php';
header("Location: /profile/".$userInfo->username);
?>