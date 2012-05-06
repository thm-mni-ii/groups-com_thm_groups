<?php
require_once JPATH_BASE.'/administrator/includes/toolbar.php';
require_once JPATH_BASE.'/administrator/components/com_thm_groups/classes/confdb.php';
require_once 'PHPUnit.php';

class ConfDBTest extends PHPUnit_TestCase
{
	// contains the object handle of the string class
	var $instance;

	// constructor of the test suite
	function ConfDBTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		$this->instance = new ConfDB();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		// delete your instance
		
		unset($this->instance);
	}
	
	// test method setValue($name, $value)
	function testSetValue() {
		$this->assertTrue(true);
	}
	
	// test method getValue($name)
	function testGetValue() {
		$this->assertTrue(true);
	}
	
	// test method setTitle($gid,$title)
	public function testSetTitle(){
		$this->assertTrue(true);
	}
	
	// test method setDescription()
	public function testSetDescription(){
		$this->assertTrue(true);
	}
	
	// test method setQuery($query)
	public function testSetQuery(){
		$this->assertTrue(true);
	}
	
	// test method setNumColumns($gid,$numColumns)
	public function testSetNumColumns(){
		$this->assertTrue(true);
	}
	
	// test method setTitleVisible($gid)
	public function testSetTitleVisible(){
		$this->assertTrue(true);
	}
	
	// test method setTitleInvisible($gid)
	public function testSetTitleInvisible(){
		$this->assertTrue(true);
	}
	
	// test method setDescriptionVisible($gid)
	public function testSetDescriptionVisible(){
		$this->assertTrue(true);
	}
	
	// test method setDescriptionInvisible($gid)
	public function testSetDescriptionInvisible(){
		$this->assertTrue(true);
	}
	
	// test method getDescriptionState($gid)
	public function testGetDescriptionState(){
		$this->assertTrue(true);
	}
	
	// test method getTitleState($gid)
	public function testGetTitleState(){
		$this->assertTrue(true);
	}
	
	// test method getNumColumns($gid)
	public function testGetNumColumns(){
		$this->assertTrue(true);
	}
	
	// test method getDescription($gid)
	public function testGetDescription(){
		$this->assertTrue(true);
	}
	
	// test method getTitle($gid)
	public function testGetTitle(){
		$this->assertTrue(true);
	}
	
	// test method isGroupFree($gid)
	public function testIsGroupFree(){
		$this->assertTrue(true);
	}
	
	// test method getfreeGroupIDs()
	public function testGetfreeGroupIDs(){
		$this->assertTrue(true);
	}
	
	// test method getfullGroupIDs()
	public function testGetfullGroupIDs(){
		$this->assertTrue(true);
	}
	
	// test method getUserCountFromGroup($gid)
	public function testGetUserCountFromGroup(){
		$this->assertTrue(true);
	}
	
	// test method delGroup($gid)
	public function testDelGroup(){
		$this->assertTrue(true);
	}
	
	// test method delRole($rid)
	public function testDelRole(){
		$this->assertTrue(true);
	}
	
	// test method addGroup($name,$alias,$title,$show_title,$description,$show_description,$numColumns)
	public function testAddGroup(){
		$this->assertTrue(true);
	}
	
	// test method addRole($name)
	public function testAddRole(){
		$this->assertTrue(true);
	}
	
	// test method getUserCount()
	public function testGetUserCount(){
		$this->assertTrue(true);
	}
	
	// test method sync()
	public function testSync(){
		$this->assertTrue(true);
	}
}

?>