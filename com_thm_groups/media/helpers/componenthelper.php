<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        provides functions useful to multiple component files
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Wolf Rost, <wolf.rost@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
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
        if (!strpos($viewName, 'manager'))
        {
            return;
        }

        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_HOME'),
            'index.php?option=com_thm_groups&view=thm_groups',
            $viewName == 'thm_groups'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_USER_MANAGER'),
            'index.php?option=com_thm_groups&view=user_manager',
            $viewName == 'user_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_GROUP_MANAGER'),
            'index.php?option=com_thm_groups&view=group_manager',
            $viewName == 'group_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_ROLE_MANAGER'),
            'index.php?option=com_thm_groups&view=role_manager',
            $viewName == 'role_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_PROFILE_MANAGER'),
            'index.php?option=com_thm_groups&view=profile_manager',
            $viewName == 'profile_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER'),
            'index.php?option=com_thm_groups&view=attribute_manager',
            $viewName == 'attribute_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER'),
            'index.php?option=com_thm_groups&view=dynamic_type_manager',
            $viewName == 'dynamic_type_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_STATIC_TYPE_MANAGER'),
            'index.php?option=com_thm_groups&view=static_type_manager',
            $viewName == 'static_types_manager'
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
}
