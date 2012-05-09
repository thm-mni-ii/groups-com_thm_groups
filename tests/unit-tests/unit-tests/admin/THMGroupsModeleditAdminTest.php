<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/edit.php';
require_once 'PHPUnit.php';

class THMGroupsModeleditAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModeleditAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModeledit();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>