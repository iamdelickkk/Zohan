<?php
$ForLoggedIn = true;
include 'app/init.php';
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
    }
}else{
    header("Location: /personalize?pfp=true");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css">
    <link rel="stylesheet" href="сss/jquery.Jcrop.min.css" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/global.js"></script>
    <script src="js/jquery.Jcrop.min.js"></script>
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
            <h1 id="welcome_title">
                Обработка фотографии
            </h1>
            <span>
                Выберите миниатюру вашей фотографии
            </span>
            <br><br>
            <div>
                <img src="<?php echo $imagePath; ?>" id="cropbox" class="img" /><br />
            </div>
            <div id="btn">
                <input type="button" id="crop" class="button" name="done" value="Готово!">
            </div>
            <a id="nocrop" data-filename="<?php echo $image_name; ?>" href="javascript:void(0);">
                Оставить фотографию без миниатюры
            </a>
        </div>
    </div>
    <script type="text/javascript" src="js/crop_img.js"></script>
</body>
</html>