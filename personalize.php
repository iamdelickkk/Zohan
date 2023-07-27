<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_POST['save'])){
    $name = $Text->convertText($_POST['name']);
    if(!empty($name) && !ctype_space($name)){
        if(strlen($name) < 2 or strlen($name) > 46){
            $error = 'У вас короткое или длинное имя!';
        }else{
            $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE token = :token");
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':token', TOKEN);
            $stmt->execute();
            header("Location: ".$_SERVER['REQUEST_URI']);
        }
    }else{
        $error = 'Введите имя!';
    }
}
if(isset($_FILES['cover'])){
    if($_FILES['cover']['size'] > 5242880){
        $error = 'Выберите фотографию меньшего размера.';
    }else if(!exif_imagetype($_FILES['cover']['tmp_name'])){
        $error = 'Выбранный файл не является фотографией.';
    }else{
        if(!empty($userInfo->coverImage) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$userInfo->coverImage)){
            unlink($_SERVER['DOCUMENT_ROOT'].'/'.$userInfo->coverImage);
        }
        $image_extension = image_type_to_extension(exif_imagetype($_FILES['cover']['tmp_name']), true);
        $image_name = bin2hex(random_bytes(72)).$image_extension;
        move_uploaded_file($_FILES['cover']['tmp_name'], __DIR__."/uploads/".$image_name);
        $imagePath = "uploads/".$image_name;
        $cover_name = bin2hex(random_bytes(72)).'.jpeg';
        $Image->convertImage($imagePath, $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$cover_name, 100);
        unlink($_SERVER['DOCUMENT_ROOT'].'/'.$imagePath);
        $stmt = $pdo->prepare("UPDATE users SET coverImage = :img WHERE token = :token");
        $stmt->bindValue(":img", 'uploads/'.$cover_name);
        $stmt->bindValue(":token", TOKEN);
        $stmt->execute();
        header("Location: ".$_SERVER['REQUEST_URI']);
    }
}
if(isset($_FILES['background'])){
    if($_FILES['background']['size'] > 5242880){
        $error = 'Выберите фотографию меньшего размера.';
    }else if(!exif_imagetype($_FILES['background']['tmp_name'])){
        $error = 'Выбранный файл не является фотографией.';
    }else{
        if(!empty($userInfo->backgroundImage) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$userInfo->backgroundImage)){
            unlink($_SERVER['DOCUMENT_ROOT'].'/'.$userInfo->backgroundImage);
        }
        $image_extension = image_type_to_extension(exif_imagetype($_FILES['background']['tmp_name']), true);
        $image_name = bin2hex(random_bytes(72)).$image_extension;
        move_uploaded_file($_FILES['background']['tmp_name'], __DIR__."/uploads/".$image_name);
        $imagePath = "uploads/".$image_name;
        $background_name = bin2hex(random_bytes(72)).'.jpeg';
        $Image->convertImage($imagePath, $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$background_name, 100);
        unlink($_SERVER['DOCUMENT_ROOT'].'/'.$imagePath);
        $stmt = $pdo->prepare("UPDATE users SET backgroundImage = :img WHERE token = :token");
        $stmt->bindValue(":img", 'uploads/'.$background_name);
        $stmt->bindValue(":token", TOKEN);
        $stmt->execute();
        header("Location: ".$_SERVER['REQUEST_URI']);
    }
}
if(isset($_GET['action']) && $_GET['action'] == 'restore_cover'){
    $stmt = $pdo->prepare("UPDATE users SET coverImage = '' WHERE token = :token");
    $stmt->bindValue(":token", TOKEN);
    $stmt->execute();
    header("Location: /personalize");
}
if(isset($_GET['action']) && $_GET['action'] == 'restore_cover'){
    $stmt = $pdo->prepare("UPDATE users SET coverImage = '' WHERE token = :token");
    $stmt->bindValue(":token", TOKEN);
    $stmt->execute();
    header("Location: /personalize");
}
if(isset($_GET['action']) && $_GET['action'] == 'restore_background'){
    $stmt = $pdo->prepare("UPDATE users SET backgroundImage = '' WHERE token = :token");
    $stmt->bindValue(":token", TOKEN);
    $stmt->execute();
    header("Location: /personalize");
}
if(isset($_GET['action']) && $_GET['action'] == 'restore_photo'){
    $stmt = $pdo->prepare("UPDATE users SET profileImage = '/img/account.png' WHERE token = :token");
    $stmt->bindValue(":token", TOKEN);
    $stmt->execute();
    header("Location: /personalize");
}
if(isset($_GET['action']) && $_GET['action'] == 'restore_accent'){
    $stmt = $pdo->prepare("UPDATE users SET colorAccent = '#00b88d' WHERE token = :token");
    $stmt->bindValue(":token", TOKEN);
    $stmt->execute();
    header("Location: /personalize");
}
if(empty($userInfo->colorAccent)){
    $colorAccent = '#00b88d';
}else{
    $colorAccent = $userInfo->colorAccent;
}
if(isset($_POST['accent_color'])){
    $stmt = $pdo->prepare("UPDATE users SET colorAccent = :color WHERE token = :token");
    $stmt->bindValue(":token", TOKEN);
    $stmt->bindValue(":color", $_POST['color']);
    $stmt->execute();
    header("Location: /personalize");
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
    <script src="js/jquery.imageviewer.min.js"></script>
    <style>
        .content{
            padding: 0 !important;
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
            <div class="profile_cover" <?php $User->profileCover($userInfo->username); ?>>
                <form method="post" class="profile_cover_content">
                    <div>
                        <div class="profile_cover_picture">
                            <a href="javascript:void(0);" id="after_pfp_a">
                                <img src="<?php echo $userInfo->profileImage ?>" id="pfp_a" class="editable" alt="Profile Picture">
                            </a>
                        </div>
                        <div>
                            <input type="text" name="name" style="text-align: center" class="input" placeholder="Имя" value="<?php echo $userInfo->name ?>">
                        </div>
                        <div>
                            <b>
                                <a href="/profile/<?php echo $userInfo->username ?>" class="profile_cover_content_username">
                                    @<?php echo $userInfo->username ?>
                                </a>
                            </b>
                        </div>
                        <br>
                        <div id="kOkk00">
                            <a class="button" href="/me">
                                <i class="ion-ios7-arrow-back"></i>
                                Назад
                            </a>
                            <div id="k000k"></div>
                            <button class="button" type="submit" name="save">
                                <i class="ion-checkmark"></i>
                                Сохранить
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="profile_content">
                <form method="post" enctype="multipart/form-data">
                    <h1 id="welcome_title">
                        Обложка профиля
                    </h1><br>
                    <div>
                        <i class="ion-loading-c" id="loading" style="display:none;"></i>
                        <label class="button">Выбрать файл...<input type="file" onchange="$('.ion-loading-c').show(0);$('label').hide(0);this.form.submit();" name="cover" accept="image/*" hidden=""></label>
                    </div>
                    <div style="margin-top: 20px;">
                        <a href="/personalize?action=restore_cover">Вернуть обложку по умолчанию</a><br><br>
                    </div>
                </form>
                <form method="post" enctype="multipart/form-data">
                    <h1 id="welcome_title">
                        Задний фон профиля
                    </h1><br>
                    <div>
                        <i class="ion-loading-c" id="loading" style="display:none;"></i>
                        <label class="button">Выбрать файл...<input type="file" onchange="$('.ion-loading-c').show(0);$('label').hide(0);this.form.submit();" name="background" accept="image/*" hidden=""></label>
                    </div>
                    <div style="margin-top: 20px;">
                        <a href="/personalize?action=restore_background">Вернуть фон по умолчанию</a><br><br>
                        
                    </div>
                </form>
                <form method="post" enctype="multipart/form-data">
                    <h1 id="welcome_title">
                        Цветовой акцент профиля
                    </h1><br>
                    <div>
                        <input type="color" name="color" class="button" value="<?php echo $colorAccent; ?>"><br><br>
                        <button type="submit" name="accent_color" class="button">Готово</button>
                    </div>

                    <div style="margin-top: 20px;">
                        <a href="/personalize?action=restore_accent">Вернуть акцент по умолчанию</a><br><br>
                        <span>...некоторые настройки есть в <a href="/settings?act=false">этой странице</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        <?php
        if(isset($_GET['pfp']) && $_GET['pfp'] == 'true'){
        ?>
        $('#popups').html(`<div class="popup"><div class="popup_container popup_large"><div class="popup_header">Добавить фотографию профиля<div></div><div class="popup_close" onclick="$('.popup').remove();"><i class="ion-android-close"></i></div></div><form method="post" action="/cropper" enctype="multipart/form-data" class="popup_content">Если добавить свою фотографию профиля, будет ещё лучше!<br><br><i class="ion-loading-c" id="loading" style="display:none;"></i><label class="button">Выбрать файл...<input type="file" onchange="$('#loading').show(0);$('label').hide(0);this.form.submit();" hidden="" name="pfp" accept="image/*"></label><br><br><span id="coOLOGgg">Вы можете загрузить изображение в формате JPG, GIF или PNG.</span><br><a href="/personalize?action=restore_photo">Вернуть фотографию по умолчанию</a></form></div></div>`);
        <?php
        }
        ?>
    </script>
</body>
</html>