<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/groups.php';
require_once 'PHPUnit.php';

class THMGroupsModelGroupsTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelGroupsTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelGroups();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}

}

?>