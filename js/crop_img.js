$(document).ready(function(){
    var size;
    $('#cropbox').Jcrop({
      aspectRatio: 1,
      boxWidth: 734,
        boxHeight: 600,
        aspectRatio: (300/300),
        pos: {},
      onSelect: function(c){
       size = {x:c.x,y:c.y,w:c.w,h:c.h};
       $("#crop").css("visibility", "visible");     
      }
    });
 
    $("#crop").click(function(){
        var img = $("#cropbox").attr('src');
        if(size == null){
            $('#popups').html('<div class="popup"><div class="popup_container"><div class="popup_header"><div>Ошибка</div><div class="popup_close" onclick="$(`.popup`).remove()"><i class="ion-android-close"></i></div></div><div class="popup_content flex">Выберите миниатюру!</div></div></div>')
        }else{
            location.href = 'image-crop.php?x='+size.x+'&y='+size.y+'&w='+size.w+'&h='+size.h+'&img='+img
        }
    });
});