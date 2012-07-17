<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelAddStructureAdminTest
 *@description THMGroupsModelAddStructureAdminTest from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

if (!defined('JPATH_COMPONENT'))
{
	define('JPATH_COMPONENT', JPATH_ROOT . '/administrator/components/com_thm_groups');
}
require_once JPATH_BASE . '/administrator/components/com_thm_groups/models/addstructure.php';

/**
 * THMGroupsModelAddStructureAdminTest class for admin component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 3.0
 */
class THMGroupsModelAddStructureAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	/**
	 * set up function before tests
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->instance = new THMGroupsModelAddStructure;
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
	 * tests _buildQuery
	 * function returns an SQL query
	 * should start with SELECT
	 *
	 * @return void
	 */
	public function test_buildQuery()
	{
		$result = $this->instance->_buildQuery();
		$expected = "SELECT ";
		$this->assertContains($expected, $result);
	}

	/**
	 * tests getData
	 * function returns array
	 * first object should be Type "Date"
	 * 
	 * @return void
	 */
	public function testgetData()
	{
		$result = $this->instance->getData();
		$this->assertTrue($result[0]->Type == "DATE");
	}

	/**
	 * tests store
	 * function inserts value in database
	 * sholud return true
	 *
	 * @return void
	 */
	public function teststore()
	{

		$array['name'] = 'THMGroupsTestSuite';
		$array['relation'] = 'TEXT';
		JRequest::set($array, 'post');

		$result = $this->instance->store();
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__thm_groups_structure WHERE field = 'THMGroupsTestSuite'";
		$db->setQuery($query);
		$db->query();

		$this->assertTrue($result);
	}
}
