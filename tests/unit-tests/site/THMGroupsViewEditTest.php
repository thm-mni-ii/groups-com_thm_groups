<?php

require_once JPATH_BASE.'/components/com_thm_groups/views/edit/view.html.php';
require_once 'PHPUnit.php';

class THMGroupsViewEditTest extends PHPUnit_TestCase
{
	// contains the object handle of the string class
	var $instance;

	// constructor of the test suite
	function THMGroupsViewEditTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		$this->instance = new THMGroupsViewEdit();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		// delete your instance
		
		unset($this->instance);
	}
	

	/*
	// test the display($tpl = null)
	function testDisplay() {
		
		$result = $this->instance->display($tpl = null);
		var_dump($result);
		$this->assertTrue(true);
	}*/
}

?>