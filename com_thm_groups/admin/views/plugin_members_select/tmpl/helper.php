<?php

/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewPlugin_Members_Select
 * @description THMGroupsViewPlugins_Members_Select
 * @author      Mehmet-Ali Pamukci, 	<mehmet.ali.pamukci@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

$lang = JFactory::getLanguage();
$lang->load('plg_editors-xtd_plg_thm_groups_editors_xtd_members', JPATH_PLUGINS . "/editors-xtd/plg_thm_groups_editors_xtd_members/", $lang->getTag(), true);

/**
 * Method getUsers()returns a list of all users with their id's and names
 *
 * @return Users
 */
function getUsers()
{
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select('id, name')
        ->from('#__users');
    $db->setQuery($query);
    return $db->loadObjectList();
}

/**
 * Method getGroups()returns a list of all groups with their id's and titles
 *
 * @return Groups
 */
function getGroups()
{
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select('id, title')
        ->from('#__usergroups');
    $db->setQuery($query);
    return $db->loadObjectList();
}


/**
 * Method getProfiles()returns a list of all profiles with their id's and names
 *
 * @return Users
 */
function getProfiles()
{
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select('id, name')
        ->from('#__thm_groups_profile');
    $db->setQuery($query);
    return $db->loadObjectList();
}


/**
 * Method createSelectFieldUsers() creates a dropdown list with the users
 *
 * @return Users
 */
function createSelectFieldUsers()
{
    $users = getUsers();

    foreach ($users as $user)
    {
        $options[] = JHTML::_('select.option', $user->id, $user->name);
    }


    $dropdown = JHTML::_('select.genericList', $options, 'uid', 'class="chosen-select " multiple style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}

/**
 * Method createSelectFieldGroups()creates a dropdown list of all groups with their id's and titles
 *
 * @return Users
 */
function createSelectFieldGroups()
{
    $groups = getGroups();

    foreach ($groups as $group)
    {
        $options[] = JHTML::_('select.option', $group->id, $group->title);
    }


    $dropdown = JHTML::_('select.genericList', $options, 'gid', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}

/**
 * Method createSelectFieldProfile()creates a dropdown list  of all profiles with their id's and names
 *
 * @return Users
 */
function createSelectFieldProfiles()
{
    $profiles = getProfiles();

    foreach ($profiles as $profile)
    {
        $options[] = JHTML::_('select.option', $profile->id, $profile->name);
    }

    $dropdown = JHTML::_('select.genericList', $options, 'pid', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}

/**
 * Method createSelectFieldParamsUsers()creates a dropdown list with all Modules
 * of the type mod_thm_groups_members which are published
 *
 * @return Users
 */
function createSelectFieldParamsUsers()
{

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select(array('title,params'))
        ->from('#__modules')
        ->where('published=1 AND module LIKE \'%mod_thm_groups_members%\'');

    $db->setQuery($query);

    $result = $db->loadObjectList();

    $titles = $result;

    foreach ($titles as $title)
    {
        $params = $title->params;
        $from = array('{','"',':');
        $to = array('','','=');

        $paramsARC = str_replace($from, $to, $params);
        $suffix = null;

        if (preg_match("/suffix[^,]*/", $paramsARC, $match))
        {
            parse_str($match[0]);

        }
        $options[] = JHTML::_('select.option', $suffix, $title->title);
    }

    $dropdown = JHTML::_('select.genericList', $options, 'suffixUsers', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}

/**
 * Method createSelectFieldParamsGroups()creates a dropdown list of all groups with their id's and titles
 *
 * @return Users
 */
function createSelectFieldParamsGroups()
{

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select(array('title,params'))
        ->from('#__modules')
        ->where('module LIKE \'%mod_thm_groups_members%\'');

    $db->setQuery($query);

    $result = $db->loadObjectList();

    $titles = $result;

    foreach ($titles as $title)
    {
        $params = $title->params;
        $from = array('{','"',':');
        $to = array('','','=');

        $paramsARC = str_replace($from, $to, $params);

        $suffix = null;

        if (preg_match("/suffix[^,]*/", $paramsARC, $match))
        {
            parse_str($match[0]);

        }
        $options[] = JHTML::_('select.option', $suffix, $title->title);
    }

    $dropdown = JHTML::_('select.genericList', $options, 'suffixGroups', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}