<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/membermanager.php';

class THMGroupsModelmembermanagerAdminTest extends PHPUnit_Framework_TestCase
{
	var $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelmembermanager();
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