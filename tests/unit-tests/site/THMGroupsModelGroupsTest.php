<?php

require_once JPATH_BASE . '/components/com_thm_groups/models/groups.php';

class THMGroupsModelGroupsTest extends PHPUnit_Framework_TestCase
{
    protected $instance;

    // PHPUnit_TestCase funtcion - overwritten
    public function setUp()
    {
        $this->instance = new THMGroupsModelGroups();
    }

    // Kill instance
    public function tearDown()
    {
        // "benutztes" Objekt entfernen
        $this->instance = null;
        // tearDown der Elternklasse aufrufen
        parent::tearDown();
    }

    /*
     * No tests:
     * canEdit
     */

    /*public function testgetGroups()
    {

        $result = $this->instance->getGroups(0);

        $this->assertEquals($result[34]->id,"7");
        $this->assertEquals($result[34]->name,"Administrator");
        $this->assertEquals($result[34]->injoomla,"1");
    }*/

}

?>