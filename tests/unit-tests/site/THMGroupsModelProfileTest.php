<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/profile.php';
require_once 'PHPUnit.php';

class THMGroupsModelProfileTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelProfileTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelProfile();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
}

?>