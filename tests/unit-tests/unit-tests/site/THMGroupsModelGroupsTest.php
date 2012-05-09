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
	
	/*
	 * No tests:
	* canEdit
	*/
	
	function testgetGroups(){
		$result = $this->instance->getGroups();
		
		$this->assertEquals($result[34]->id,"7");
		$this->assertEquals($result[34]->name,"Administrator");
		$this->assertEquals($result[34]->injoomla,"1");
	}

}

?>