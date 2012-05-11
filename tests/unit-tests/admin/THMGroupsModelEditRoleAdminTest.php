<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/editrole.php';
require_once 'PHPUnit.php';

class THMGroupsModelEditRoleAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelEditRoleAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelEditRole();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>