<?php

define('JPATH_COMPONENT_ADMINISTRATOR',JPATH_ROOT . '/administrator/components/com_thm_groups');
define('JPATH_COMPONENT',JPATH_ROOT . '/administrator/components/com_thm_groups');

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/_membermanager.php';

class _membermanagerTest extends PHPUnit_Framework_TestCase
{
	var $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new staffsModelmembermanager();
	}

	// Kill instance
	function tearDown() {
		//unset($this->instance);
	}
	
	/*
	 * No tests:
	 * getData
	 * getAnz
	 * getTotal
	 * getListQuery (protected)
	 * populateState (protected)
	*/

	// tests _buildQuery()
	// function returns an SQL query
	// should start with SELECT
	function test_buildQuery(){
		$result = $this->instance->_buildQuery();
		$expected = "SELECT ";
		$this->assertContains($expected, $result);
	}
	
	// tests getTotal
	// functin returns JPagination object
	// ???
	function testgetPagination(){
		$result = $this->instance->getPagination();
		$expected = new JPagination( null, 0, 20 );
		$this->assertNotSame($expected, $result);
	}
	
	/* cannot test, function protected
	function testgetListQuery(){
		$result = $this->instance->getListQuery();
		var_dump($result);
	}
	*/
	
	
}

?>