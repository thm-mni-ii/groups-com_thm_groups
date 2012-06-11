<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/editstructure.php';

class THMGroupsModelEditStructureAdminTest extends PHPUnit_Framework_TestCase
{
	var $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelEditStructure();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	/*
	 * No tests:
	* getData
	* store
	*/
	
	// tests _buildQuery()
	// function returns an SQL query
	// should start with SELECT
	function test_buildQuery(){
		$result = $this->instance->_buildQuery();
		$expected = "SELECT ";
		$this->assertContains($expected, $result);
	}
	
	// tests getItem
	// function returns object from database
	// should: id = 1 , field = Vorname
	function testgetItem(){
		$array['cid'] = '1';
		JRequest::set($array, 'post');
		$result = $this->instance->getItem();
		$this->assertTrue($result->id == "1");
		$this->assertTrue($result->field == "Vorname");
	}
	
	// tests getExtra
	// insert value in database
	// function returns object
	// sholud returned inserted value 
	function testgetExtra() {
		$relation = "TEXT";
		$array['sid'] = '999999';
		JRequest::set($array, 'post');
		
		$db =& JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_text_extra (structid, value)";
		$query .= "VALUES ('999999','88888')";
		$db->setQuery( $query );
		$db->query();
		
		$result = $this->instance->getExtra($relation);
		
		$query = "DELETE FROM #__thm_groups_text_extra WHERE structid = '999999'";
		$db->setQuery($query);
		$db->query();
		
		$this->assertTrue($result->value == "88888");
	}
	
}

?>