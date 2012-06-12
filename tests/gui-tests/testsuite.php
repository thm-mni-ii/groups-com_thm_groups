<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'framework_include.php';

class AllComThmGroupsGuiTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Component THM Groups GUI Test');

		$suite->addTestFile(__DIR__.'/F0010.php');
		$suite->addTestFile(__DIR__.'/F01X0.php');
		$suite->addTestFile(__DIR__.'/F0210.php');
		$suite->addTestFile(__DIR__.'/F0220.php');
		$suite->addTestFile(__DIR__.'/F0240.php');
		$suite->addTestFile(__DIR__.'/F03X0.php');
		$suite->addTestFile(__DIR__.'/F0450.php');
		$suite->addTestFile(__DIR__.'/F0460.php');
		$suite->addTestFile(__DIR__.'/F04X0.php');
		$suite->addTestFile(__DIR__.'/F06X0.php');
		$suite->addTestFile(__DIR__.'/F07X0.php');
		$suite->addTestFile(__DIR__.'/F08X0.php');
		$suite->addTestFile(__DIR__.'/F10X0.php');
		$suite->addTestFile(__DIR__.'/F11X0.php');
		$suite->addTestFile(__DIR__.'/F13X0.php');
		$suite->addTestFile(__DIR__.'/F20X0.php');

		return $suite;
	}
}

?>