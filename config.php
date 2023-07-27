<?php
$ForLoggedIn = true;
include 'app/init.php';
if(isset($_GET['community']) && !empty($_GET['community'])){
    $communityIdent = $_GET['community'];
    if($Community->checkCommunity($communityIdent) === true){
        $community = $Community->getCommunityData($communityIdent);
        if($community->communityBy != $userInfo->username){
            header("Location: /");
            die();
        }
    }else{
        header("Location: /");
        die();
    }
}else{
    header("Location: /");
    die();
}
if(isset($_FILES['pfp'])){
    if($_FILES['pfp']['size'] > 5242880){
        $error = 'Выберите фотографию меньшего размера.';
    }else if(!exif_imagetype($_FILES['pfp']['tmp_name'])){
        $error = 'Выбранный файл не является фотографией.';
    }else{
        $image_extension = image_type_to_extension(exif_imagetype($_FILES['pfp']['tmp_name']), true);
        $image_name = bin2hex(random_bytes(72)).$image_extension;
        move_uploaded_file($_FILES['pfp']['tmp_name'], __DIR__."/uploads/".$image_name);
        $imagePath = "uploads/".$image_name;
        $image_name = bin2hex(random_bytes(72)).'.jpeg';
        $Image->convertImage($imagePath, $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$image_name, 100);
        unlink($_SERVER['DOCUMENT_ROOT'].'/'.$imagePath);
        $stmt = $pdo->prepare("UPDATE communities SET communityImage = :img WHERE communityIdent = :ident");
        $stmt->bindValue(":img", 'uploads/'.$image_name);
        $stmt->bindValue(":ident", $community->communityIdent);
        $stmt->execute();
        header("Location: ".$_SERVER['REQUEST_URI']);
    }
}
if(isset($_FILES['cover'])){
    if($_FILES['cover']['size'] > 5242880){
        $error = 'Выберите фотографию меньшего размера.';
    }else if(!exif_imagetype($_FILES['cover']['tmp_name'])){
        $error = 'Выбранный файл не является фотографией.';
    }else{
        $image_extension = image_type_to_extension(exif_imagetype($_FILES['cover']['tmp_name']), true);
        $image_name = bin2hex(random_bytes(72)).$image_extension;
        move_uploaded_file($_FILES['cover']['tmp_name'], __DIR__."/uploads/".$image_name);
        $imagePath = "uploads/".$image_name;
        $image_name = bin2hex(random_bytes(72)).'.jpeg';
        $Image->convertImage($imagePath, $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$image_name, 100);
        unlink($_SERVER['DOCUMENT_ROOT'].'/'.$imagePath);
        $stmt = $pdo->prepare("UPDATE communities SET communityCover = :img WHERE communityIdent = :ident");
        $stmt->bindValue(":img", 'uploads/'.$image_name);
        $stmt->bindValue(":ident", $community->communityIdent);
        $stmt->execute();
        header("Location: ".$_SERVER['REQUEST_URI']);
    }
}
if(isset($_GET['action']) && isset($_GET['community'])){
    if($_GET['action'] == 'restore_cover'){
        $stmt = $pdo->prepare("UPDATE communities SET communityCover = '' WHERE communityIdent = :ident");
        $stmt->bindValue(":ident", $community->communityIdent);
        $stmt->execute();
        header("Location: /config?community=".$communityIdent);
    }
    if($_GET['action'] == 'remove'){
        $stmt = $pdo->prepare("DELETE FROM communities WHERE communityIdent = :ident");
        $stmt->bindValue(":ident", $community->communityIdent);
        $stmt->execute();
        $stmtt = $pdo->prepare("DELETE FROM statuses WHERE statusByCommunity = :ident");
        $stmtt->bindValue(":ident", $community->communityIdent);
        $stmtt->execute();
        $stmttt = $pdo->prepare("DELETE FROM followers WHERE communityFollow = :ident");
        $stmttt->bindValue(":ident", $community->communityIdent);
        $stmttt->execute();
        header("Location: /communities");
    }
}
if(isset($_POST['save'])){
    $name = $Text->convertText($_POST['name']);
    if(!empty($name) && !ctype_space($name)){
        if(strlen($name) > 36){
            $error = 'Длинное название сообщества!';
        }else{
            $stmt = $pdo->prepare("UPDATE communities SET communityName = :name WHERE communityIdent = :ident");
            $stmt->bindValue(":name", $name);
            $stmt->bindValue(":ident", $community->communityIdent);
            $stmt->execute();
            header("Location: ".$_SERVER['REQUEST_URI']);
        }
    }else{
        $error = 'Введите название!';
    }
}
if(isset($_POST['save_desc'])){
    $description = $Text->convertText($_POST['description']);
    if(ctype_space($description)){
        $error = 'Нельзя так делать!';
    }else{
        $stmt = $pdo->prepare("UPDATE communities SET communityDescription = :description WHERE communityIdent = :ident");
        $stmt->bindValue(":description", $description);
        $stmt->bindValue(":ident", $community->communityIdent);
        $stmt->execute();
        header("Location: ".$_SERVER['REQUEST_URI']);
    }
}
if(isset($_POST['save_pn'])){
    if($_POST['publicnews'] == 1 or !isset($_POST['publicnews'])){
        if(!isset($_POST['publicnews'])){
            $pn = 0;
        }else{
            $pn = $_POST['publicnews'];
        }
        $stmt = $pdo->prepare("UPDATE communities SET communityPublicNews = :pn WHERE communityIdent = :ident");
        $stmt->bindValue(":pn", $pn);
        $stmt->bindValue(":ident", $community->communityIdent);
        $stmt->execute();
        header("Location: ".$_SERVER['REQUEST_URI']);
    }else{
        $error = 'Произошла неизвестная ошибка!';
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
            <div class="profile_cover" <?php $Community->communityCover($communityIdent); ?>>
                <form method="post" class="profile_cover_content">
                    <div>
                        <div class="profile_cover_picture">
                            <a href="javascript:void(0);" id="after_community_a">
                                <img src="<?php echo $community->communityImage ?>" id="pfp_a" class="editable" alt="Profile Picture">
                            </a>
                        </div>
                        <div>
                            <input type="text" name="name" style="text-align: center" class="input" placeholder="Название" value="<?php echo $community->communityName ?>">
                        </div>
                        <br>
                        <div id="kOkk00">
                            <a class="button" href="/community?v=<?php echo $communityIdent ?>">
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
                        Обложка сообщества
                    </h1><br>
                    <div>
                        <i class="ion-loading-c" id="loading" style="display:none;"></i>
                        <label class="button">Выбрать файл...<input type="file" onchange="$('.ion-loading-c').show(0);$('label').hide(0);this.form.submit();" name="cover" accept="image/*" hidden=""></label>
                    </div>
                    <div style="margin-top: 20px;">
                        <a href="/config?community=<?php echo $communityIdent ?>&action=restore_cover">Вернуть обложку по умолчанию</a><br><br>
                    </div>
                </form>
                <form method="post" enctype="multipart/form-data">
                    <h1 id="welcome_title">
                        Посты сообщества
                    </h1><br>
                    <div class="post_post">
                        <input type="checkbox" name="publicnews" id="pNnnn" value="1" <?php if($community->communityPublicNews == 1){ echo 'checked'; } ?>>
                        <label for="pNnnn">
                            Посты в сообществе могут писать все люди
                        </label>
                    </div>
                    <div style="margin-top: 20px;">
                        <button class="button flex ai-c" type="submit" name="save_pn">
                                <i class="ion-checkmark"></i>
                                Сохранить
                        </button><br>
                    </div>
                </form>
                <form method="post" enctype="multipart/form-data">
                    <h1 id="welcome_title">
                        Описание сообщества
                    </h1><br>
                    <div class="post_post">
                        <textarea class="input" name="description" placeholder="описание"><?php echo $community->communityDescription; ?></textarea>
                    </div>
                    <div style="margin-top: 20px;">
                        <button class="button flex ai-c" type="submit" name="save_desc">
                                <i class="ion-checkmark"></i>
                                Сохранить
                        </button><br>
                    </div>
                    <div>
                        <span>...вы можете <a href="/config?community=<?php echo $communityIdent ?>&action=remove">удалить сообщество</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>