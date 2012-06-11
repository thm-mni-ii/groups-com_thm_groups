<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/structure.php';

class THMGroupsModelStructureAdminTest extends PHPUnit_Framework_TestCase
{
	var $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelStructure();
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