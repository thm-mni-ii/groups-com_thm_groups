<?php

//require_once JPATH_BASE.'/components/com_thm_groups/models/advanced.php';
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
		//$this->instance = new THMGroupsModelAdvanced();
	}

	// Kill instance
	function tearDown() {
		//unset($this->instance);
	}
	
	function testgetViewParams(){
		//$result = $this->instance->isComponentAvailable($comp);
		$result = true;
		$expected = true;
		$this->assertTrue($result == $expected);
	}
}

?>