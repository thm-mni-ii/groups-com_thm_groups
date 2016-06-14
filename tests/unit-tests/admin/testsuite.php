<?php
/**
 * @category    Joomla component
 *
 * @package     THM_Groups
 *
 * @subpackage  com_thm_groups
 * @name        Testsuite
 * @description testsuite from admin com_thm_groups
 * @author      Dennis Priefer, dennis.priefer@mni.thm.de
 * @author      Niklas Simonis, niklas.simonis@mni.thm.de
 *
 * @copyright   2012 TH Mittelhessen
 *
 * @license     GNU GPL v.2
 * @link        www.thm.de
 * @version     3.0
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'framework_include.php';

/**
 * Testsuite class for admin component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.thm.de
 * @codeCoverageIgnore
 */
class AllComThmGroupsAdminTests
{
	/**
	 * Testsuite
	 *
	 * @return suite
	 */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Component THM Groups admin Test');

		if (!defined('JPATH_COMPONENT'))
		{
			define('JPATH_COMPONENT', JPATH_BASE . '/administrator/components/com_thm_groups');
		}

		$suite->addTestFile(__DIR__ . '/THMGroupsModelAddGroupAdminTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelAddRoleAdminTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelAddStructureAdminTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModeleditAdminTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelEditGroupAdminTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelEditRoleAdminTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelEditStructureAdminTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelGroupmanagerAdminTest.php');

		return $suite;
	}
}
