<?php

require_once JPATH_BASE . '/administrator/components/com_thm_groups/models/editgroup.php';

class THMGroupsModelEditGroupAdminTest extends PHPUnit_Framework_TestCase
{
    protected $instance;

    // PHPUnit_TestCase funtcion - overwritten
    function setUp()
    {
        $this->instance = new THMGroupsModelEditGroup();
        $array['gsgid'] = '2';
        $array['gid']   = '1';
        JRequest::set($array, 'post');
    }

    // Kill instance
    function tearDown()
    {
        // "benutztes" Objekt entfernen
        $this->instance = null;
        // tearDown der Elternklasse aufrufen
        parent::tearDown();
    }

    /*
     * No tests:
    * getData
    */

    // tests getForm
    // returns JForm object
    function testgetForm()
    {
        $result   = $this->instance->getForm();
        $options  = '';
        $expected = new JForm($options);
        $this->assertNotSame($expected, $result);
    }

    // tests _buildQuery()
    // function returns an SQL query
    // should start with SELECT
    function test_buildQuery()
    {
        $result   = $this->instance->_buildQuery();
        $expected = "SELECT ";
        $this->assertContains($expected, $result);
    }

    // tests function updatePic
    // insert value in database
    // updatPic() creates pictures and update inserted value
    // delete database entry
    function testupdatePic()
    {

        $db    = JFactory::getDBO();
        $query = "INSERT INTO #__thm_groups_groups (id, name, info, picture, mode, injoomla)";
        $query .= "VALUES ('99999','THMGroupsTest','TestSuite','','','1')";
        $db->setQuery($query);
        $db->query();

        $picField = null;
        $result   = $this->instance->updatePic("99999", $picField);
        //var_dump($result);
        $expected = false; // because of $picField = null

        $query = "DELETE FROM #__thm_groups_groups WHERE id = '99999'";
        $db->setQuery($query);
        $db->query();

        $this->assertEquals($result, $expected);
    }

    // tests delPic() function
    // function updates entry with GID = 1
    function testdelPic()
    {
        $gid = "1";
        $db  = JFactory::getDBO();

        $result   = $this->instance->delPic();
        $expected = true;

        $query = "UPDATE #__thm_groups_groups SET picture=NULL WHERE id = $gid ";
        $db->setQuery($query);
        $db->query();

        $this->assertEquals($result, $expected);
    }

    // tests getParentId
    // function returns id from parentgroup
    // id should be "1"
    function testgetParentId()
    {

        $array['cid'] = '2';
        JRequest::set($array, 'post');

        $result   = $this->instance->getParentId();
        $expected = "1"; // ID from Group Public
        $this->assertEquals($result, $expected);
    }

    // tests getAllGroups
    // getAllGroups use $array['gid'] = '1';
    // should return objectlist, sorted by id
    // first object should be Public Group
    function testgetAllGroups()
    {
        $result = $this->instance->getAllGroups();
        $this->assertEquals($result[0]->id, "1");
        $this->assertEquals($result[0]->parent_id, "0");
        $this->assertEquals($result[0]->lft, "1");
        $this->assertEquals($result[0]->title, "Public");
    }

}

?>