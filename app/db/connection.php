<?php
include $_SERVER['DOCUMENT_ROOT'].'/app/db/config.php';
try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", 
                    $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Не удалось подключиться к базе данных.<br>Подробности для разработчика - ". $e->getMessage();
}
?>