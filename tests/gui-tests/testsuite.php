<?php
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'framework_include.php';
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class AllComThmGroupsGuiTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Component THM Groups GUI Test');

		$suite->addTestFile(__DIR__.'/com_thm_groups__administration_home__en_gb.php');
		$suite->addTestFile(__DIR__.'/com_thm_groups__groupmanager__en_gb.php');
		$suite->addTestFile(__DIR__.'/com_thm_groups__home__en_gb.php');
		$suite->addTestFile(__DIR__.'/com_thm_groups__membermanager_1__en_gb.php');
		//$suite->addTestFile(__DIR__.'/com_thm_groups__membermanager_2__en_gb.php');
		$suite->addTestFile(__DIR__.'/com_thm_groups__rolemanager__en_gb.php');
		//$suite->addTestFile(__DIR__.'/com_thm_groups__structure__en_gb.php');

		return $suite;
	}
}

?>