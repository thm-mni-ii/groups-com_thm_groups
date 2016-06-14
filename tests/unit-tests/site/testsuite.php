<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'framework_include.php';

if (!defined('JPATH_COMPONENT'))
{
	define('JPATH_COMPONENT', dirname(__FILE__));
}

class AllComThmGroupsSiteTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Component THM Groups site Test');

		$suite->addTestFile(__DIR__ . '/THMGroupsModelAdvancedTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModeleditTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsModelEditGroupTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsModelGroupsTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelListTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsModelProfileTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsQuickpageHelpersHtmlContentadministratorTest.php');
		$suite->addTestFile(__DIR__ . '/THMGroupsQuickpageModelsArticlesTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsControllerProfileTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsViewProfileTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsViewAdvancedTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsViewEditGroupTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsViewEditTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsViewGroupsTest.php');
		//$suite->addTestFile(__DIR__.'/THMGroupsViewListTest.php');
		//$suite->addTestFile(__DIR__.'/PicTransformTest.php');

		return $suite;
	}
}

?>