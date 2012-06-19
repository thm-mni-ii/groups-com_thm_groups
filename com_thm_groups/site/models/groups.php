
BETA
 

Welcome to the new PHP Formatter BETA!
We've given PHP Formatter a new design as well as a new engine! The new engine features:
Blazingly fast, on the fly formatting of all scripts!
PHP 4 and PHP 5 support
Handy syntax check function
Ability to create your own coding styles, or to use builtin styles
Proper handling of doc comments, and alternative control structures



1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49

<?php

/**
 * PHP version 5
 *
 * @category Web Programming Weeks 2011 Technische Hochschule Mittelhessen
 * @package  com_thm_groups
 * @author   Jacek Sokalla <jacek.sokalla@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'confdb.php');

class THMGroupsModelGroups extends JModel
{
    function getGroups()
    {
        $db =& JFactory::getDBO();
        $query = 'SELECT * FROM #__thm_groups_groups ';
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        return $rows;
    }
    
    function canEdit()
    {
        $canEdit = 0;
        $user =& JFactory::getUser();
        
        $db =& JFactory::getDBO();
        $query = "SELECT gid FROM #__thm_groups_groups_map " . "WHERE uid = " . $user->id . " AND rid = 2";
        
        $db->setQuery($query);
        $db = $db->loadObjectlist();
        
        return $db;
    }
    
}
?>

  Formatting took: 113 ms PHP Formatter made by Spark Labs  
Copyright Gerben van Veenendaal  