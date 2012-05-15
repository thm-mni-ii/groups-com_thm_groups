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
		$db =& JFactory::getDBO();
		
		// Set testrow in table #__thm_groups_conf
		$query = "INSERT INTO #__thm_groups_conf (name, value) ".
				 "VALUES ('test_name','Test_Value')";
		$db->setQuery( $query );
		$db->query();
		
		// Set testgroup in table #__thm_groups_groups
		$query = "INSERT INTO `#__thm_groups_groups` (`id`, `name`, `info`, `picture`, `mode`, `injoomla`) ".
				 "VALUES (99999, 'testgroup', 'testinfo', 'testpicture.jpg', 'testmode', '1')";
		$db->setQuery( $query );
		$db->query();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__thm_groups_conf WHERE name = 'test_name'";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_groups WHERE id = 99999";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_groups WHERE id = 100000";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_roles WHERE id = 100000";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_roles WHERE name = 'testrole'";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_groups_map WHERE uid = 99999";
		$db->setQuery( $query);
		$db->query();
		// delete your instance
		unset($this->instance);
	}
	
	// test method getValue($name)
	function testGetValue() {
		$result = $this->instance->getValue('test_name');
		$expected = 'Test_Value';
		$this->assertEquals($expected, $result);
	}
	
	// test method getValue($name) with wrong $expected
	function testGetValue2() {
		$result = $this->instance->getValue('test_name');
		$expected = 'Wrong_Test_Value';
		$this->assertFalse($expected == $result);
	}
	
	// test method setValue($name, $value)
	function testSetValue() {
		$this->instance->setValue('test_name', 'test_value_update');
		$result = $this->instance->getValue('test_name');
		$expected = 'test_value_update';
		$this->assertEquals($expected, $result);
	}
	
	// test method getfreeGroupIDs()
	public function testGetfreeGroupIDs(){
		$resultArray = $this->instance->getfreeGroupIDs();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == 99999)
			{
				$result = true;
			}
		}
		$this->assertTrue($result);
	}
	
	// test method getfullGroupIDs()
	public function testGetfullGroupIDs(){
		$resultArray = $this->instance->getfullGroupIDs();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == 1)
			{
				$result = true;
			}
		}
		$this->assertTrue($result);
	}
	
	// test method getUserCountFromGroup($gid)
	public function testGetUserCountFromGroup(){
		// Set testrow in table #__thm_groups_groups_map
		$db =& JFactory::getDBO();
		$query = "INSERT INTO `#__thm_groups_groups_map` (`uid`, `gid`, `rid`) VALUES (99999, 99999, 1)";
		$db->setQuery( $query );
		$db->query();
		
		$gid = '99999';
		$result = $this->instance->getUserCountFromGroup($gid);
		$this->assertNotNull($result);
	}
	
	// test method delGroup($gid)
	public function testDelGroup(){
		$db =& JFactory::getDBO();
		// set testgroup in table #__thm_groups_groups
		$query = "INSERT INTO `#__thm_groups_groups` (`id`, `name`, `info`, `picture`, `mode`, `injoomla`) ".
				 "VALUES (100000, 'testgroup', 'testinfo', 'testpicture.jpg', 'testmode', '1')";
		$db->setQuery( $query );
		$db->query();
		
		// get testgroup in table #__thm_groups_groups
		$query = "SELECT * FROM `#__thm_groups_groups` WHERE `id` = 100000";
		$db->setQuery( $query );
		$item = $db->loadObject();
		if (!isset($item))
			$this->assertTrue(false);
		$gid = '100000';
		$result = $this->instance->delGroup($gid);
		
		// get testgroup in table #__thm_groups_groups
		$query = "SELECT * FROM `#__thm_groups_groups` WHERE `id` = 100000";
		$db->setQuery( $query );
		$item = $db->loadObject();
		if (isset($item))
			$this->assertTrue(false);
		else
			$this->assertTrue(true);
	}
	
	// test method delRole($rid)
	public function testDelRole(){
		$db =& JFactory::getDBO();
		// set testgroup in table #__thm_groups_groups
		$query = "INSERT INTO `#__thm_groups_roles` (`id`, `name`) ".
				 "VALUES (100000, 'testrole')";
		$db->setQuery( $query );
		$db->query();
		
		// get test role in table #__thm_groups_roles
		$query = "SELECT * FROM `#__thm_groups_roles` WHERE `id` = 100000";
		$db->setQuery( $query );
		$item = $db->loadObject();
		if (!isset($item))
			$this->assertTrue(false);
		$rid = '100000';
		$result = $this->instance->delRole($rid);
		
		// get test role in table #__thm_groups_roles
		$query = "SELECT * FROM `#__thm_groups_roles` WHERE `id` = 100000";
		$db->setQuery( $query );
		$item = $db->loadObject();
		if (isset($item))
			$this->assertTrue(false);
		else
			$this->assertTrue(true);
	}
	
	// test method addRole($name)
	public function testAddRole(){
		$result = $this->instance->addRole('testrole');
		$db =& JFactory::getDBO();
		// get test role in table #__thm_groups_roles
		$query = "SELECT * FROM `#__thm_groups_roles` WHERE `name` = 'testrole'";
		$db->setQuery( $query );
		$item = $db->loadObject();
		if (!isset($item))
			$this->assertTrue(false);
		else
			$this->assertTrue(true);
	}

	/*// test method setTitle($gid,$title)
	public function testSetTitle(){
		$this->assertTrue(true);
	}
	
	// test method setDescription()
	public function testSetDescription(){
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
	
	// test method sync()
	public function testSync(){
		$this->assertTrue(true);
	}
	
	// test method addGroup($name,$alias,$title,$show_title,$description,$show_description,$numColumns)
	public function testAddGroup(){
		$this->assertTrue(true);
	}
	
	// test method getUserCount()
	public function testGetUserCount(){
		$this->assertTrue(true);
	}
	
	
	*/
}

?>