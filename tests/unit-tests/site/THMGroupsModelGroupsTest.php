<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/groups.php';

class THMGroupsModelGroupsTest extends PHPUnit_Framework_TestCase
{
	var $instance;

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