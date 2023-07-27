<?php
$ForLoggedIn = false;
include 'app/init.php';
if(isset($_POST['signin'])){
    $email = $_POST['Semail'];
    $password = $_POST['Spassword'];
    if(!empty($email) && !empty($password)){
        if($User->checkEmail($email) === true){
            $userData = $User->getUserDataEmail($email);
            if(password_verify($password, $userData->password)){
                setcookie('token', $userData->token, time()+7000000);
                header("Location: /home");
                die();
            }else{
                $error = 'Неправильный email или пароль!';
            }
        }else{
            $error = 'Неправильный email или пароль!';
        }
    }else if(ctype_space($email) && ctype_space($password)){
        $error = 'Введите все данные!';
    }else{
        $error = 'Введите все данные!';
    }
}
if(isset($_POST['reg'])){
    $name = $Text->convertText($_POST['name']);
    $username = $Text->convertText($_POST['username']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    if(!empty($name) && !empty($username) && !empty($email) && !empty($password)){
        if(strlen($name) < 2 or strlen($name) > 46){
            $error = 'У вас короткое или длинное имя!';
        }else if(strlen($username) < 4 or strlen($username) > 32){
            $error = 'У вас короткий или длинный никнейм!';
        }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error = 'Неверный тип почты!';
        }else if(preg_match("/[^a-zA-Z0-9\!]/", $username)){
            $error  = "В никнейме допустимо только латинские символы и числа!";
        }else if(strlen($password) < 8){
            $error = 'У вас короткий пароль!';
        }else if($User->checkUsername($username) === true){
            $error = 'Данный никнейм уже используется!';
        }else if($User->checkEmail($email) === true){
            $error = 'Данная почта уже используется!';
        }else{
            $User->register($name, $username, $email, $password);
            
            $userData = $User->getUserData($username);
            setcookie('token', $userData->token, time()+7000000);
            header("Location: /personalize?pfp=true");
            die();
        }
    }else if(ctype_space($name) && ctype_space($username) && ctype_space($email) && ctype_space($password)){
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
    <title>Zohan - share what happend with you.</title>
    <style type="text/css">
        .content{background: #fff!important}
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
    <div class="navbar">
        <div class="navbar-container">
            <div class="navbar-element">
                <div id="logotype"></div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-element" data-link="/">
                <div class="sidebar-icon">
                    <i class="ion-ios7-home-outline"></i>
                </div>
                <div class="sidebar-title">
                    Главная
                </div>
            </div>
            <div class="sidebar-element" id="loginPOPUP" data-link="#">
                <div class="sidebar-icon">
                    <i class="ion-log-in"></i>
                </div>
                <div class="sidebar-title">
                    Войти
                </div>
            </div>
        </div>
        <div class="content">
            <div>
                <center>
                    <div>
                        <h1 id="welcome_title">
                            Добро пожаловать в Zohan.<br>
                            Делитесь что случилось с вами.
                         </h1>
                        <img src="img/welcome.png" alt="добро пожаловать" width="500">
                    </div>
                </center>
                <center>
                    <h1 class="colorpri">
                        Регистрация
                    </h1>
                </center><br>
                <center>
                    <form id="rEEEf" method="post">
                        <div><input type="text" placeholder="имя" name="name" class="input"></div>
                        <div><input type="text" placeholder="никнейм" name="username" class="input"></div> 
                        <div><input type="email" placeholder="email" name="email" class="input"></div>
                        <div><input type="password" placeholder="пароль" name="password" class="input"></div>
                        <div>
                            <button type="submit" name="reg" class="button">Готово</button>
                            <span>или <a href="#" onclick="$('#loginPOPUP').click()">войти в аккаунт</a></span>
                        </div>
                    </form>
                </center>
            </div>
        </div>
    </div>
</body>
</html>