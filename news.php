<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['act']) && $_GET['act'] == 'byebye'){
    unset($_COOKIE['token']);
    setcookie("token", "", time()-7000000);
    header("Location: /");
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
    <script src="js/imgFiles.js"></script>
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
            <form method="post" enctype="multipart/form-data" action="/home" class="post_post">
                <div>
                    <textarea name="post" class="input" placeholder="Что случилось с вами?"  maxlength="255"></textarea>
                </div>
                <div id="preview"></div>
                <div class="flex ai-c">
                    
                    <button class="button" type="submit" name="publish">Опубликовать</button>
                </div>
            </form>
            <h1 class="colorpri">
                Новости
            </h1><br>
            <div id="posts">
                <?php
                $Status->AllStatuses($userInfo->username)
                ?>
            </div>
        </div>
    </div>
</body>
</html>