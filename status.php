<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['v']) && !empty($_GET['v'])){
    $ident = $_GET['v'];
    if($_SERVER['REQUEST_URI'] == '/status?v='.$ident or $_SERVER['REQUEST_URI'] == '/status.php?v='.$ident){
        header("Location: /");
        die();
    }
    if($Status->checkStatus($ident) === true){
        $status = $Status->GetStatusInfo($ident);
        $username = $userInfo->username;
        if(!isset($_GET['limit'])){
            $limit = 16;
        }else{
            $limit = $_GET['limit'];
        }
        if(isset($_POST['publish'])){
            $commentText = nl2br($Text->convertText($_POST['comment']));
            if(!empty($commentText) && !ctype_space($commentText)){
                $Status->PublishComment($userInfo->username, $commentText, $ident);
                header("Location: ".$_SERVER['REQUEST_URI']);
            }else{
                $error = 'Введите комментарий!';
            }
        }
    }else{
        header("Location: /");
    }
}else{
    header("Location: /");
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
            <?php
            if(isset($status->communityImage)){
                if($status->communityImage != '/img/group.png'){
                    $status->communityImage = '/'.$status->communityImage;
                }
            }
            echo '<div class="post" id="post-'.$status->statusIdent.'">
        <div>
            <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'"><img src="'.(!empty($status->statusByCommunity) ? $status->communityImage : $status->profileImage).'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'">
                    '.(!empty($status->statusByCommunity) ? $status->communityName : $status->name).'
                </a>
                '.(!empty($status->statusByCommunity) ? '<span id="coOLOGgg">(@'.$status->username.')</span>' : '').'
            </div>
            <div class="post_text">
                '.$status->statusText.'
            </div>
            <div class="post_actions">
                <div class="post_action like-post '.($Status->checkLike($status->statusIdent, $username) === true ? 'post_action_active' : '').'" data-status="'.$status->statusIdent.'">
                    <i class="ion-thumbsup"></i>
                    <span class="count-likes">'.$Status->countLikes($status->statusIdent).'</span>
                </div>
                '.($status->statusBy == $username && empty($status->statusByCommunity) ? '
                <div class="post_action remove_status" data-status="'.$status->statusIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
            ?>
            <form method="post" class="post_post comment_post">
                <div>
                    <textarea name="comment" class="input" placeholder="Комментарий..." maxlength="128"></textarea>
                </div>
                <div class="flex">
                    <button class="button" type="submit" name="publish">Опубликовать</button>
                </div>
            </form>
            <div>
                <?php
                $Status->StatusComments($ident, $limit, $userInfo->username);
                ?>
            </div>
        </div>
    </div>
</body>
</html>