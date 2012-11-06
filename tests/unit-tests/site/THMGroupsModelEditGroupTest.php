<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/editgroup.php';

class THMGroupsModelEditGroupTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

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
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}
	
	/*
	 * No tests:
	 * store
	 * getForm
	*/
	
	// tests function getData() and _buildQuery()
	// first object = Registered
	function testgetData(){
		$result = $this->instance->getData();
		
		$this->assertEquals($result[0]->id,"2");
		$this->assertEquals($result[0]->name,"Registered");
		$this->assertEquals($result[0]->info,' ');
		$this->assertEquals($result[0]->picture,' ');
		$this->assertEquals($result[0]->mode,' ');
		$this->assertEquals($result[0]->injoomla,"1");
	}
	
	// tests function updatePic
	// insert value in database
	// updatPic() creates pictures and update inserted value
	// delete database entry
	function testupdatePic(){
		
		$db = JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_groups (id, name, info, picture, mode, injoomla)";
		$query .= "VALUES ('99999','THMGroupsTest','TestSuite','','','1')";
		$db->setQuery( $query );
		$db->query();
		
		$picField = null;
		$result = $this->instance->updatePic("99999",$picField);
		//var_dump($result);
		$expected = false; // because of $picField = null
		
		$query = "DELETE FROM #__thm_groups_groups WHERE id = '99999'";
		$db->setQuery( $query);
		$db->query();
		
		$this->assertEquals($result,$expected);
	}
	
	// tests delPic() function
	// function updates entry with GID = 1
	function testdelPic(){
		$gid = "1";
		$db = JFactory::getDBO();
		
		$result = $this->instance->delPic();
		$expected = true;
		
		$query = "UPDATE #__thm_groups_groups SET picture=NULL WHERE id = $gid ";
		$db->setQuery( $query );
		$db->query();
		
		$this->assertEquals($result,$expected);
	}
	
	// tests getParentID
	// getParentID use $array['gsgid'] = '2';
	// should return GSGID = 1 form Public Group
	function testgetParentIdPublic(){
		// GSGID get ID from Group Registered
		$array['gsgid'] = '2';
		JRequest::set($array, 'post');

		$result = $this->instance->getParentId();
		$expected = "1"; // ID from Group Public
		$this->assertEquals($expected, $result);
	}
	
	// tests getAllGroups
	// getAllGroups use $array['gid'] = '1';
	// should return objectlist, sorted by id
	// first object should be Public Group
	function testgetAllGroups(){
		$result = $this->instance->getAllGroups();
	
		$this->assertEquals($result[0]->id,"1");
		$this->assertEquals($result[0]->parent_id,"0");
		$this->assertEquals($result[0]->lft,"1");
		$this->assertEquals($result[0]->title,"Public");
	}
	
}

?>