<?php
$ForLoggedIn = true;
include 'app/init.php';
if(!isset($_GET['limit'])){
    $limit = 16;
}else{
    $limit = $_GET['limit'];
}
if(isset($_POST['create'])){
    $name = $Text->convertText($_POST['title']);
    if(!empty($name) && !ctype_space($name)){
        if(strlen($name) > 36){
            $error = 'Длинное название сообщества!';
        }else{
            $Community->AddCommunity($name, $userInfo->username);
        }
    }else{
        $error = 'Введите название!';
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
    <style type="text/css">
        .content{
            padding:0;
        }
    </style>
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
            <h1 id="welcome_title" class="mden flex ai-c">
                Сообщества
                <button class="button ml-a flex ai-c add_com" type="button">
                    <i class="ion-plus"></i>
                    Добавить
                </button>
            </h1>
            <div class="tabs">
                <div class="tab tab-active" data-show="#all">
                    все сообщества
                </div>
                <div class="tab" data-show="#mine">
                    мои сообщества
                </div>
            </div>
            <div class="profile_content mden" id="all">
                <?php
                $Community->AllCommunities($limit);
                ?>
            </div>
            <div class="profile_content mden" id="mine" style="display:none;">
                <?php
                $Community->MyCommunities($userInfo->username);
                ?>
            </div>
        </div>
    </div>
</body>
</html>