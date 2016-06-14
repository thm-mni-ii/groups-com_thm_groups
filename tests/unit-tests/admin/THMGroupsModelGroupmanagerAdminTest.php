<?php

require_once JPATH_BASE . '/administrator/components/com_thm_groups/models/groupmanager.php';

class THMGroupsModelGroupmanagerAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp()
	{
		$this->instance = new THMGroupsModelGroupmanager();
	}

	// Kill instance
	function tearDown()
	{
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}

	/*
	 * No tests:
	* populateState (protected)
	* getListQuery (protected)
	* getfreeGroups
	* getfullGroupIDs
	*/

	// tests getJoomlaGroups
	// returns objectlist with all groups
	// first object->title = Public
	function testgetJoomlaGroups()
	{
		$result = $this->instance->getJoomlaGroups();
		$this->assertTrue($result[0]->title == "Public");
	}

	// tests delGroup
	// insert value in database
	// function deletes inserted value
	function testdelGroup()
	{

		$db    = JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_groups (id, name, info, picture, mode, injoomla)";
		$query .= "VALUES ('99999','THMGroupsTest','TestSuite','','','1')";
		$db->setQuery($query);
		$db->query();

		$result = $this->instance->delGroup('99999');
	}
}

?>