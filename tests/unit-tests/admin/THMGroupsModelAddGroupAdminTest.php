<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelAddGroupAdminTest
 *@description THMGroupsModelAddGroupAdminTest from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

require_once JPATH_BASE . '/administrator/components/com_thm_groups/models/addgroup.php';

/**
 * THMGroupsModelAddGroupAdminTest class for admin component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 3.0
 */
class THMGroupsModelAddGroupAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	/**
	 * set up function before tests
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->instance = new THMGroupsModelAddGroup;
	}

	/**
	 * kill function after tests
	 *
	 * @return void
	 */
	public function tearDown()
	{
		$this->instance = null;
		parent::tearDown();
	}

	/**
	 * tests updatePic,
	 * inserts an value in database
	 * function sholud update value
	 * but picFile is null, function return false
	 *
	 * @return void
	 */
	public function testupdatePic()
	{
		$db = JFactory::getDBO();
		$query = "INSERT INTO  #__thm_groups_groups(`id` ,`name` ,`info` ,`picture` ,`mode` ,`injoomla`)
					VALUES ('99999',  'THMGroupsSuite',  'Test',  'test.jpg', NULL ,  '1')";
		$db->setQuery($query);
		if ($db->query())
		{
			$insert = true;
		}
		else
		{
			$insert = false;
		}

		$picField = null;
		$result = $this->instance->updatePic("99999", "88888", $picField);
		$expected = false;

		$sql = "DELETE FROM #__thm_groups_groups WHERE id = '99999'";
		$db->setQuery($sql);
		if ($db->query())
		{
			$delete = true;
		}
		else
		{
			$delete = false;
		}
		$this->assertEquals($insert, $delete);
		$this->assertEquals($expected, $result);
	}

	/**
	 * tests getExtra,
	 * insert value in database
	 * returns value vom inserted query (THMGroupsTest.jpg)
	 * 
	 * @return void
	 */
	public function testgetExtra()
	{
		$structid = "999999";
		$type = "picture";

		$db = JFactory::getDBO();
		$sql = "INSERT INTO #__thm_groups_picture_extra (structid, value)";
		$sql .= "VALUES ('999999' ,'THMGroupsTest.jpg')";
		$db->setQuery($sql);
		if ($db->query())
		{
			$insert = true;
		}
		else
		{
			$insert = false;
		}

		$result = $this->instance->getExtra($structid, $type);

		$sql = "DELETE FROM #__thm_groups_picture_extra WHERE structid = '999999'";
		$db->setQuery($sql);
		if ($db->query())
		{
			$delete = true;
		}
		else
		{
			$delete = false;
		}
		$this->assertEquals($insert, $delete);
		$expected = "THMGroupsTest.jpg";
		$this->assertEquals($expected, $result);
	}

	/**
	 * tests getAllGroups
	 * returns objectlist with all groups
	 * first Object must be Public
	 *
	 * @return void
	 */
	public function testgetAllGroups()
	{
		$result = $this->instance->getAllGroups();
		$expected = "Public";
		$this->assertTrue($result[0]->title == $expected);
	}
}
