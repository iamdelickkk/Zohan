<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['u']) && !empty($_GET['u'])){
    $username = $_GET['u'];
    if($User->checkUsername($username) === true){
        $profileInfo = $User->getUserData($username);
        if(isset($_POST['report'])){
            $why = $_POST['why'];
            $stmt = $pdo->prepare("INSERT INTO reports(reportReason, reportBy, reportTo) VALUES(:why, :username, :profileUsername)");
            $stmt->bindValue(':why', $why);
            $stmt->bindValue(':username', $userInfo->username);
            $stmt->bindValue(':profileUsername', $username);
            $stmt->execute();
            header("Location: /home?from=report");
        }
        if($username == $userInfo->username){
            header("Location: /");
            die();
        }
        if($Follow->checkFollow($username, $userInfo->username) === true){
            header("Location: /");
            die();
        }
    }else{
        header("Location: /");
        die();
    }
}else{
    header("Location: /");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/global.js"></script>
    <title>Zohan - share what happend with you.</title>
</head>
<body>
    <div id="popups">
        
    </div>
    <?php include 'app/ui/header.php'; ?>
    <div class="container">
        <?php include 'app/ui/sidebar.php'; ?>
        <form method="post" class="content">
            <h1 id="welcome_title">
                Пожаловаться на @<?php echo $username ?>
            </h1>
            <span>
                Пожалуйста, выберите причину жалобы:
            </span>
            <br><br>
            <div>
                <li>
                    <input type="radio" name="why" value="1" checked>
                    Спам
                </li>
                <li>
                    <input type="radio" name="why" value="2">
                    Мошенничество
                </li>
                <li>
                    <input type="radio" name="why" value="3">
                    Оскорбления
                </li>
                <li>
                    <input type="radio" name="why" value="4">
                    18+ контент
                </li>
                <li>
                    <input type="radio" name="why" value="5">
                    Поддельный профиль
                </li>
            </div>
            <button class="button flex ai-c" type="submit" name="report">
                Пожаловаться
            </button>
        </form>
    </div>
</body>
</html>