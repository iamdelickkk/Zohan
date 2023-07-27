<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_POST['done'])){
    $email = $Text->convertText($_POST['email']);
    if(!empty($email)){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error = 'Неверный тип почты!';
        }else{
            $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE token = :token");
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':token', TOKEN);
            $stmt->execute();
            header("Location: ".$_SERVER['REQUEST_URI']);
        }
    }else if(ctype_space($email)){
        $error = 'Введите все данные!';
    }else{
        $error = 'Введите все данные!';
    }
}
if(isset($_POST['chngpwd'])){
    $oldPassword = $_POST['oldpwd'];
    $newPassword = $_POST['newpwd'];
    $repPassword = $_POST['reppwd'];
    if(!empty($oldPassword) && !empty($newPassword) && !empty($repPassword)){
        if($newPassword != $repPassword){
            $error = 'Пароли отличаются';
        }else{
            if(password_verify($oldPassword, $userInfo->password)){
                if(strlen($newPassword) < 8){
                    $error = 'У вас короткий пароль!';
                }else{
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $charactersLength = strlen($characters);
                    $token = '';
                    for ($i = 0; $i < 255; $i++) {
                        $token .= $characters[random_int(0, $charactersLength - 1)];
                    }
                    $passwordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = :password, token = :token WHERE username = :username");
                    $stmt->bindValue(':password', $passwordHashed);
                    $stmt->bindValue(':token', $token);
                    $stmt->bindValue(':username', $userInfo->username);
                    $stmt->execute();
                    header("Location: ".$_SERVER['REQUEST_URI']);
                }
            }else{
                $error = 'Старый пароль неверен!';
            }
        }
    }else if(ctype_space($oldPassword) && ctype_space($newPassword) && ctype_space($repPassword)){
        $error = 'Введите все данные!';
    }else{
        $error = 'Введите все данные!';
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
        <form method="post" class="content">
            <h1 id="welcome_title" class="mden">
                Настройки
            </h1>
            <div class="tabs">
                <div class="tab tab-active" data-show="#account">
                    аккаунт
                </div>
                <div class="tab" data-show="#password">
                    пароль
                </div>
                <div class="tab" data-show="#blocklist">
                    чёрный список
                </div>
                <div class="tab" data-show="#api">
                    токен
                </div>
                <div class="tab global--link" data-show="#account" data-link="/home?act=byebye">
                    выйти
                </div>
            </div>
            <div class="profile_content mden" id="account">
                <div class="label">email</div>
                <div><input type="email" placeholder="email" name="email" class="input" value="<?php echo $userInfo->email ?>"></div>
                <div>
                    <button type="submit" name="done" class="button">Готово</button>
                </div>
            </div>
            <div class="profile_content mden" id="password" style="display:none">
                <div class="label">старый пароль</div>
                <div><input type="password" placeholder="старый пароль" name="oldpwd" class="input"></div>
                <div class="label">новый пароль</div>
                <div><input type="password" placeholder="новый пароль" name="newpwd" class="input"></div>
                <div class="label">повторите пароль</div>
                <div><input type="password" placeholder="повторите пароль" name="reppwd" class="input"></div>
                <div>
                    <button type="submit" name="chngpwd" class="button">Готово</button>
                </div>
            </div>
            <div class="profile_content mden" id="blocklist" style="overflow:hidden;display:none">
                <?php
                $User->blocklist_all($userInfo->username);
                ?>
            </div>
            <div class="profile_content mden" id="api" style="display:none">
                <span class="warn_text">
                    ВНИМАНИЕ!<br>
                    НИКОМУ НЕ ПОКАЗЫВАЙТЕ, НЕ ОТПРАВЛЯЙТЕ ЭТОТ ТОКЕН НИКОМУ, ДАЖЕ АДМИНИСТРАЦИИ ZOHAN!
                </span>
                <button type="button" onclick="$('.token').slideToggle();"class="button flex ai-c">
                    <i class="ion-eye"></i>
                    Показать токен
                </button>
                <div class="token" style="display: none;word-break: break-all;">
                    <?php echo $userInfo->token ?>
                </div>
            </div>
        </form>
    </div>
</body>
</html>