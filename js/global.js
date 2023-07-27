function generateSerial(len) {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 10;
    var randomstring = '';

    for (var x=0;x<string_length;x++) {

        var letterOrNumber = Math.floor(Math.random() * 2);
        if (letterOrNumber == 0) {
            var newNum = Math.floor(Math.random() * 9);
            randomstring += newNum;
        } else {
            var rnum = Math.floor(Math.random() * chars.length);
            randomstring += chars.substring(rnum,rnum+1);
        }

    }
    return randomstring;
}
$(document).ready(() => {
    $('.navbar-logo').click(() => {
        location.href = '/';
    })
    $('#nocrop').click(function(){
        $.post('/app/ajax/nocrop.php', {imagename:$(this).data('filename')}, function(){
            location.href = '/personalize'; 
        })
    })
    $('.profile_mini_content').click(function(){
        location.href = '/profile/' + $(this).data('profile');
    })
    $(".add_com").click(() => {
        $('#popups').html(`<div class="popup"><div class="popup_container"><div class="popup_header">Добавить сообщество<div></div><div class="popup_close" onclick="$('.popup').remove();"><i class="ion-android-close"></i></div></div><form method="post" class="popup_content"><div id="pOOppUUU">Для начала, введите название сообщества:</div><div><input type="text" name="title" placeholder="название сообщества" class="input"></div><div><button class="button flex ai-c" type="submit" name="create"><i class="ion-chevron-right"></i>Далее</button></div></form></div>`);
    })
    $('.person_im_choose').click(function(){
        location.href = '/messages?c=' + $(this).data('profile');
    })
    $(document).on('submit', 'form', function(){
        if($(this).attr('method') != 'get'){
            $(this).after('<div id="loooOAOA" class="width_pe" style="display:none;"><i class="ion-loading-c"></i></div>')
            $(this).hide(0);
            $('#loooOAOA').show(0);
        }
    })
    $("#addGraffity").click(function(){
        $('#popups').html(`<div class="popup">
            <!-- drawingboard cdn files -->
            <script src="/drawingboard/dist/drawingboard.min.js"></script>
            <link rel="stylesheet" type="text/css" href="/drawingboard/dist/drawingboard.min.css">
            <!-- drawingboard cdn files closed -->

            <div class="popup_container popup_drawingboard">
                <div class="popup_header">
                    <div>Нарисовать граффити</div>
                    <div class="popup_close" onclick="$('.popup').remove()">
                        <i class="ion-android-close"></i>
                    </div>
                </div>
                <div class="popup_content" id="zhnGraffity"></div>
                <div class="popup_content flex" id="iDeeNNTT">
                    <button class="button ml-a" onclick="$(this).hide(0);$.post('/app/ajax/graffity.php', {photo: document.querySelector('canvas').toDataURL('image/jpeg')}, function(){location.reload();})">Опубликовать</button>
                </div>
            </div>
            <!-- Starting -->
            <script>
                var zhnGraffity = new DrawingBoard.Board('zhnGraffity');
                $('.drawing-board-control-navigation-reset').click();
            </script>
        </div>`);
    })
    $('#follow_community').click(function(){
        if($(this).html() == '<i class="ion-minus"></i>Отписаться'){
            $(this).html('<i class="ion-plus"></i>Подписаться');
        }else if($(this).html() == '<i class="ion-plus"></i>Подписаться'){
            $(this).html('<i class="ion-minus"></i>Отписаться');
        }else{
            location.reload();
        }
        $.post('/app/ajax/followCommunity.php', {followTo:$(this).data('follow')});
    })
    $('#block').click(function(){
        $.post('/app/ajax/block.php', {block:$(this).data('block')}, function(){
            location.reload();
        });
    })
    $('#after_pfp_a').click(function(){
        $('#popups').html(`<div class="popup"><div class="popup_container popup_large"><div class="popup_header">Добавить фотографию профиля<div></div><div class="popup_close" onclick="$('.popup').remove();"><i class="ion-android-close"></i></div></div><form method="post" action="/cropper" enctype="multipart/form-data" class="popup_content">Если добавить свою фотографию профиля, будет ещё лучше!<br><br><i class="ion-loading-c" id="loading" style="display:none;"></i><label class="button">Выбрать файл...<input type="file" onchange="$('#loading').show(0);$('label').hide(0);this.form.submit();" hidden="" name="pfp" accept="image/*"></label><br><br><span id="coOLOGgg">Вы можете загрузить изображение в формате JPG, GIF или PNG.</span><br><a href="/personalize?action=restore_photo">Вернуть фотографию по умолчанию</a></form></div></div>`);
    })
    $('#after_community_a').click(function(){
        $('#popups').html(`<div class="popup"><div class="popup_container popup_large"><div class="popup_header">Добавить фотографию сообщества<div></div><div class="popup_close" onclick="$('.popup').remove();"><i class="ion-android-close"></i></div></div><form method="post" enctype="multipart/form-data" class="popup_content">Если добавить фотографию сообществу, будет ещё лучше!<br><br><i class="ion-loading-c" id="loading" style="display:none;"></i><label class="button">Выбрать файл...<input type="file" onchange="$('#loading').show(0);$('label').hide(0);this.form.submit();" hidden="" name="pfp" accept="image/*"></label><br><br><span id="coOLOGgg">Вы можете загрузить изображение в формате JPG, GIF или PNG.</span></form></div></div>`);
    })
    $('#pfp_a').click(function(){
        $('#popups').html(`<div class="popup"><div class="popup_container photo_viewer"><div class="popup_header"><div></div><div class="popup_close" onclick="$('.popup').remove();"><i class="ion-android-close"></i></div></div><div class="popup_content"><img src="${$(this).attr('src')}"><br><center><a href="${$(this).attr('src')}">Открыть оригинал</a></center></div></div></div>`);
    })
    $('.post .post_text .PostImgs .PostImgI img').click(function(){
        $('#popups').html(`<div class="popup"><div class="popup_container photo_viewer"><div class="popup_header"><div></div><div class="popup_close" onclick="$('.popup').remove();"><i class="ion-android-close"></i></div></div><div class="popup_content"><img src="${$(this).attr('src')}"><br><center><a href="${$(this).attr('src')}">Открыть оригинал</a></center></div></div></div>`);
    })
    $('#loginPOPUP').click(() => {
        $('#popups').html(`<div class="popup"><div class="popup_container"><div class="popup_header"><div>Войти в ваш аккаунт</div><div class="popup_close" onclick="$('.popup').remove()"><i class="ion-android-close"></i></div></div><div class="popup_content flex"><form method="post"><div><input type="email" placeholder="email" name="Semail" class="input"></div><div><input type="password" placeholder="пароль" name="Spassword" class="input"></div><div><button type="submit" name="signin" class="button">Войти</button></div></form></div></div></div>`);
    })
    $('.sidebar-element').click(function(){
        location.href = $(this).data('link');
    })
    $('.global--link').click(function(){
        location.href = $(this).data('link');
    })
    $('#follow').click(function(){
        if($(this).html() == '<i class="ion-minus"></i>Отписаться'){
            $(this).html('<i class="ion-person-add"></i>Подписаться');
        }else if($(this).html() == '<i class="ion-person-add"></i>Подписаться'){
            $(this).html('<i class="ion-minus"></i>Отписаться');
        }else{
            location.reload();
        }
        $.post('/app/ajax/follow.php', {followTo:$(this).data('follow')});
    })
    $('.like-post').click(function(){
        $.post('/app/ajax/like.php', {like:$(this).data('status')});
        if($(this).hasClass('post_action_active')){
            $(this).removeClass('post_action_active');
            $('#post-' + $(this).data('status') + ' .post_actions .like-post .count-likes').html(Number($('#post-' + $(this).data('status') + ' .count-likes').html()) - 1);
        }else{
            $(this).addClass('post_action_active');
            $('#post-' + $(this).data('status') + ' .post_actions .like-post .count-likes').html(Number($('#post-' + $(this).data('status') + ' .count-likes').html()) + 1);
        }
    })
    $('.dropdown_trig').click(function(){
        $($(this).data('id')).slideToggle();
    })
    $('.remove_comment').click(function(){
        $('#comment-' + $(this).data('comment')).slideUp(450);
        setTimeout(() => {
            $('#comment-' + $(this).data('comment')).remove();
        }, 500)
        $.post('/app/ajax/delete_comment.php', {delete:$(this).data('comment')});
    })
    $('.remove_status').click(function(){
        $('#post-' + $(this).data('status')).slideUp(450);
        setTimeout(() => {
            $('#post-' + $(this).data('status')).remove();
        }, 500)
        $.post('/app/ajax/delete_status.php', {delete:$(this).data('status')});
    })
    $('.tab').click(function(){
        if(!$(this).hasClass('tab_dropdown') && !$(this).hasClass('tab-active')){
            $('.profile_content').slideUp();
            $($(this).data('show')).slideDown();
            $('.tab').removeClass('tab-active');
            $(this).addClass('tab-active');
        }
    })
})