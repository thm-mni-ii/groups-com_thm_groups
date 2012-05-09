<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/editstructure.php';
require_once 'PHPUnit.php';

class THMGroupsModelEditStructureAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelEditStructureAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelEditStructure();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function test(){
	
	}
}

?>