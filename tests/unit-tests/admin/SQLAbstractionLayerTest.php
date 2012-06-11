<?php
require_once JPATH_BASE.'/administrator/includes/toolbar.php';
require_once JPATH_BASE.'/administrator/components/com_thm_groups/classes/confdb.php';

class SQLAbstractionLayerTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	var $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		$this->instance = new SQLAbstractionLayer();
		$db =& JFactory::getDBO();
		// Set test group in table #__thm_groups_groups
		$query = "INSERT INTO `#__thm_groups_groups` (`id`, `name`, `info`, `picture`, `mode`, `injoomla`) ".
				 "VALUES (99999, 'testgroup', 'testinfo', 'testpicture.jpg', 'testmode', '1')";
		$db->setQuery( $query );
		$db->query();
		
		// Set test group in table #__thm_groups_groups
		$query = "INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) ".
				"VALUES (99999, '0', '71', '72', 'testgroup')";
		$db->setQuery( $query );
		$db->query();
		
		// set test role in table #__thm_groups_roles
		$query = "INSERT INTO `#__thm_groups_roles` (`id`, `name`) ".
				 "VALUES (99999, 'testrole')";
		$db->setQuery( $query );
		$db->query();
		
		// Set testrow in table #__thm_groups_groups_map
		$query = "INSERT INTO `#__thm_groups_groups_map` (`uid`, `gid`, `rid`) VALUES (99999, 99999, 1)";
		$db->setQuery( $query );
		$db->query();
		
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__thm_groups_groups WHERE id = 99999";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_groups WHERE id = 100001";
		$db->setQuery( $query);
		$db->query();
		
		
		$query = "DELETE FROM #__usergroups WHERE id = 99999";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_roles WHERE id = 99999";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_groups_map WHERE uid = 99999";
		$db->setQuery( $query);
		$db->query();
		
		$query = "DELETE FROM #__thm_groups_groups_map WHERE uid = 100000";
		$db->setQuery( $query);
		$db->query();
		
		// delete your instance
		unset($this->instance);
	}
	
	// test method setDbData($query)
	function testSetDbData() {
		$query = "SELECT * FROM `#__thm_groups_groups` WHERE `id` = 100000";
		$result = $this->instance->setDbData($query);
		$this->assertTrue($result);
	}
	
	/* Doesn't work
	// test method setDbData($query) with wrong query, should return false
	function testSetDbData2() {
		$query = "TEST_NO_SQL_QUERY";
		$result = $this->instance->setDbData($query);
		$this->assertFalse($result);
	}
	*/
	
	// test method getGroups()
	function testGetGroups() {
		$resultArray = $this->instance->getGroups();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == '99999')
				$result = true;
		}
		$this->assertTrue($result);
	}
	
	// test method getJoomlaGroups()
	function testGetJoomlaGroups() {
		$resultArray = $this->instance->getJoomlaGroups();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == '99999')
				$result = true;
		}
		$this->assertTrue($result);
	}
	
	// test method getGroupsHirarchy()
	function testGetGroupsHirarchy() {
		$resultArray = $this->instance->getGroupsHirarchy();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == '99999')
				$result = true;
		}
		$this->assertTrue($result);
	}
	
	// test method getRoles()
	function testGetRoles() {
		$resultArray = $this->instance->getRoles();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == '99999')
				$result = true;
		}
		$this->assertTrue($result);
	}
	
	// test method getGroupsAndRoles($uid)
	function testGetGroupsAndRoles() {
		$uid = '99999';
		$resultArray = $this->instance->getGroupsAndRoles($uid);
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->groupid == '99999')
				$result = true;
		}
		$this->assertTrue($result);
	}
	
	// test method getGroupRolesByUser($uid, $gid)
	function testGetGroupRolesByUser() {
		$uid = '99999';
		$gid = '99999';
		
		$resultArray = $this->instance->getGroupRolesByUser($uid, $gid);
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->rid == '1')
				$result = true;
		}
		$this->assertTrue($result);
	}
	
	// test method setGroupsAndRoles($uids, $gid, $rid)
	function testSetGroupsAndRoles() {
		$uid = array( 
				0 => '100000'
				);
		$gid = '100000';
		$rid = '1';
		
		$return = $this->instance->setGroupsAndRoles($uid, $gid, $rid);
		if(!$return)
			$this->assertTrue(false);
		
		$resultArray = $this->instance->getGroupRolesByUser($uid[0], $gid);
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->rid == '1')
				$result = true;
		}
		$this->assertTrue($result);
	}
	
	// test method delGroupsAndRoles($uids, $gid, $rid)
	function testDelGroupsAndRoles() {
		$uid = array( 
				0 => '100000'
				);
		$gid = '100000';
		$rid = '1';
		
		// set test entry
		$return = $this->instance->setGroupsAndRoles($uid, $gid, $rid);
		
		// check if error arised through setting test entry
		if(!$return)
			$this->assertTrue(false);
		
		// check, if entry is set correct
		$resultArray = $this->instance->getGroupRolesByUser($uid[0], $gid);
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if (!($resultItem->rid == '1'))
				$this->assertTrue(false);
		}
		
		// call method to delete test entry
		$resultArray = $this->instance->delGroupsAndRoles($uid, $gid, $rid);
		
		// check, if test entry is deleted
		$resultArray = $this->instance->getGroupRolesByUser($uid[0], $gid);
		$result = true;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->rid == '1')
				$result = false;
		}
		$this->assertTrue($result);
	}
	
	// test method setGroup($object, $insert = false), insert mode
	function testSetGroupInsert() {
		$groupObject = array(
						0 => array(
							'id'		=> 100001,
							'name'		=> "'testgroup'", 
							'info'		=> "'testinfo'",
							'picture'	=> "'testpicture.jpg'",
							'mode'		=> "'testmode'",
							'injoomla'	=> 0 ,
						)
					);
		
		$return = $this->instance->setGroup($groupObject, true);
		
		// check if error arised through setting test entry
		if(!$return)
			$this->assertTrue(false);
		
		// check, if entry is set correct
		$resultArray = $this->instance->getGroups();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == '100001')
				$result = true;
		}		
		$this->assertTrue($result);
	}
	
	// test method setGroup($object, $insert = false), update mode
	function testSetGroupUpdate() {
		$groupObject = array(
				0 => array(
						'id'		=> 100001,
						'name'		=> "'testgroup'",
						'info'		=> "'testinfo'",
						'picture'	=> "'testpicture.jpg'",
						'mode'		=> "'testmode'",
						'injoomla'	=> 0 ,
				)
		);
		
		// set test group with method setGroup
		$return = $this->instance->setGroup($groupObject, true);
	
		// check if error arised through setting test entry
		if(!$return)
			$this->assertTrue(false);
		
		// check, if entry is set correct
		$resultArray = $this->instance->getGroups();
		$result = false;
		foreach ($resultArray as $resultItem)
		{
			if (!($resultItem->id == '100001'))
				$result = false;
		}
		
		$groupObject = array(
				0 => array(
						'id'		=> 100001,
						'name'		=> "'testnameUpdate'",
						'info'		=> "'testinfo'",
						'picture'	=> "'testpicture.jpg'",
						'mode'		=> "'testmode'",
						'injoomla'	=> 0 ,
				)
		);
		
		// update test group with method setGroup
		$return = $this->instance->setGroup($groupObject);
		
		// check if error arised through updating test entry
		if(!$return)
			$this->assertTrue(false);
	
		// check, if entry is set correct
		$resultArray = $this->instance->getGroups();
		foreach ($resultArray as $resultItem)
		{
			if ($resultItem->id == '100001')
				if($resultItem->name == 'testnameUpdate')
					$result = true;
		}
		$this->assertTrue($result);
	}
}

?>