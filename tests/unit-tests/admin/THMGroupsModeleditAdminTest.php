<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModeleditAdminTest
 *@description THMGroupsModeleditAdminTest from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

require_once JPATH_BASE . '/administrator/components/com_thm_groups/models/edit.php';

/**
 * THMGroupsModeleditAdminTest class for admin component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 3.0
 */
class THMGroupsModeleditAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	/**
	 * set up function before tests
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->instance = new THMGroupsModeledit;
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
	 * tests getStructure
	 * returns an objectlist
	 * first object is "Titel", type -> Texts
	 *
	 * @return void
	 */
	public function testgetStructure()
	{
		$result = $this->instance->getStructure();
		$expected = "TEXT";
		$this->assertEquals($expected, $result[0]->type);
	}

	/**
	 * tests getTypes
	 * function returns objectlist
	 * first object type should be "LINK"
	 *
	 * @return void
	 */
	public function testgetTypes()
	{
		$result = $this->instance->getTypes();
		$this->assertTrue($result[0]->Type != null);
	}

	/**
	 * tests updatePic
	 * inserts an value in database
	 * function sholud update value
	 * but picFile is null, function return false
	 *
	 * @return void
	 */
	public function testupdatePic()
	{
		$db = JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_picture (userid, structid, value, publish, group)";
		$query .= "VALUES ('99999','88888',' ','0','0')";
		$db->setQuery($query);
		$db->query();

		$picField = null;
		$result = $this->instance->updatePic("99999", "88888", $picField);
		$expected = false;

		$sql = "DELETE FROM #__thm_groups_picture WHERE userid = '99999'";
		$db->setQuery($sql);
		$db->query();

		$this->assertEquals($expected, $result);
	}

	/**
	 * tests delPic
	 * insert an value in database
	 * function updates the value and returns true
	 * 
	 * @return void
	 */
	public function testdelPic()
	{
		$array['userid'] = '99999';
		$array['structid'] = '88888';
		JRequest::set($array, 'post');

		$db = JFactory::getDBO();
		$query = "INSERT INTO #__thm_groups_picture (userid, structid, value, publish, group)";
		$query .= "VALUES ('99999','88888',' ','0','0')";
		$db->setQuery($query);
		$db->query();

		$result = $this->instance->delPic();
		$expected = true;

		$sql = "DELETE FROM #__thm_groups_picture WHERE userid = '99999'";
		$db->setQuery($sql);
		$db->query();
		$this->assertEquals($expected, $result);
	}

	/**
	 * tests getExtra
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
		$db->query();

		$result = $this->instance->getExtra($structid, $type);

		$sql = "DELETE FROM #__thm_groups_picture_extra WHERE structid = '999999'";
		$db->setQuery($sql);
		$db->query();

		$expected = "THMGroupsTest.jpg";
		$this->assertEquals($expected, $result);
	}

	/**
	 * tests getForm
	 * returns JForm object
	 * 
	 * @return void
	 */
	public function testgetForm()
	{
		$result = $this->instance->getForm();
		$options = '';
		$expected = new JForm($options);
		$this->assertNotSame($expected, $result);
	}
}
