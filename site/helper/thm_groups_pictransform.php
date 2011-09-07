<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
class PicTransform {
	private $picFile, $type;

	/**
	 * Constructor with the Picturefile to transform
	 *
	 * @param $_FILES[] $picFile
	 */
	public function __construct($picFile) {
		$this->picFile = $picFile;

		if(!is_uploaded_file($this->picFile['tmp_name'])) {
			throw new Exception("Datei nicht hochgeladen");
		}

		$imgSize = getimagesize($this->picFile['tmp_name']);

		switch($imgSize[2]) {
			case 1: $this->type = "GIF"; break;
			case 2: $this->type = "JPG"; break;
			case 3: $this->type = "PNG"; break;
			default: throw new Exception("Unpassender Typ"); break;
		}
	}

	/**
	 * Gets the path of the uploaded file
	 *
	 * @return unknown
	 */
	public function getPath() {
		return $this->picFile['tmp_name'];
	}

	/**
	 * Returns the filetype (if picture)
	 *
	 * @return String ("GIF"/"JPG"/"PNG")
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Returns the fileextension
	 *
	 * @return String (".gif"/".jpg"/".png")
	 */
	public function getExtension() {
		switch($this->getType()){
			case 'GIF': return ".gif"; break;
			case 'JPG': return ".jpg"; break;
			case 'PNG': return ".png"; break;
			default: throw new Exception("Nicht unterstuetztes Format"); break;
		}
	}

	/**
	 * Saves the file to $dest with $filename in picturtype $type
	 *
	 * @param string $dest
	 * @param string $filename
	 * @param string $type
	 * @return full destination
	 */
	public function safePlain($dest, $filename, $type="PNG") {
		switch($this->getType()){
			case 'PNG':
				$image = imagecreatefrompng($this->getPath());
				break;
					
			case 'GIF':
				$image = imagecreatefromgif($this->getPath());
				break;
					
			case 'JPG':
				$image = imagecreatefromjpeg($this->getPath());
				break;
					
			default: throw new Exception("Falscher Dateityp");break;
		}

		return $this->safeImage($image, $dest, $filename, $type);
	}

		/**
	 * Saves the file to $dest with $filename in picturtype $type with $maxHeight and $maxWidth
	 *
	 * @param string $dest
	 * @param string $filename
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @param string $type
	 * @return full destination
	 */
	public function safeSpecial($dest, $filename, $maxWidth, $maxHeight, $type="PNG") {
		switch($this->getType()) {
			case 'JPG':
				$image = imagecreatefromjpeg($this->getPath());
				break;
					
			case 'GIF':
				$image = imagecreatefromgif($this->getPath());
				break;
					
			case 'PNG':
				$image = imagecreatefrompng($this->getPath());
				break;
					
			default: throw new Exception("Falscher Typ"); break;
		}

		$image = $this->maxWidth($image, $maxWidth);
		$image = $this->maxHeight($image, $maxHeight);
		
		return $this->safeImage($image, $dest, $filename, $type);
	}	
	
	private function safeImage($image, $dest, $filename, $type="PNG") {
		switch($type) {
			case 'GIF':
				imagegif($image, $dest.$filename.'.gif');
				return $filename.'.gif';
				break;
					
			case 'JPG':
				imagejpeg($image, $dest.$filename.'.jpg');
				return $filename.'.jpg';
				break;
					
			case 'PNG':
				imagepng($image, $dest.$filename.'.png');
				return $filename.'.png';
				break;
					
			default: throw new Exception("Illegaler Typ");break;
		}
	}

	/**
	 * resizes the Picture an keeps Properties. Resized by Width
	 *
	 * @param int $image
	 * @param int $maxWidth
	 * @return img
	 */
	private function maxWidth($image, $maxWidth) {
		$width  = imagesx($image);
		$height = imagesy($image);

		if($width > $maxWidth) {
			$newWidth  = $maxWidth;
			$newHeight = floor($maxWidth * ($height/$width));
		} else {
			$newWidth = $width;
			$newHeight = $height;
		}
		
		return $this->resizeImg($image, $newWidth, $newHeight);
	}

	/**
	 * resizes the Picture an keeps Properties. Resized by Height
	 *
	 * @param unknown_type $image
	 * @param unknown_type $maxWidth
	 * @return img
	 */
	private function maxHeight($image, $maxHeight) {
		$width  = imagesx($image);
		$height = imagesy($image);

		if($height > $maxHeight) {
			$newHeight = $maxHeight;
			$newWidth  = floor($maxHeight * ($width/$height));
		} else {
			$newWidth = $width;
			$newHeight = $height;
		}

		return $this->resizeImg($image, $newWidth, $newHeight);
	}

	/**
	 * Resizes a picture to given size
	 *
	 * @param img $image
	 * @param int $newWidth
	 * @param int $newHeight
	 * @return img
	 */
	private function resizeImg($image, $newWidth, $newHeight) {
		$width = imagesx($image);
		$height = imagesy($image);
		
		$newImg = imagecreatetruecolor($newWidth, $newHeight);

		imagecopyresized($newImg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		return $newImg;
	}

	/**
	 * Returns the path of the uploaded file
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->picFile['tmp_name'];
	}
}

//$defaultFolder = $_SERVER['DOCUMENT_ROOT']."/upload/";
//$pt = new PicTransform($_FILES['file']);
//echo $pt->safePlain($defaultFolder, "testPlain", $_POST['typ']);
//echo "<br/>";
//echo $pt->safeSpecial($defaultFolder, "testSpechial", $_POST['width'],  $_POST['height'], $_POST['typ']);
?>