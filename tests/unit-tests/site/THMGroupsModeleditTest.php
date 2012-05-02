<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/edit.php';
require_once 'PHPUnit.php';

class THMGroupsModeleditTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModeleditTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModeledit();
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	/*
	 * Cannot test 
	 * getModerator -> simulate user
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
	
	//tests getGroupNumer
	//should return 2 for Group Public
	function testgetGroupNumber(){
		$array['gsgid'] = '2';
		JRequest::set($array, 'post');
		
		$result = $this->instance->getGroupNumber();
		$expected = "2";
		$this->assertTrue($result == $expected);
	}
}

?>