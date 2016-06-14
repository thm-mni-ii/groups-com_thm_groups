<?php

require_once JPATH_BASE . '/administrator/components/com_thm_groups/models/editrole.php';

class THMGroupsModelEditRoleAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp()
	{
		$this->instance = new THMGroupsModelEditRole();
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
	* getData
	*/

	// tests _buildQuery()
	// function returns an SQL query
	// should start with SELECT
	function test_buildQuery()
	{
		$result   = $this->instance->_buildQuery();
		$expected = "SELECT ";
		$this->assertContains($expected, $result);
	}

	// tests store
	// function updates some values in database
	// should return true
	function teststore()
	{
		$array['role_name'] = 'THMGroupsSuiteTest';
		$array['rid']       = '999999';
		JRequest::set($array, 'post');

		$db    = JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_roles (`id`, `name`)";
		$query .= "VALUES ('999999' ,  'THMGroupsSuite')";
		$db->setQuery($query);
		$db->query();

		$result = $this->instance->store();

		$query = "DELETE FROM #__thm_groups_roles WHERE id = '999999'";
		$db->setQuery($query);
		$db->query();

		$this->assertTrue($result);
	}
}

?>