<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['u']) && !empty($_GET['u'])){
    $username = $_GET['u'];
    if($_SERVER['REQUEST_URI'] == '/usr?u='.$username or $_SERVER['REQUEST_URI'] == '/usr.php?u='.$username){
        header("Location: /");
        die();
    }
    if($User->checkUsername($username) === true){
        $profileData = $User->getUserData($username);
        if(!isset($_GET['limit'])){
            $limit = 16;
        }else{
            $limit = $_GET['limit'];
        }
    }else{
        error_reporting(0);
        $profileData->profileImage = 'img/account.png';
        $profileData->name = 'Аккаунт удалён';
    }
}else{
    header("Location: /");
}
if(isset($_POST['bio_save'])){
    $biography = $Text->convertText($_POST['biography']);
    if(!ctype_space($biography)){
        $stmt = $pdo->prepare("UPDATE users SET biography = :biography WHERE token = :token");
        $stmt->bindValue(':token', TOKEN);
        $stmt->bindValue(':biography', $biography);
        $stmt->execute();
        header("Location: ".$_SERVER['REQUEST_URI']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/global.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?php echo BASE_URL; ?>js/global.js"></script>
    <style>
        .content{
            padding: 0 !important;
        }
        .profile_cover .profile_cover_content .button{
            opacity: .5;
        }
        .profile_cover .profile_cover_content .button:hover{
            opacity: 1;
        }
    </style>
    <title>Zohan - share what happend with you.</title>
</head>
<body>
    <div id="popups">
        
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/app/ui/header.php'; ?>
    <div class="container">
        <?php include 'app/ui/sidebar.php'; ?>
        <div class="content">
            <?php
            if($User->checkBlock($userInfo->username, $username) === true or $User->checkBlock($username, $userInfo->username) === true){
                if($User->whoBlocked($username, $userInfo->username) == $userInfo->username){
                    echo '<center id="coOLOGgg"><i class="ion-ios7-locked-outline blocked-icon"></i><br><br><i>Вы ограничили доступ к '.$profileData->name.'</i><br><br><button class="button" id="block" data-block="'.$username.'"><i class="ion-minus"></i>Разблокировать</button></center><br>';
                }else{
                    echo '<center id="coOLOGgg"><i class="ion-ios7-locked-outline blocked-icon"></i><br><br><i>'.$profileData->name.' ограничил доступ к профилю</i></center><br>';
                }
                die();
            }
            ?>
            <div class="profile_cover" <?php $User->profileCover($username); ?>>
                <div class="profile_cover_content">
                    <div>
                        <div class="profile_cover_picture">
                            <a href="javascript:void(0);">
                                <img src="<?php echo BASE_URL.$profileData->profileImage ?>" id="pfp_a" alt="Profile Picture">
                            </a>
                        </div>
                        <div>
                            <b>
                                <a href="<?php echo BASE_URL ?>profile/<?php echo $username ?>" class="profile_cover_content_name">
                                    <?php echo $profileData->name ?>
                                </a>
                            </b>
                        </div>
                        <div>
                            <b>
                                <a href="<?php echo BASE_URL ?>profile/<?php echo $username ?>" class="profile_cover_content_username">
                                    @<?php echo $username ?>
                                </a>
                            </b>
                        </div>
                        <br>
                        <div id="kOkk00">
                            <?php
                                if($User->checkUsername($username) === true){
                                    $Follow->followButton($username, $userInfo->username);
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if($User->checkUsername($username) === false){
                die();
            }
            ?>
            <div class="tabs ">
                <div class="tab tab-active" data-show="#posts">
                    посты
                </div>
                <div class="tab" data-show="#fw">
                    подписчики (<?php echo $Follow->FollowersCount($username); ?>)
                </div>
                <div class="tab" data-show="#fl">
                    подписан(-a) (<?php echo $Follow->FollowingCount($username); ?>)
                </div>
                <div class="tab" data-show="#about">
                    обо мне
                </div>
                <?php
                if($username != $userInfo->username && $Follow->checkFollow($username, $userInfo->username) === false){
                ?>
                <div class="tab tab_right tab_dropdown dropdown_trig" data-id="#dropdown_profile">
                    <i class="ion-chevron-down"></i>
                </div>
                <div class="dropdown" id="dropdown_profile" style="display: none">
                    <?php
                    if($Follow->checkFollow($username, $userInfo->username) === false){
                    ?>
                    <div class="dropdown_el global--link" data-link="/report?u=<?php echo $username ?>">
                        <i class="ion-flag"></i>
                        Пожаловаться
                    </div>
                    <div class="dropdown_el" id="block" data-block="<?php echo $username ?>">
                        <i class="ion-minus-circled"></i>
                        Заблокировать
                    </div>
                    <?php
                    }
                    ?>
                </div>
            <?php } ?>
            </div>
            <div class="profile_content" id="fw" style="display:none;">
                <?php
                echo $Follow->Followers($username);
                ?>
            </div>
            <div class="profile_content" id="fl" style="display:none;">
                <?php
                echo $Follow->Following($username);
                ?>
            </div>
            <div class="profile_content" id="about" style="display:none;">
                <h1 id="welcome_title">
                    Обо мне
                </h1><br>
                <?php
                if($username == $userInfo->username){
                ?>
                <form method="post" class="post_post">
                    <div>
                        <textarea name="biography" class="input" placeholder="Расскажите о себе" maxlength="128"><?php echo $profileData->biography; ?></textarea>
                    </div>
                    <div class="flex">
                        <button class="button" type="submit" name="bio_save">Опубликовать</button>
                    </div>
                </form>
                <?php
                }else{
                ?>
                <?php
                if(!empty($profileData->biography)){
                    echo nl2br(htmlspecialchars($profileData->biography));
                }else{
                    echo '<i id="coOLOGgg">'.$profileData->name.' пока что не рассказал о себе</i>';
                }
                ?>
                <?php } ?>
            </div>
            <div class="profile_content" id="posts">
                <?php
                $Status->StatusesBy($username, $userInfo->username, $limit);
                ?>
            </div>
        </div>
    </div>
</body>
</html>