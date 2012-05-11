<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/groupmanager.php';
require_once 'PHPUnit.php';

class THMGroupsModelGroupmanagerAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelGroupmanagerAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelGroupmanager();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>