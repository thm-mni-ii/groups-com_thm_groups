<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/addstructure.php';

class THMGroupsModelAddStructureAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelAddStructure();
	}

	// Kill instance
	function tearDown() {
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}
	
	// tests _buildQuery()
	// function returns an SQL query
	// should start with SELECT
	function test_buildQuery(){
		$result = $this->instance->_buildQuery();
		$expected = "SELECT ";
		$this->assertContains($expected, $result);
	}
	
	// tests getData
	// function returns array
	// first object should be Type "Date"
	function testgetData(){
		$result = $this->instance->getData();
		$this->assertTrue($result[0]->Type == "DATE");
	}
	
	// tests store
	// function inserts value in database
	// sholud return true
	function teststore(){
		
		$array['name'] = 'THMGroupsTestSuite';
		$array['relation'] = 'TEXT';
		JRequest::set($array, 'post');
		
		$result = $this->instance->store();
		
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__thm_groups_structure WHERE field = 'THMGroupsTestSuite'";
		$db->setQuery($query);
		$db->query();
		
		$this->assertTrue($result);
	}
}

?>