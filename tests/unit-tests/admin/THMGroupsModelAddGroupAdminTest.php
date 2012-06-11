<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/addgroup.php';

class THMGroupsModelAddGroupAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelAddGroup();
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
	* store
	*/
	
	// tests updatePic
	// inserts an value in database
	// function sholud update value
	// but picFile is null, function return false
	function testupdatePic(){
	
		$db =& JFactory::getDBO();
		$query = "INSERT INTO  #__thm_groups_groups(`id` ,`name` ,`info` ,`picture` ,`mode` ,`injoomla`)
					VALUES ('99999',  'THMGroupsSuite',  'Test',  'test.jpg', NULL ,  '1')";
		$db->setQuery( $query );
		$db->query();
	
		$picField = null;
		$result = $this->instance->updatePic("99999", "88888", $picField);
		$expected = false; // because of $picField = null
	
		$sql = "DELETE FROM #__thm_groups_groups WHERE id = '99999'";
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
	
	// tests getAllGroups
	// returns objectlist with all groups
	// first Object must be Public
	function testgetAllGroups(){
		$result = $this->instance->getAllGroups();
		$expected = "Public";
		$this->assertTrue($result[0]->title == $expected);
	}
}

?>