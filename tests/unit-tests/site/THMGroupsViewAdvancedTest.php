<?php

require_once JPATH_BASE.'/components/com_thm_groups/views/advanced/view.html.php';

class THMGroupsViewAdvancedTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	var $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp() {
		//--- set test user
		
		$this->instance = new THMGroupsViewAdvanced();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown() {
		// delete your instance
		
		unset($this->instance);
	}
	
	// test the make_table($data) function
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
	/*
	// test the display($tpl = null)
	function testDisplay() {
		
		//$result = $this->instance->display($tpl = null);
		//var_dump($result);
		$this->assertTrue(true);
	}*/
}

?>