<?php
/**
 * @category    Joomla component
 *
 * @package     THM_Groups
 *
 * @subpackage  com_thm_groups
 * @name        THMGroupsModelAddRoleAdminTest
 * @description THMGroupsModelAddRoleAdminTest from com_thm_groups
 * @author      Dennis Priefer, dennis.priefer@mni.thm.de
 * @author      Niklas Simonis, niklas.simonis@mni.thm.de
 *
 * @copyright   2012 TH Mittelhessen
 *
 * @license     GNU GPL v.2
 * @link        www.thm.de
 * @version     3.0
 */

require_once JPATH_BASE . '/administrator/components/com_thm_groups/models/addrole.php';

/**
 * THMGroupsModelAddRoleAdminTest class for admin component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.thm.de
 */
class THMGroupsModelAddRoleAdminTest extends PHPUnit_Framework_TestCase
{
	protected $instance;

	/**
	 * set up function before tests
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->instance = new THMGroupsModelAddRole;
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
	 * tests store
	 * function inserts value in database
	 * should return true
	 * delete value
	 *
	 * @return void
	 */
	public function teststore()
	{

		$array['role_name'] = 'THMGroupsTestSuite';
		JRequest::set($array, 'post');

		$result   = $this->instance->store();
		$expected = true;

		$db    = JFactory::getDBO();
		$query = "DELETE FROM #__thm_groups_roles WHERE name = 'THMGroupsTestSuite'";
		$db->setQuery($query);
		if ($db->query())
		{
			$delete = true;
		}
		else
		{
			$delete = false;
		}
		$this->assertTrue($delete == true);
		$this->assertTrue($result == $expected);
	}
}
