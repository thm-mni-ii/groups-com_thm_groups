<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroups component entry
 * @description Template file of module mod_thm_groups_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
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

    $quickpageGlobalEnabled = THMLibThmQuickpages::isQuickpageEnabled();

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
    if (!$userHasEnabledQuickpage || !$quickpageGlobalEnabled )
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

    // Show quickpage control or redirect
    if ($userHasEnabledQuickpage OR $groupsHaveEnabledQuickpage)
    {
        $controller = JControllerLegacy::getInstance('thm_groups');

        $controller->execute(JRequest::getCmd('task'));

        $controller->redirect();
    }
}
elseif($view == 'qp_categories')
{
    $user = JFactory::getUser();
    if ($user->authorise('core.create', 'com_content.category'))
    {
        $controller = JControllerLegacy::getInstance('thm_groups');

        $controller->execute(JRequest::getCmd('task'));

        $controller->redirect();
    }
    else
    {
        return JError::raiseWarning(404, JText::_("COM_THM_QUICKPAGES_NO_RIGHTS_TO_CREATE_CATEGORY"));
    }
}
else
{
        $controller = JControllerLegacy::getInstance('thm_groups');

        $controller->execute(JRequest::getCmd('task'));

        $controller->redirect();
}

