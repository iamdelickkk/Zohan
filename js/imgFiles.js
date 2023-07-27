$(() => {
	$(document).on('change', '#imgFiles', function(){
		var totalFiles = $(this).get(0).files.length;
		if($(this)[0].files.length < 10){
			for(var i = 0; i < totalFiles; i++){
	          	$('#preview').append("<img class='previewImgPostPost' src = '"+URL.createObjectURL(event.target.files[i])+"'>");
	        }
		}else{
			$('#popups').html('<div class="popup"><div class="popup_container"><div class="popup_header"><div>Ошибка</div><div class="popup_close" onclick="$(`.popup`).remove()"><i class="ion-android-close"></i></div></div><div class="popup_content flex">Вы не можете выложить более 10 фотографий!</div></div></div>')
		}
	})
})