<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/addrole.php';

class THMGroupsModelAddRoleAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelAddRole();
	}

	// Kill instance
	function tearDown() {
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}
	
	// tests store()
	// function inserts value in database
	// should return true
	// delete value
	function teststore(){
		
		$array['role_name'] = 'THMGroupsTestSuite';
		JRequest::set($array, 'post');
		
		$result = $this->instance->store();
		$expected = true;
		
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__thm_groups_roles WHERE name = 'THMGroupsTestSuite'";
		$db->setQuery($query);
		$db->query();
		
		$this->assertTrue($result == $expected);
	}
}

?>