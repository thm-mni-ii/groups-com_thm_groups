<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'framework_include.php';

class AllComThmGroupsAdminTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Component THM Groups admin Test');

		$suite->addTestFile(__DIR__.'/THMGroupsModelAddGroupAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelAddRoleAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelAddStructureAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModeleditAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelEditGroupAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelEditRoleAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelEditStructureAdminTest.php');
		$suite->addTestFile(__DIR__.'/THMGroupsModelGroupmanagerAdminTest.php');

		return $suite;
	}
}
?>