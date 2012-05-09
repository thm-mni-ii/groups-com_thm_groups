<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/addrole.php';
require_once 'PHPUnit.php';

class THMGroupsModelAddRoleAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelAddRoleAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelAddRole();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>