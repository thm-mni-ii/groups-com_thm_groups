<?php
/**
 * @version     v3.1.0
 * @category    Joomla component
 * @package     THM_Groups_Tests
 * @subpackage  com_thm_groups.admin
 * @name        Testsuite
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Mariusz Homeniuk, <mariusz.homeniuk@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'framework_include.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'JoomlaSeleniumTest.php';

/**
 * AllComThmGroupsGuiTests is the class to add compile the Tests
 *
 * @category    Joomla.Component.Admin
 * @package     thm_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.thm.de
 */
class AllComThmGroupsGuiTests
{
    /**
     * Testsuite
     *
     * @return suite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Component THM Groups GUI Test');

        $suite->addTestFile(__DIR__ . '/admin/com_thm_groups_structmanager.php');
        $suite->addTestFile(__DIR__ . '/admin/com_thm_groups_structmanager_add_entry.php');
        $suite->addTestFile(__DIR__ . '/admin/com_thm_groups__administration_home__en_gb.php');

        return $suite;
    }
}
