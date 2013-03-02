<?php

if(!defined('JPATH_COMPONENT_ADMINISTRATOR')) define('JPATH_COMPONENT_ADMINISTRATOR',JPATH_ROOT . '/administrator/components/com_thm_groups');

require_once JPATH_BASE.'/components/com_thm_groups/models/advanced.php';

class THMGroupsModelAdvancedTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$params = new JDispatcher();
		$params->set('selGroup','10');
		/*
		$array['selGroup'] = '1';
		JRequest::set($array, 'post');
		*/
		$this->instance = new THMGroupsModelAdvanced();
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
	 * getGroupNumber
	 * canEdit
	 */
	
	// tests getViewParams()
	// sholud return an object with siteinfos
	function testgetViewParams(){
		$frameParams = Jfactory::getApplication(); ;
		$expected = $frameParams->getParams();
		
		$result = $this->instance->getViewParams();
		$this->assertTrue($result == $expected);
	}
	
	// tets getGroupNumber
	// Cannot get siteparams, object should be null
	function testgetGroupNumber(){
		$result = $this->instance->getGroupNumber();
		$this->assertTrue($result == null);
	}
	
	// tests getImage
	// returns an html string
	function testgetImage(){
		$path = "PfadZuGroup";
		$text = "THMGroupsTest";
		$cssc = "TestCSSC";
		
		$result = $this->instance->getImage($path, $text, $cssc);
		$expected = "<img src=\"\" alt=\"THMGroupsTest\" class=\"TestCSSC\" />";
		$this->assertTrue($result == $expected);
	}
	
	// tests getLink
	// returns an html string
	function testgetLink(){
		$path = "PfadZuGroup";
		$text = "THMGroupsTest";
		$cssc = "TestCSSC";
	
		$result = $this->instance->getLink($path, $text, $cssc);
		$expected = "<a class=\"TestCSSC\" href=\"PfadZuGroup\" target=\"_blank\">THMGroupsTest</a>";
		$this->assertTrue($result == $expected);
	}
	
	// tests getUnsortedRoles
	// insert 2 values
	// returns an objectlist with Groupids
	// first objectid should be 0 (last value)and second 1.
	function testgetUnsortedRoles() {
		$db = JFactory::getDBO();
		$sql = "INSERT INTO #__thm_groups_groups_map (uid, gid, rid)";
		$sql .= "VALUES ('999999' ,'999999' ,'0')";
		$db->setQuery( $sql);
		$db->query();
		
		$sql = "INSERT INTO #__thm_groups_groups_map (uid, gid, rid)";
		$sql .= "VALUES ('999999' ,'999999' ,'1')";
		$db->setQuery( $sql);
		$db->query();
		
		$gid = "999999";
		$result = $this->instance->getUnsortedRoles($gid);
		
		$sql = "DELETE FROM #__thm_groups_groups_map WHERE uid = '999999' AND gid = '999999'";
		$db->setQuery( $sql);
		$db->query();
		
		$expected[0] = "0";
		$expected[1] = "1";
		
		$this->assertTrue($expected[0] == $result[0]);
		$this->assertTrue($expected[1] == $result[1]);
	}
	
	// tests getStructure()
	// returns an objectlist
	// first object is "Titel", type -> Texts
	function testgetStructure() {
		$result = $this->instance->getStructure();
		$expected = "TEXT";
		
		$this->assertEquals($expected, $result[0]->type);
	}
	
	// tests getExtra
	// insert value in database
	// returns value vom inserted query (THMGroupsTest.jpg)
	function testgetExtra() {
		$structid = "999999";
		$type = "picture";
		
		$db = JFactory::getDBO();
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
	
}

?>