<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['act']) && $_GET['act'] == 'byebye'){
    unset($_COOKIE['token']);
    setcookie("token", "", time()-7000000);
    header("Location: /");
}
if(isset($_POST['publish'])){
    $statusText = nl2br($Text->convertText($_POST['post']));
    if(!empty($statusText)){
        if(!empty($_FILES['imgFiles']['name'][0])){
            $Status->Publish($userInfo->username, $statusText, false);
        }else{
            $Status->Publish($userInfo->username, $statusText, false);
        }
        header("Location: /news");
    }else{
        if(!empty($_FILES['imgFiles']['name'][0]) && !ctype_space($statusText)){
            $Status->Publish($userInfo->username, '', false);
            header("Location: /news");
        }else{
            $error = 'Введите текст!';
        }
    }
}
if(isset($_GET['from'])){
    if($_GET['from'] == 'report'){
        $error = 'Вы успешно пожаловались на пользователя.<br>Модераторы рассмотрят вашу жалобу.';
    }
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
    <style type="text/css">
        .content{padding:0;}
    </style>
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
            <form method="post" enctype="multipart/form-data" class="post_post mden">
                <div>
                    <textarea name="post" class="input" placeholder="Что случилось с вами?"  maxlength="144"></textarea>
                </div>
                <div id="preview"></div>
                <div class="flex ai-c">
                    
                    <button class="button" type="submit" name="publish">Опубликовать</button>
                </div>
            </form>
            <div class="tabs">
                <div class="tab tab-active" data-show="#people">
                    люди
                </div>
                <div class="tab" data-show="#community">
                    сообщества
                </div>
            </div>

            <div id="people" class="profile_content mden">
                <?php
                $Status->StatusesFollowed($userInfo->username)
                ?>
            </div>
            <div id="community" style="display: none;" class="profile_content mden">
                <?php
                $Status->StatusesFollowedCommunity($userInfo->username)
                ?>
            </div>
        </div>
    </div>
</body>
</html>