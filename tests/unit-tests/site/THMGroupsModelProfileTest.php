<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/profile.php';

class THMGroupsModelProfileTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelProfile();
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
	 * canEdit
	 * store
	 */
	
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
	
	// tests updatePic
	// inserts an value in database
	// function sholud update value
	// but picFile is null, function return false
	function testupdatePic(){
	
		$db =& JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_picture (userid, structid, value, publish, group";
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
		$this->assertTrue($result[0]->Type != null);
	}
}

?>