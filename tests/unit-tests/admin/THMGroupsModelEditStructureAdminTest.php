<?php

require_once JPATH_BASE.'/administrator/components/com_thm_groups/models/editstructure.php';

class THMGroupsModelEditStructureAdminTest extends PHPUnit_Framework_TestCase
{
    protected $instance;

    // PHPUnit_TestCase funtcion - overwritten
    function setUp() {
        $this->instance = new THMGroupsModelEditStructure();
    }

    // Kill instance
    function tearDown() {
        // "benutztes" Objekt entfernen
        $this->instance = null;
        // tearDown der Elternklasse aufrufen
        parent::tearDown();
    }

    /*
     * No tests:
    * getData
    * store
    */

    // tests _buildQuery()
    // function returns an SQL query
    // should start with SELECT
    function test_buildQuery(){
        $result = $this->instance->_buildQuery();
        $expected = "SELECT ";
        $this->assertContains($expected, $result);
    }

    // tests getItem
    // function returns object from database
    // should: id = 1 , field = Vorname
    function testgetItem(){
        $array['cid'] = '1';
        JRequest::set($array, 'post');
        $result = $this->instance->getItem();
        $this->assertTrue($result->id == "1");
        $this->assertTrue($result->field == "Vorname");
    }
}

?>