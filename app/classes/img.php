<?php
class Image{
	public function convertImage($originalImage, $outputImage, $quality){
	    $exploded = explode('.',$originalImage);
	    $ext = $exploded[count($exploded) - 1]; 

	    if (preg_match('/jpg|jpeg/i',$ext))
	        $imageTmp=imagecreatefromjpeg($originalImage);
	    else if (preg_match('/png/i',$ext))
	        $imageTmp=imagecreatefrompng($originalImage);
	    else if (preg_match('/gif/i',$ext))
	        $imageTmp=imagecreatefromgif($originalImage);
	    else if (preg_match('/bmp/i',$ext))
	        $imageTmp=imagecreatefrombmp($originalImage);
	    else

	        die('<html lang="en"><head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/global.css">
    <title>Zohan - share what happend with you.</title>
</head>
<body>
    <div id="popups">
            </div>
    


    <div class="container">
                <div class="content" style="
    width: 300px;
    margin: 0 auto;
"><h1 class="colorpri">Ошибка</h1><br>Формат файла не поддерживается.<br><a href="/">На главную страницу &gt;&gt;&gt;</a></div>
    </div>

</body></html>');

	    imagejpeg($imageTmp, $outputImage, $quality);
	    imagedestroy($imageTmp);

	    return 1;
	}

}
?>