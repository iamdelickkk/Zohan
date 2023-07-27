<?php
class Text{
	public function convertText($string){
		$string = htmlspecialchars($string);
		$string = trim($string);
		$string = stripslashes($string);
		return $string;
	}
}
?>