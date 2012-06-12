<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'framework_include.php';

class AllComThmGroupsAdminTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Component THM Groups admin Test');

		$suite->addTestFile(__DIR__.'/_membermanagerTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelAddGroupAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelAddRoleAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelAddStructureAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModeleditAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelEditGroupAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelEditRoleAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelEditStructureAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelGroupmanagerAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelmembermanagerAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelRolemanagerAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelStructureAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsControllerAddGroupTest.php');
		$suite->addTestFile(__DIR__.'/ConfDBTest.php');
		$suite->addTestFile(__DIR__.'/MemberManagerDBTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsViewTHMGroupsTest.php');
		$suite->addTestFile(__DIR__.'/SQLAbstractionLayerTest.php');

		return $suite;
	}
}
?>