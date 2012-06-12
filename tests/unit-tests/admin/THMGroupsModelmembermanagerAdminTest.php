<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/membermanager.php';

class THMGroupsModelmembermanagerAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelmembermanager();
	}

	// Kill instance
	function tearDown() {
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}
	
	/*
	 * No tests:
	* populateState (protected)
	* getListQuery (protected)
	*/

}

?>