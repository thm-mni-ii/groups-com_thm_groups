<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/editgroup.php';
require_once 'PHPUnit.php';

class THMGroupsModelEditGroupAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelEditGroupAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelEditGroup();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>