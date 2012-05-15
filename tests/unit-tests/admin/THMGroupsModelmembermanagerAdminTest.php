<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/membermanager.php';
require_once 'PHPUnit.php';

class THMGroupsModelmembermanagerAdminTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelmembermanagerAdminTest($name) {
		$this->PHPUnit_TestCase($name);
	}

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