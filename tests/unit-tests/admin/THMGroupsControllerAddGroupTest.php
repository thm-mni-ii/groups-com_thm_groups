<?php
define('JPATH_COMPONENT', '../../../administrator/components/com_thm_groups');
require_once JPATH_BASE.'/administrator/components/com_thm_groups/controllers/addgroup.php';

class THMGroupsControllerAddGroupTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	var $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		$this->instance = new THMGroupsControllerAddGroup();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		// delete your instance
		
		unset($this->instance);
	}
	
	/*	
	// test the apply() function
	// Doesn't work!!!
	function testApply() {
		
		$result = $this->instance->apply();
		$this->assertTrue(true);
	}*/
}

?>