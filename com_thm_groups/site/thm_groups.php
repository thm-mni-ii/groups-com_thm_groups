<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroups component entry
 * @description Template file of module mod_thm_groups_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

$view = JRequest::getCmd('view');
$input = JFactory::getApplication()->input;
$task = $input->getCmd('task');
$contr = $input->getCmd('controller');

if ($view == "articles")
{
    jimport('thm_groups.data.lib_thm_groups_quickpages');

    // Get user object
    $currUser = JFactory::getUser();


    // Check if the user has Quickpage enabled
    $userHasEnabledQuickpage = THMLibThmQuickpages::isQuickpageEnabledForUser($currUser->id);

    // Check if one group from user has Quickpage enabled
    $groupsHaveEnabledQuickpage = false;
    $userGroups = THMLibThmQuickpages::getGroupsOfUser($currUser->id);
    foreach ($userGroups as $groupID)
    {
        if (THMLibThmQuickpages::isQuickpageEnabledForGroup($groupID))
        {
            $groupsHaveEnabledQuickpage = true;
        }
    }

    // Access check.
    if (!$userHasEnabledQuickpage && !$groupsHaveEnabledQuickpage)
    {
        return JError::raiseWarning(404, JText::_('COM_THM_QUICKPAGES_NOT_ENABLED'));
    }


    if ($userHasEnabledQuickpage)
    {
        $profileData = THMLibThmQuickpages::getPageProfileDataByUserSession();

        // Check if user's quickpage category exist and if not, create it
        if (!THMLibThmQuickpages::existsQuickpageForProfile($profileData))
        {
            THMLibThmQuickpages::createQuickpageForProfile($profileData);
        }
    }


    // Check if the user's groups have Quickpages enabled
    foreach ($userGroups as $groupID)
    {

        if (THMLibThmQuickpages::isQuickpageEnabledForGroup($groupID))
        {
            $profileData = THMLibThmQuickpages::getPageProfileDataByGroup($groupID);

            // Check if group's quickpage category exist and if not, create it
            if (!THMLibThmQuickpages::existsQuickpageForProfile($profileData))
            {
                THMLibThmQuickpages::createQuickpageForProfile($profileData);
            }
        }
    }


    // Show quickpage control or redirect
    if ($userHasEnabledQuickpage OR $groupsHaveEnabledQuickpage)
    {
        $controller = JControllerLegacy::getInstance('thmgroups');

        $controller->execute(JRequest::getCmd('task'));

        $controller->redirect();
    }
}
else
{
        $controller = JControllerLegacy::getInstance('thmgroups');

        $controller->execute(JRequest::getCmd('task'));

        $controller->redirect();
}

