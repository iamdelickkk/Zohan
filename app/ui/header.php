<div class="navbar">
    <div class="navbar-container">
        <div class="navbar-element navbar-logo">
            <div id="logotype"></div>
        </div>
        <div>
            <form action="/search" method="get">
                <input type="search" name="q" placeholder="Поиск постов, сообществ, людей..." class="input-search" <?php if(isset($query)){ echo 'value="'.$query.'"'; } ?> required>
            </form>
        </div>
        <div class="navbar-element navbar-element-profile global--link" data-link="/profile/<?php echo $userInfo->username; ?>">
            <img src="<?php echo $userInfo->profileImage; ?>" class="navbar-element-profile-image" alt="Profile Picture">
            <div id="username_navbar">
                <?php echo $userInfo->username; ?>
            </div>
        </div>
    </div>
</div>
<?php
if(!empty($userInfo->backgroundImage)){
?>
<style type="text/css">
    body{background: url('../<?php echo $userInfo->backgroundImage ?>') center/cover fixed;}
</style>
<?php } ?>
<?php
if(!empty($userInfo->colorAccent)){
?>
<style type="text/css">
    .popup_header{
        background-color:<?php echo $userInfo->colorAccent ?>!important;
    }
    #welcome_title{
        color:<?php echo $userInfo->colorAccent ?>!important;
    }
    .button{
        background-color:<?php echo $userInfo->colorAccent ?>!important;
    }
    a, a:hover{
        color:<?php echo $userInfo->colorAccent ?>!important;
    }
    .colorpri{
        color:<?php echo $userInfo->colorAccent ?>!important;
    }
    .tabs .tab:hover {
      color: <?php echo $userInfo->colorAccent ?>!important;
      box-shadow: 0 2px 0 0 <?php echo $userInfo->colorAccent ?>!important;
    }
    .tabs .tab-active {
      color: <?php echo $userInfo->colorAccent ?>!important;
      box-shadow: 0 2px 0 0 <?php echo $userInfo->colorAccent ?>!important;
    }
    .post_action:hover {
      background-color: <?php echo $userInfo->colorAccent ?>23!important;
      color: <?php echo $userInfo->colorAccent ?>!important;
      transition: all .2s;
    }
    .post_action_active {
      background-color: <?php echo $userInfo->colorAccent ?>23!important;
      color: <?php echo $userInfo->colorAccent ?>!important;
    }
    .dropdown .dropdown_el{
        color:<?php echo $userInfo->colorAccent ?>!important;
    }
    .dropdown .dropdown_el:hover{
        background-color: <?php echo $userInfo->colorAccent ?>!important;color:#fff!important;
    }
</style>
<?php } ?>
<?php
if(isset($profileData)){
    if(!empty($profileData->backgroundImage)){
?>
<style type="text/css">
    body{background: url('/<?php echo $profileData->backgroundImage ?>') center/cover fixed!important;}
</style>
<?php
    }else{
?>
<style type="text/css">
    body{background: url('../img/background.jpeg') center/cover fixed!important;}
</style>
<?php
    }
?>
<?php
    if(!empty($profileData->colorAccent)){
    ?>
    <style type="text/css">
        .dropdown .dropdown_el{
            color:<?php echo $profileData->colorAccent ?>!important;
        }
        .dropdown .dropdown_el:hover{
            background-color: <?php echo $profileData->colorAccent ?>!important;
            color:#fff!important;
        }
        .popup_header{
            background-color:<?php echo $profileData->colorAccent ?>!important;
        }
        #welcome_title{
            color:<?php echo $profileData->colorAccent ?>!important;
        }
        .button{
            background-color:<?php echo $profileData->colorAccent ?>!important;
        }
        a, a:hover{
            color:<?php echo $profileData->colorAccent ?>!important;
        }
        .colorpri{
            color:<?php echo $profileData->colorAccent ?>!important;
        }
        .tabs .tab:hover {
          color: <?php echo $profileData->colorAccent ?>!important;
          box-shadow: 0 2px 0 0 <?php echo $profileData->colorAccent ?>!important;
        }
        .tabs .tab-active {
          color: <?php echo $profileData->colorAccent ?>!important;
          box-shadow: 0 2px 0 0 <?php echo $profileData->colorAccent ?>!important;
        }
        .post_action:hover {
          background-color: <?php echo $profileData->colorAccent ?>23!important;
          color: <?php echo $profileData->colorAccent ?>!important;
          transition: all .2s;
        }
        .post_action_active {
          background-color: <?php echo $profileData->colorAccent ?>23!important;
          color: <?php echo $profileData->colorAccent ?>!important;
        }
    </style>
    <?php }else{ ?>
    <style type="text/css">
        .dropdown .dropdown_el{
            color:#00b88d!important;
        }
        .dropdown .dropdown_el:hover{
            background-color: #00b88d!important;
            color:#fff!important;
        }
        .popup_header{
            background-color:#00b88d!important;
        }
        #welcome_title{
            color:#00b88d!important;
        }
        .button{
            background-color:#00b88d!important;
        }
        a, a:hover{
            color:#00b88d!important;
        }
        .colorpri{
            color:#00b88d!important;
        }
        .tabs .tab:hover {
          color: #00b88d!important;
          box-shadow: 0 2px 0 0 #00b88d!important;
        }
        .tabs .tab-active {
          color: #00b88d!important;
          box-shadow: 0 2px 0 0 #00b88d!important;
        }
        .post_action:hover {
          background-color: #00b88d23!important;
          color: #00b88d!important;
          transition: all .2s;
        }
        .post_action_active {
          background-color: #00b88d23!important;
          color: #00b88d!important;
        }
    </style>
    <?php } ?>
<?php
}
?>