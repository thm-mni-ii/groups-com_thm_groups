<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/editgroup.php';
require_once 'PHPUnit.php';

class THMGroupsModelEditGroupTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function THMGroupsModelEditGroupTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelEditGroup();
		// GSGID get ID from Group Registered
		$array['gsgid'] = '2';
		$array['gid'] = '1';
		JRequest::set($array, 'post');
	}

	// Kill instance
	function tearDown() {
		unset($this->instance);
	}
	
	function testdelPic(){
		$gid = "1";
		$db =& JFactory::getDBO();
		
		$result = $this->instance->delPic();
		$expected = true;
		
		$query = "UPDATE #__thm_groups_groups SET picture=NULL WHERE id = $gid ";
		$db->setQuery( $query );
	
		$db->query();
		$this->assertEquals($result,$expected);
	}
	
	function testgetParentIdPublic(){
		
		$result = $this->instance->getParentId();
		$expected = "1"; // ID from Group Public
		
		$this->assertEquals($expected, $result);
	}
	
	function testgetAllGroups(){
		$result = $this->instance->getAllGroups();
	
		$this->assertEquals($result[0]->id,"1");
		$this->assertEquals($result[0]->parent_id,"0");
		$this->assertEquals($result[0]->lft,"1");
		$this->assertEquals($result[0]->rgt,"124");
		$this->assertEquals($result[0]->title,"Public");
	}
	
}

?>