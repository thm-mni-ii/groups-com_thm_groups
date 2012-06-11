<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/edit.php';

class THMGroupsModeleditAdminTest extends PHPUnit_Framework_TestCase
{
	var $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModeledit();
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
	
	// tests getStructure()
	// returns an objectlist
	// first object is "Titel", type -> Texts
	function testgetStructure() {
		$result = $this->instance->getStructure();
		$expected = "TEXT";
	
		$this->assertEquals($expected, $result[0]->type);
	}
	
	// tests getTypes
	// function returns objectlist
	// first object type should be "LINK"
	function testgetTypes(){
		$result = $this->instance->getTypes();
	
		$this->assertTrue($result[0]->Type == "LINK");
	}
	
	// tests updatePic
	// inserts an value in database
	// function sholud update value
	// but picFile is null, function return false
	function testupdatePic(){
	
		$db =& JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_picture (userid, structid, value, publish, group)";
		$query .= "VALUES ('99999','88888',' ','0','0')";
		$db->setQuery( $query );
		$db->query();
	
		$picField = null;
		$result = $this->instance->updatePic("99999", "88888", $picField);
		$expected = false; // because of $picField = null
	
		$sql = "DELETE FROM #__thm_groups_picture WHERE userid = '99999'";
		$db->setQuery( $sql);
		$db->query();
	
		$this->assertEquals($expected, $result);
	}
	
	// tests delPic
	// insert an value in database
	// function updates the value and returns true
	function testdelPic(){
		$array['userid'] = '99999';
		$array['structid'] = '88888';
		JRequest::set($array, 'post');
	
		$db =& JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_picture (userid, structid, value, publish, group)";
		$query .= "VALUES ('99999','88888',' ','0','0')";
		$db->setQuery( $query );
		$db->query();
	
		$result = $this->instance->delPic();
		$expected = true;
	
		$sql = "DELETE FROM #__thm_groups_picture WHERE userid = '99999'";
		$db->setQuery( $sql);
		$db->query();
	
		$this->assertEquals($expected, $result);
	}
	
	// tests getExtra
	// insert value in database
	// returns value vom inserted query (THMGroupsTest.jpg)
	function testgetExtra() {
		$structid = "999999";
		$type = "picture";
	
		$db =& JFactory::getDBO();
		$sql = "INSERT INTO #__thm_groups_picture_extra (structid, value)";
		$sql .= "VALUES ('999999' ,'THMGroupsTest.jpg')";
		$db->setQuery( $sql);
		$db->query();
	
		$result = $this->instance->getExtra($structid, $type);
	
		$sql = "DELETE FROM #__thm_groups_picture_extra WHERE structid = '999999'";
		$db->setQuery( $sql);
		$db->query();
	
		$expected = "THMGroupsTest.jpg";
		$this->assertEquals($expected, $result);
	}
	
	// tests addTableRow
	// function inserts two values and should return true
	function testaddTableRow(){
		$array['userid'] = '99999';
		$array['structid'] = '88888';
		JRequest::set($array, 'post');
	
		$result = $this->instance->addTableRow();
		$expected = true;
	
		$this->assertTrue($result == $expected);
	}
	
	// tests delTableRow
	// insert an value in database
	// funcion updates value and should return true
	function testdelTableRow(){
		$array['userid'] = '99999';
		$array['structid'] = '88888';
		$array['tablekey'] = '';
		JRequest::set($array, 'post');
	
		$db =& JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_table (`userid`, `structid`, `value`, `publish`, `group`)";
		$query .= " VALUES ('99999','88888',' ','0','0')";
		$db->setQuery( $query );
		$db->query();
	
		$result = $this->instance->delTableRow();
		$expected = true;
	
		$query = "DELETE FROM #__thm_groups_table WHERE structid = '88888'";
		$db->setQuery( $query);
		$db->query();
	
		$this->assertTrue($result == $expected);
	}
	
	
	// test editTableRow
	// insert two values in database
	// function change one entry and should return true
	function testeditTableRow(){
		$array['userid'] = '99999';
		$array['structid'] = '88888';
		$array['tablekey'] = '';
		JRequest::set($array, 'post');
	
		$db =& JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_table (`userid`, `structid`, `value`, `publish`, `group`)";
		$query .= " VALUES ('99999','88888',' ','0','0')";
		$db->setQuery( $query );
		$db->query();
	
		$query = "INSERT INTO #__thm_groups_table_extra (`structid`, `value`)";
		$query .= " VALUES ('88888','Spalte1;Spalte2')";
		$db->setQuery( $query );
		$db->query();
	
		$result = $this->instance->editTableRow();
		$expected = true;
	
		$query = "DELETE FROM #__thm_groups_table WHERE structid = '88888'";
		$db->setQuery( $query);
		$db->query();
	
		$query = "DELETE FROM #__thm_groups_table_extra WHERE structid = '88888'";
		$db->setQuery( $query);
		$db->query();
	
		$this->assertTrue($result == $expected);
	}
	
	// tests getForm
	// returns JForm object
	function testgetForm(){
		$result = $this->instance->getForm();
		$expected = new JForm();
		$this->assertNotSame($expected, $result);
	}
	
	
}

?>