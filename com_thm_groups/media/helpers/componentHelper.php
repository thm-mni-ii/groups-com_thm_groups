<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        provides functions useful to multiple component files
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing functions usefull to multiple component files
 *
 * @category  Joomla.Component.Admin
 * @package   thm_organizer
 */
class THM_GroupsHelperComponent
{
    /**
     * Configure the Linkbar.
     *
     * @param   object  &$view  the view context calling the function
     *
     * @return void
     */
    public static function addSubmenu(&$view)
    {
        $viewName = $view->get('name');

        // No submenu creation while editing a resource
        if (strpos($viewName, 'edit'))
        {
            return;
        }

        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_HOME'),
            'index.php?option=com_thm_groups&view=thm_groups',
            $viewName == 'thm_groups'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER'),
            'index.php?option=com_thm_groups&view=dynamic_type_manager',
            $viewName == 'dynamic_type_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_GROUP_MANAGER'),
            'index.php?option=com_thm_groups&view=group_manager',
            $viewName == 'group_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_PROFILE_MANAGER'),
            'index.php?option=com_thm_groups&view=profile_manager',
            $viewName == 'profile_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_TEMPLATE_MANAGER'),
            'index.php?option=com_thm_groups&view=template_manager',
            $viewName == 'template_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER'),
            'index.php?option=com_thm_groups&view=attribute_manager',
            $viewName == 'attribute_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_PLUGIN_MANAGER'),
            'index.php?option=com_thm_groups&view=plugin_manager',
            $viewName == 'plugin_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_ROLE_MANAGER'),
            'index.php?option=com_thm_groups&view=role_manager',
            $viewName == 'role_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_STATIC_TYPE_MANAGER'),
            'index.php?option=com_thm_groups&view=static_type_manager',
            $viewName == 'static_type_manager'
        );

        $view->sidebar = JHtmlSidebar::render();
    }

    /**
     * Set variables for user actions.
     *
     * @param   object  &$view  the view context calling the function
     *
     * @return void
     */
    public static function addActions(&$view)
    {
        return;
    }

    /**
     * Checks access for edit views
     *
     * @param   object  &$model  the model checking permissions
     * @param   int     $itemID  the id if the resource to be edited (empty for new entries)
     *
     * @return  bool  true if the user can access the edit view, otherwise false
     */
    public static function allowEdit(&$model, $itemID = 0)
    {
        return true;
    }

    /**
     * Method to check if the current user can edit the profile
     *
     * @param   int  $profileUserID  the id of the profile user
     * @param   int  $groupID        the id of the group
     *
     * @return  boolean  true if the current user is the moderator for the group, otherwise false
     */
    public static function canEditProfile($profileUserID, $groupID = 0)
    {
        $user = JFactory::getUser();
        $isSuperAdmin = $user->authorise('core.admin', 'com_thm_groups');
        $isComponentAdmin = $user->authorise('core.manage', 'com_thm_groups');
        $isModerator = self::getModerator($groupID);

        $isOwn = JFactory::getUser()->id == $profileUserID;
        $params = JComponentHelper::getParams('com_thm_groups');
        $canEditOwn = ($isOwn && $params->get('editownprofile', 0) == 1 );
        $allow = ($isSuperAdmin OR $isComponentAdmin OR $isModerator OR $canEditOwn);
        return ($allow)? true : false;
    }

    /**
     * Method to check if the current user is a moderator of the requested group
     *
     * @param   int  $groupID  the id of the group
     *
     * @return  boolean  true if the current user is the moderator for the group, otherwise false
     */
    public static function getModerator($groupID)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('id');
        $query->from('#__thm_groups_users_usergroups_moderator');
        $query->where("usersID = '" . JFactory::getUser()->id . "'");
        $query->where("usergroupsID = '" . $groupID . "'");
        $dbo->setQuery((string) $query);

        try
        {
            $modID = $dbo->loadResult();
        }
        catch (Exception $exc)
        {
            JErrorPage::render($exc);
        }

        return (empty($modID))? false : true;
    }

    /**
     * Redirects to the homepage and displays a message about missing access rights
     *
     * @return  void
     */
    public static function noAccess()
    {
        $app = JFactory::getApplication();
        $msg = JText::_('COM_THM_GROUPS_NOT_ALLOWED');
        $link = JRoute :: _('index.php');
        $app->Redirect($link, $msg);
    }
}
