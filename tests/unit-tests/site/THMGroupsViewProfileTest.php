<?php

require_once JPATH_BASE.'/components/com_thm_groups/views/profile/view.html.php';

class THMGroupsViewProfileTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	protected $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		//--- set test user
		
		$this->instance = new THMGroupsViewProfile();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}
	/*
	// test the getExtra($structId, $type) function
	function testGetExtra() {
		//$structId = 1;
		//$type = 'TEXT';
		//$result = $this->instance->getExtra($structId, $type);
		//var_dump($result);
		//$expected = array("thm_groups" => "THM Gruppen");
		//$this->assertEquals($expected, $result);
		$this->assertTrue(true);
	}	
	
	// test the getStructureType($structId)
	function testGetStructureType() {
		
		//$result = $this->instance->getStructureType($structId);
		$this->assertTrue(true);
	}
	
	// test the display($tpl = null)
	function testDisplay() {
		
		//$result = $this->instance->display($tpl = null);
		//var_dump($result);
		$this->assertTrue(true);
	}*/
}

?>