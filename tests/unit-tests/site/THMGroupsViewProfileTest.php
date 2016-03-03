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
}

?>