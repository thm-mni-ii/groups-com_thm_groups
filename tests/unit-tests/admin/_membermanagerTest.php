<?php

define('JPATH_COMPONENT_ADMINISTRATOR',JPATH_ROOT . '/administrator/components/com_thm_groups');
define('JPATH_COMPONENT',JPATH_ROOT . '/administrator/components/com_thm_groups');


require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/_membermanager.php';
require_once 'PHPUnit.php';

class _membermanagerTest extends PHPUnit_TestCase
{
	var $instance;

	// constructor of the test suite
	function _membermanagerTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new staffsModelmembermanager();
	}

	// Kill instance
	function tearDown() {
		//unset($this->instance);
	}

	function test(){
		
	}
	
}

?>