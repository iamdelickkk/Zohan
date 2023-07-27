<?php
$ForLoggedIn = true;
include 'app/init.php';
if(!isset($_GET['limit'])){
    $limit = 16;
}else{
    $limit = $_GET['limit'];
}
if(isset($_GET['v']) && !empty($_GET['v'])){
    $communityIdent = $_GET['v'];
    if($Community->checkCommunity($communityIdent) === true){
        $community = $Community->getCommunityData($communityIdent);
    }else{
        header("Location: /");
        die();
    }
}else{
    header("Location: /");
    die();
}
if(isset($_POST['publish'])){
    $statusText = nl2br($Text->convertText($_POST['post']));
    if(!empty($statusText)){
        if(!empty($_FILES['imgFiles']['name'][0])){
            $Community->PublishStatus($userInfo->username, $statusText, $communityIdent, false);
        }else{
            $Community->PublishStatus($userInfo->username, $statusText, $communityIdent, false);
        }
        header("Location: ".$_SERVER['REQUEST_URI']);
    }else{
        if(!empty($_FILES['imgFiles']['name'][0]) && !ctype_space($statusText)){
            $Community->PublishStatus($userInfo->username, '', $communityIdent, false);
            header("Location: ".$_SERVER['REQUEST_URI']);
        }else{
            $error = 'Введите текст!';
        }
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
    <script src="<?php echo BASE_URL; ?>js/imgFiles.js"></script>
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
        <?php
        if(isset($error)){
            echo '<div class="popup"><div class="popup_container"><div class="popup_header"><div>Ошибка</div><div class="popup_close" onclick="$(`.popup`).remove()"><i class="ion-android-close"></i></div></div><div class="popup_content flex">'.$error.'</div></div></div>';
        }
        ?>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/app/ui/header.php'; ?>
    <div class="container">
        <?php include 'app/ui/sidebar.php'; ?>
        <div class="content">
            <div class="profile_cover" <?php $Community->communityCover($communityIdent); ?>>
                <div class="profile_cover_content">
                    <div>
                        <div class="profile_cover_picture">
                            <a href="javascript:void(0);">
                                <img src="<?php echo $community->communityImage ?>" id="pfp_a" alt="Community Picture">
                            </a>
                        </div>
                        <div>
                            <b>
                                <a href="<?php echo BASE_URL ?>community?v=<?php echo $communityIdent ?>" class="profile_cover_content_name">
                                    <?php echo $community->communityName ?>
                                </a>
                            </b>
                        </div>
                        <br>
                        <div id="kOkk00">
                            <?php $Follow->CommunityFollowButton($communityIdent, $userInfo->username, $community->communityBy) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tabs ">
                <div class="tab tab-active" data-show="#posts">
                    посты
                </div>
                <div class="tab" data-show="#fw">
                    подписчики (<?php echo $Follow->CommunityFollowersCount($communityIdent); ?>)
                </div>
                <div class="tab" data-show="#about">
                    о сообществе
                </div>
            </div>
            <div class="profile_content" id="posts">
                <?php
                if($community->communityPublicNews != 1){
                    if($community->communityBy == $userInfo->username){
                        ?>
                        <form method="post" enctype="multipart/form-data" class="post_post">
                            <div>
                                <textarea name="post" class="input" placeholder="Что произошло?" maxlength="144"></textarea>
                            </div>
                            <div id="preview"></div>
                            <div class="flex ai-c">
                                
                                <button class="button" type="submit" name="publish">Опубликовать</button>
                            </div>
                        </form>
                        <?php
                    }
                }else{
                ?>
                 <form method="post" enctype="multipart/form-data" class="post_post">
                    <div>
                        <textarea name="post" class="input" placeholder="Что произошло?" maxlength="144"></textarea>
                    </div>
                    <div id="preview"></div>
                    <div class="flex ai-c">
                        
                        <button class="button" type="submit" name="publish">Опубликовать</button>
                    </div>
                </form>
            <?php } ?>
            <?php
            $Status->StatusesByCommunity($userInfo->username, $communityIdent, $limit);
            ?>
            </div>
            <div class="profile_content" id="fw" style="display:none;">
                <?php
                echo $Follow->FollowersCommunity($communityIdent);
                ?>
            </div>
            <div class="profile_content" id="about" style="display:none;">
                <h1 id="welcome_title">
                    О сообществе
                </h1><br>
                <?php
                if(!empty($community->communityDescription)){
                    echo nl2br($community->communityDescription);
                }else{
                    echo '<i id="coOLOGgg">У сообщества '.$community->communityName.' нет описания</i>';
                }
                ?>
            </div>
            <div class="profile_content" id="posts">
            </div>
        </div>
    </div>
</body>
</html>