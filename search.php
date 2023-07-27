<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['q']) && !empty($_GET['q'])){
    $query = $Text->convertText($_GET['q']);
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
            <div class="tabs">
                <div class="tab tab-active" data-show="#posts">
                    посты
                </div>
                <div class="tab" data-show="#community">
                    сообщества
                </div>
                <div class="tab" data-show="#people">
                    люди
                </div>
            </div>
            <div id="posts" class="profile_content mden">
                <?php
                $Status->search($query, $userInfo->username)
                ?>
            </div>
            <div id="community" style="display:none;" class="profile_content mden">
                <?php
                $Community->search($query)
                ?>
            </div>
            <div id="people" style="display:none;" class="profile_content mden">
                <?php
                $User->searchUsers($query)
                ?>
            </div>
        </div>
    </div>
</body>
</html>