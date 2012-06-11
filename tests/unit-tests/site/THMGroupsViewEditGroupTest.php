<?php

require_once JPATH_BASE.'/components/com_thm_groups/views/editgroup/view.html.php';

class THMGroupsViewEditGroupTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	var $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		$this->instance = new THMGroupsViewEditGroup();
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