<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/rolemanager.php';

class THMGroupsModelRolemanagerAdminTest extends PHPUnit_Framework_TestCase
{
	var $instance;

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