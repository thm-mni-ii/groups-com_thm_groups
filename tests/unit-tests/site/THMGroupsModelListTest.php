<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/list.php';
require_once 'PHPUnit.php';

class THMGroupsModelListTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelListTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelList();
	}

	// Kill instance
	function tearDown() {
		//unset($this->instance);
	}
	
}

?>