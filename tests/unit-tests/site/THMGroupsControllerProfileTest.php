<?php

require_once JPATH_BASE.'/components/com_thm_groups/controllers/profile.php';

class THMGroupsControllerProfileTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	protected $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		//--- set test user
		JRequest :: setVar('option', 'com_thm_groups');
		JRequest :: setVar('layout', 'default');
		JRequest :: setVar('view', 'profile');
		JRequest :: setVar('Itemid', 40);
		$uri = 'http://fredbloggs:itsasecret@www.example.com:8080/path/to/Joomla/index.php?task=view&id=32#anchorthis';
		$u =& JURI::getInstance( $uri );
		echo 'Before: ' . $u->toString() . "\n";
		$u->setPath( '/administrator/components/com_contact/controller.php' );
		$this->instance = new THMGroupsControllerProfile();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		// delete your instance
		$db = JFactory :: getDBO();
		$query = "DELETE FROM `#__users` WHERE `id` = 99999";
		$db->setQuery($query);
		$db->query();
		
		$query = "DELETE FROM `#__thm_groups_groups_map` WHERE `uid` = 99999 AND `gid` = 66 AND `rid` = 1;";
		$db->setQuery($query);
		$db->query();
		
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}
	/*
	// test the getLink() function
	function testGetLink() {
		$result = $this->instance->getLink();
		//var_dump($result);
		//$expected = array("thm_groups" => "THM Gruppen");
		//$this->assertEquals($expected, $result);
		$this->assertTrue(true);
	}	
	
	// test the backToRefUrl()
	function testBackToRefUrl() {
		$username = "testusername";
		$result = $this->instance->getSurname($username);
		$this->assertFalse($result);
	}*/
}

?>