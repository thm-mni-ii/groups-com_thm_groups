<?php

require_once JPATH_BASE.'/components/com_thm_groups/models/list.php';

class THMGroupsModelListTest extends PHPUnit_Framework_TestCase
{
	var $instance;

	// PHPUnit_TestCase funtcion - overwritten
	function setUp() {
		$this->instance = new THMGroupsModelList();
	}

	// Kill instance
	function tearDown() {
		//unset($this->instance);
	}
	
	/*
	 * No tests:
	 * getGroupNumber -> cannot simulate Params
	 * getShowMode -> cannot simulate Params
	 * getgListAll -> getShowMode
	 * getgListAlphabet -> getShowMode
	 * getTitle -> getGroupNumber
	 * getDesc -> getGroupNumber
	 */
	
	// tests getViewParams()
	// sholud return an object with siteinfos
	function testgetViewParams(){
		$frameParams = Jfactory::getApplication(); ;
		$expected = $frameParams->getParams();
	
		$result = $this->instance->getViewParams();
		$this->assertTrue($result == $expected);
	}
}

?>