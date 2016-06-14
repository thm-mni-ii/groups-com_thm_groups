<?php

require_once JPATH_BASE . '/components/com_thm_groups/views/advanced/view.html.php';

class THMGroupsViewAdvancedTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	protected $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp()
	{
		$this->instance = new THMGroupsViewAdvanced;
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown()
	{
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}

	// test the make_table($data) function
	/*
	function testMake_table() {
		$tableArr = array(
						0 => array(
								'testkey1' => 'testvalue1',
								'testkey2' => 'testvalue2'
							)
					);
		$tableJson = json_encode($tableArr);
		$result = $this->instance->make_table($tableJson);
		$expected = "<table class='table'><tr><th>testkey1</th><th>testkey2</th></tr><tr><td>testvalue1</td><td>testvalue2</td></tr></table>";
		$this->assertEquals($expected, $result);
	}
	*/
}

?>