<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['app_id']) && isset($_GET['app_secret'])){
    $check = $pdo->prepare("SELECT * FROM apps WHERE appGetID = :app_id AND appSecretKey = :secret");
    $check->execute([':app_id' => $_GET['app_id'], ':secret' => $_GET['app_secret']]);
    if($check->rowCount() != 0){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $unique = '';
        for ($i = 0; $i < 72; $i++) {
            $unique .= $characters[random_int(0, $charactersLength - 1)];
        }
        $stmt = $pdo->prepare("INSERT INTO oauths(authUnique, authForSecret, authAppID, authFor) VALUES(:unique, :for, :id, :token)");
        $stmt->execute([':unique' => $unique, ':for' => $_GET['app_secret'], ':id' => $_GET['app_id'], ':token' => TOKEN]);
        header("Location: /auth?LoginForApp=".$unique);
        die();
    }else{
        die("Oauth Error! Code - APP_NOT_FOUND");
    }
}else if(isset($_GET['LoginForApp'])){
    $check = $pdo->prepare("SELECT * FROM oauths LEFT JOIN apps ON appSecretKey = authForSecret WHERE authUnique = :LoginForApp AND authFor = :token");
    $check->execute([':LoginForApp' => $_GET['LoginForApp'], ':token' => TOKEN]);
    if($check->rowCount() != 0){
        $data = $check->fetch(PDO::FETCH_OBJ);
        if(isset($_POST['aproove'])){
            
        }
    }else{
        die("Oauth Error! Code - OAUTH_SPECIAL_CODE_NOT_FOUND");
    }
}else{
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
        <?php
        if(isset($error)){
            echo '<div class="popup"><div class="popup_container"><div class="popup_header"><div>Ошибка</div><div class="popup_close" onclick="$(`.popup`).remove()"><i class="ion-android-close"></i></div></div><div class="popup_content flex">'.$error.'</div></div></div>';
        }
        ?> 
    </div>
    <?php include 'app/ui/header.php'; ?>
    <div class="container">
        <?php include 'app/ui/sidebar.php'; ?>
        <div class="content">
            <h1 id="welcome_title">Войти в <? echo $data->appName ?></h1><br>
            <div>
                Вы даете согласие на использование ваших данных аккаунта Zohan приложению <? echo $data->appName ?>?
            </div><br>
            <div class="flex">
                <button class="button flex ai-c" type="submit" name="aproove">
                    <i class="ion-checkmark"></i>
                    Да
                </button>
                <div id="k000k"></div>
                <a class="button flex ai-c" style="color:#fff!important" href="<? echo $data->appRedirectURI ?>">
                    <i class="ion-close"></i>
                    Нет
                </a>
            </div>
        </div>
    </div>
</body>
</html>