<?php

define('JPATH_COMPONENT_ADMINISTRATOR',JPATH_ROOT . '/administrator/components/com_thm_groups');

require_once JPATH_BASE.'/components/com_thm_groups/models/advanced.php';
require_once 'PHPUnit.php';

class THMGroupsModelAdvancedTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelAdvancedTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelAdvanced();
	}

	// Kill instance
	function tearDown() {
		//unset($this->instance);
	}
	
	function testgetViewParams(){
		$frameParams = Jfactory::getApplication(); ;
		$expected = $frameParams->getParams();
		$result = $this->instance->getViewParams();

		$this->assertTrue($result == $expected);
	}
	
	function testgetGroupNumber(){
		$result = $this->instance->getGroupNumber();
		$this->assertTrue($result == null);
	}
	
	function testgetImage(){
		$path = "PfadZuGroup";
		$text = "THMGroupsTest";
		$cssc = "TestCSSC";
		
		$result = $this->instance->getImage($path, $text, $cssc);
		$expected = "<img src=\"\" alt=\"THMGroupsTest\" class=\"TestCSSC\" />";
		$this->assertTrue($result == $expected);
	}
	
	function testgetLink(){
		$path = "PfadZuGroup";
		$text = "THMGroupsTest";
		$cssc = "TestCSSC";
	
		$result = $this->instance->getLink($path, $text, $cssc);
		$expected = "<a class=\"TestCSSC\" href=\"PfadZuGroup\" target=\"_blank\">THMGroupsTest</a>";
		$this->assertTrue($result == $expected);
	}
	
	function testgetUnsortedRoles() {
		$db =& JFactory::getDBO();
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
	
	function testcanEdit() {
		$user->id = "1337";
		$user->username = "THMTest";
		$user->name = "THM";
		$user->email = "test@test.de";
		$user->usertype = "Administrator";
		$user->guest = "false";
		
		$result = $this->instance->canEdit();
		
		var_dump($result);
	}
}

?>