<?php

require_once JPATH_BASE.'/components/com_thm_groups/helper/thm_groups_pictransform.php';

class PicTransformTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	var $instance;


	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	/*function setUp() {
		//--- create test tmpl file, which is required for constructor
		$picField = array (
				'name' 		=> 'test.jpg',
				'type'		=> 'image/jpeg',
				'tmp_name' 	=> 'C:/xampp/tmp/phptest.tmp',
				'error' 	=> 0,
				'size' 		=> 1111
		);
		$_FILES = array (
			'testPicture' => $picField
		);
		clearstatcache();
		var_dump(is_uploaded_file($_FILES['testPicture']['tmp_name']));
		$tmplFile = fopen("C:/xampp/tmp/phptest.tmp","w");
		$picField = $_FILES;
		$this->instance = new PicTransform($picField);
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		// delete your instance
				
		unset($this->instance);
	}
	
	// test the getPath() function
	function testGetPath() {
		$result = $this->instance->getPath();
		var_dump($result);
		//$expected = array("thm_groups" => "THM Gruppen");
		//$this->assertEquals($expected, $result);
		$this->assertTrue(true);
	}	
	
	// test the getType()
	function testGetType() {
		$result = $this->instance->getType();
		var_dump($result);
		$this->assertTrue(true);
	}
	
	// test the getExtension()
	function testGetExtension() {
		$result = $this->instance->getExtension();
		var_dump($result);
		$this->assertTrue(true);
	}
	
	// test the safePlain($dest, $filename, $type="PNG")
	function testSafePlain() {
		$result = $this->instance->safePlain($dest, $filename, $type="PNG");
		var_dump($result);
		$this->assertTrue(true);
	}
	
	// test the safeSpecial($dest, $filename, $maxWidth, $maxHeight, $type="PNG")
	function testSafeSpecial() {
		$result = $this->instance->safeSpecial($dest, $filename, $maxWidth, $maxHeight, $type="PNG");
		var_dump($result);
		$this->assertTrue(true);
	}
	
	// test the safeImage($image, $dest, $filename, $type="PNG")
	function testSafeImage() {
		$result = $this->instance->safeImage($image, $dest, $filename, $type="PNG");
		var_dump($result);
		$this->assertTrue(true);
	}
	
	// test the maxWidth($image, $maxWidth)
	function testMaxWidth() {
		$result = $this->instance->maxWidth($image, $maxWidth);
		var_dump($result);
		$this->assertTrue(true);
	}
	
	// test the maxHeight($image, $maxHeight)
	function testMaxHeight() {
		$result = $this->instance->maxHeight($image, $maxHeight);
		var_dump($result);
		$this->assertTrue(true);
	}*/
}

?>