<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/rolemanager.php';
require_once 'PHPUnit.php';

class THMGroupsModelRolemanagerAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelRolemanagerAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelRolemanager();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	/*
	 * No tests:
	* populateState (protected)
	* getListQuery (protected)
	*/
}

?>