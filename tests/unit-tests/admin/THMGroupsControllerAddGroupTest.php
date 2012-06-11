<?php
if(!defined('JPATH_COMPONENT')) define('JPATH_COMPONENT', '../../../administrator/components/com_thm_groups');
require_once JPATH_BASE.'/administrator/components/com_thm_groups/controllers/addgroup.php';

class THMGroupsControllerAddGroupTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	protected $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		$this->instance = new THMGroupsControllerAddGroup();
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
	// test the apply() function
	// Doesn't work!!!
	function testApply() {
		
		$result = $this->instance->apply();
		$this->assertTrue(true);
	}*/
}

?>