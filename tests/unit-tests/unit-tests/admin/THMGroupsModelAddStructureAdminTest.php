<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/addstructure.php';
require_once 'PHPUnit.php';

class THMGroupsModelAddStructureAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelAddStructureAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelAddStructure();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>