<?php
/**
 *@category    Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups.site
 *@name		   PicTransform
 *@description THMGroups helper file for transformation of uploaded pictures
 *@author	   Dennis Priefer, dennis.priefer@mni.thm.de
 * 
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link		   www.mni.thm.de
 *@version	   3.0
 */

/**
 * Helper class PicTransform for component com_thm_groups
 *
 * Class provides methods for picture transformation during uploading
 *
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.mni.thm.de
 * @since       Class available since Release 1.0
 */
class PicTransform
{
	/**
	 * PictureFile
	 *
	 * @var    $_FILES[]
	 * @since  1.0
	 */
	private $_picFile;
	
	/**
	 * Type
	 *
	 * @var    integer
	 * @since  1.0
	 */
	private $_type;

	/**
	 * Constructor with the Picturefile to transform
	 *@since  Available since Release 1.0
	 *
	 * @param   $_FILES[]  $picFile  Array, which contains the uploaded file.
	 */
	public function __construct($picFile)
	{
		$this->_picFile = $picFile;

		if (!is_uploaded_file($this->_picFile['tmp_name']))
		{
			throw new Exception("Datei nicht hochgeladen");
		}
		else
		{
		}

		$imgSize = getimagesize($this->_picFile['tmp_name']);

		switch ($imgSize[2])
		{
			case 1:
				$this->_type = "GIF";
				break;
			case 2:
				$this->_type = "JPG";
				break;
			case 3:
				$this->_type = "PNG";
				break;
			default:
				throw new Exception("Unpassender Typ");
				break;
		}
	}

	/**
	 * Gets the path of the uploaded file
	 *
	 * @return path of the pic file
	 */
	public function getPath()
	{
		return $this->_picFile['tmp_name'];
	}

	/**
	 * Returns the filetype (if picture)
	 *
	 * @return String ("GIF"/"JPG"/"PNG")
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Returns the fileextension
	 *
	 * @return String (".gif"/".jpg"/".png")
	 */
	public function getExtension()
	{
		switch ($this->getType())
		{
			case 'GIF':
				return ".gif";
				break;
			case 'JPG':
				return ".jpg";
				break;
			case 'PNG':
				return ".png";
				break;
			default:
				throw new Exception("Nicht unterstuetztes Format");
				break;
		}
	}

	/**
	 * Saves the file to $dest with $filename in picturtype $type
	 *
	 * @param   string  $dest      Destination
	 * @param   string  $filename  File name
	 * @param   string  $type      Type of the file
	 * 
	 * @return full destination
	 */
	public function safePlain($dest, $filename, $type="PNG")
	{
		switch ($this->getType())
		{
			case 'PNG':
				$image = imagecreatefrompng($this->getPath());
				break;
			case 'GIF':
				$image = imagecreatefromgif($this->getPath());
				break;
			case 'JPG':
				$image = imagecreatefromjpeg($this->getPath());
				break;
			default: throw new Exception("Falscher Dateityp");
				break;
		}

		return $this->safeImage($image, $dest, $filename, $type);
	}

    /**
	 * Saves the file to $dest with $filename in picturtype $type with $maxHeight and $maxWidth
	 *
	 * @param   string  $dest       Destination
	 * @param   string  $filename   Filename
	 * @param   int     $maxWidth   Maximal width
	 * @param   int     $maxHeight  Maximal height
	 * @param   string  $type       Type of the file
	 * 
	 * @return full destination
	 */
	public function safeSpecial($dest, $filename, $maxWidth, $maxHeight, $type="PNG")
	{
		switch ($this->getType())
		{
			case 'JPG':
				$image = imagecreatefromjpeg($this->getPath());
				break;
			case 'GIF':
				$image = imagecreatefromgif($this->getPath());
				break;
			case 'PNG':
				$image = imagecreatefrompng($this->getPath());
				break;
			default:
				throw new Exception("Falscher Typ");
				break;
		}

		$image = $this->maxWidth($image, $maxWidth);
		$image = $this->maxHeight($image, $maxHeight);

		return $this->safeImage($image, $dest, $filename, $type);
	}

	/**
	 * Saves the file to $dest with $filename in picturtype $type
	 *
	 * @param   $_FILES[]  $image     Array with uploaded image
	 * @param   string     $dest      Destination
	 * @param   string     $filename  Filename
	 * @param   string     $type      Type of the file
	 *
	 * @return void
	 */
	private function safeImage($image, $dest, $filename, $type="PNG")
	{
		switch ($type)
		{
			case 'GIF':
				imagegif($image, $dest . $filename . '.gif');
				return $filename . '.gif';
				break;
			case 'JPG':
				imagejpeg($image, $dest . $filename . '.jpg');
				return $filename . '.jpg';
				break;
			case 'PNG':
				imagepng($image, $dest . $filename . '.png');
				return $filename . '.png';
				break;
			default:
				throw new Exception("Illegaler Typ");
				break;
		}
	}

	/**
	 * Resizes the Picture an keeps Properties. Resized by Width
	 *
	 * @param   $_FILES[]  $image     Image
	 * @param   int        $maxWidth  Maximal width
	 * 
	 * @return img
	 */
	private function maxWidth($image, $maxWidth)
	{
		$width  = imagesx($image);
		$height = imagesy($image);

		if ($width > $maxWidth)
		{
			$newWidth  = $maxWidth;
			$newHeight = floor($maxWidth * ($height / $width));
		}
		else
		{
			$newWidth = $width;
			$newHeight = $height;
		}
		return $this->resizeImg($image, $newWidth, $newHeight);
	}

	/**
	 * Resizes the Picture an keeps Properties. Resized by height
	 *
	 * @param   $_FILES[]  $image      Image
	 * @param   int        $maxHeight  Maximal height
	 * 
	 * @return img
	 */
	private function maxHeight($image, $maxHeight)
	{
		$width  = imagesx($image);
		$height = imagesy($image);

		if ($height > $maxHeight)
		{
			$newHeight = $maxHeight;
			$newWidth  = floor($maxHeight * ($width / $height));
		}
		else
		{
			$newWidth = $width;
			$newHeight = $height;
		}

		return $this->resizeImg($image, $newWidth, $newHeight);
	}

	/**
	 * Resizes a picture to given size
	 *
	 * @param   $_FILES[]  $image      Image
	 * @param   int        $newWidth   New width
	 * @param   int        $newHeight  New height
	 * 
	 * @return img
	 */
	private function resizeImg($image, $newWidth, $newHeight)
	{
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
	public function __toString()
	{
		return $this->_picFile['tmp_name'];
	}
}
