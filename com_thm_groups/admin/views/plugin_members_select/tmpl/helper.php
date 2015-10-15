<?php

/**
 * Load Language file From Plugin.
 */
$lang = JFactory::getLanguage();
$lang->load('plg_thm_groups_editors_xtd_members', JPATH_PLUGINS . "/editors-xtd/plg_thm_groups_editors_xtd_members/", $lang->getTag(), true);

/**
 * @return Users for SelectField
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
 * @return Users for SelectField
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
 * @return Users for SelectField
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
 * @return SelectFieldUsers
 */
function createSelectFieldUsers()
{
    $users = getUsers();

    foreach ($users as $user) {
        $options[] = JHTML::_('select.option', $user->id, $user->name);
    }


    $dropdown = JHTML::_('select.genericList', $options, 'uid', 'class="chosen-select " multiple style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}

/**
 * @return SelectFieldGroups
 */
function createSelectFieldGroups()
{
    $groups = getGroups();

    foreach ($groups as $group) {
        $options[] = JHTML::_('select.option', $group->id, $group->title);
    }


    $dropdown = JHTML::_('select.genericList', $options, 'gid', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}

/**
 * @return SelectFieldGroups
 */
function createSelectFieldProfiles()
{
    $profiles = getProfiles();

    foreach ($profiles as $profile) {
        $options[] = JHTML::_('select.option', $profile->id, $profile->name);
    }

    $dropdown = JHTML::_('select.genericList', $options, 'pid', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}


function createSelectFieldParamsUsers()
{

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select(array('title,params'))
        ->from('#__modules')
       ->where('module LIKE \'%mod_thm_groups_members%\'');

    $db->setQuery($query);

    $result = $db->loadObjectList();

    $titles=$result;

    foreach($titles as $title){


        $params=$title->params;
        $from=array('{','"',':');
        $to=array('','','=');

        $paramsARC=str_replace($from,$to,$params);


        $suffix=null;

        if(preg_match("/suffix[^,]*/", $paramsARC, $match)){
            parse_str($match[0]);

        }
        $options[] = JHTML::_('select.option', $suffix,$title->title);
    }

    $dropdown = JHTML::_('select.genericList', $options, 'suffixUsers', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}

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

    $titles=$result;

    foreach($titles as $title){
        $params=$title->params;
        $from=array('{','"',':');
        $to=array('','','=');

        $paramsARC=str_replace($from,$to,$params);


        $suffix=null;

        if(preg_match("/suffix[^,]*/", $paramsARC, $match)){
            parse_str($match[0]);

        }
        $options[] = JHTML::_('select.option', $suffix,$title->title);
    }

    $dropdown = JHTML::_('select.genericList', $options, 'suffixGroups', 'class="chosen-select "  style="width:400px;"', 'value', 'text', -1);

    return $dropdown;
}