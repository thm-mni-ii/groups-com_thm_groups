<?php

require_once JPATH_BASE . '/components/com_thm_groups/models/edit.php';

class THMGroupsModeleditTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp()
	{
		$this->instance = new THMGroupsModeledit();
	}

	// Kill instance
	function tearDown()
	{
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}

	/*
	 * No tests:
	 * getModerator -> simulate user
	 * store
	 */

	// tests delPic
	// insert an value in database
	// function updates the value and returns true
	function testdelPic()
	{
		$array['userid']   = '99999';
		$array['structid'] = '88888';
		JRequest::set($array, 'post');

		$db    = JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_picture (userid, structid, value, publish, group)";
		$query .= "VALUES ('99999','88888',' ','0','0')";
		$db->setQuery($query);
		$db->query();

		$result   = $this->instance->delPic();
		$expected = true;

		$sql = "DELETE FROM #__thm_groups_picture WHERE userid = '99999'";
		$db->setQuery($sql);
		$db->query();

		$this->assertEquals($expected, $result);
	}

	// tests updatePic
	// inserts an value in database
	// function sholud update value
	// but picFile is null, function return false
	function testupdatePic()
	{

		$db    = JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_picture (userid, structid, value, publish, group";
		$query .= "VALUES ('99999','88888',' ','0','0')";
		$db->setQuery($query);
		$db->query();

		$picField = null;
		$result   = $this->instance->updatePic("99999", "88888", $picField);
		$expected = false; // because of $picField = null

		$sql = "DELETE FROM #__thm_groups_picture WHERE userid = '99999'";
		$db->setQuery($sql);
		$db->query();

		$this->assertEquals($expected, $result);
	}

	//tests getGroupNumer
	//should return 2 for Group Public
	function testgetGroupNumber()
	{
		$array['gsgid'] = '2';
		JRequest::set($array, 'post');

		$result   = $this->instance->getGroupNumber();
		$expected = "2";
		$this->assertTrue($result == $expected);
	}
}

?>