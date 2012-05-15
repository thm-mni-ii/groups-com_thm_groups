<?php
require_once JPATH_BASE.'/administrator/components/com_thm_groups/classes/membermanagerdb.php';
require_once 'PHPUnit.php';

class MemberManagerDBTest extends PHPUnit_TestCase
{
	// contains the object handle of the string class
	var $instance;

	// constructor of the test suite
	function MemberManagerDBTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		$this->instance = new MemberManagerDB();
		$db =& JFactory::getDBO();
		// Set testrow in table #__thm_groups_conf
		$query = "INSERT INTO #__thm_groups_additional_userdata (userid, usertype, published, injoomla) ".
				 "VALUES ('99999', 'Registered', '0', '1')";
		$db->setQuery( $query );
		$db->query();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		$db =& JFactory::getDBO();		
		$query = "DELETE FROM #__thm_groups_additional_userdata WHERE `userid` = 99999";
		$db->setQuery( $query);
		$db->query();
		// delete your instance
		unset($this->instance);
	}
	
	// test method userInJoomla()
	function testUserInJoomla() {
		$db =& JFactory::getDBO();
		// get test entry in table #__thm_groups_additional_userdata
		// $item->injoomla should be 1
		$query = "SELECT * FROM `#__thm_groups_additional_userdata` WHERE `userid` = 99999";
		$db->setQuery( $query );
		$item = $db->loadObject();
		
		// Call function userInJoomla()
		$this->instance->userInJoomla();
		
		// get test entry in table #__thm_groups_additional_userdata
		// $item->injoomla should be 0
		$query = "SELECT * FROM `#__thm_groups_additional_userdata` WHERE `userid` = 99999";
		$db->setQuery( $query );
		$item = $db->loadObject();
		$actual = $item->injoomla;
		
		$this->assertEquals(0, $actual);
	}
}

?>