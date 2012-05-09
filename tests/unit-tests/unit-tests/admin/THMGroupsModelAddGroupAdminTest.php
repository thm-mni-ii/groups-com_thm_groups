<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/addgroup.php';
require_once 'PHPUnit.php';

class THMGroupsModelAddGroupAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelAddGroupAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelAddGroup();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>