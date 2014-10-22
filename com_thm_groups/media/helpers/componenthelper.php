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
class THM_ComponentHelper
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
        if (!strpos($viewName, 'manager') AND $viewName == 'thmgroups')
        {
            return;
        }

        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_HOME'),
            'index.php?option=com_thm_groups&view=thmgroups',
            $viewName == 'thmgroups'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_MEMBERMANAGER'),
            'index.php?option=com_thm_groups&view=user_manager',
            $viewName == 'user_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_GROUPMANAGER'),
            'index.php?option=com_users&view=groups',
            $viewName == 'groupmanager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_ROLEMANAGER'),
            'index.php?option=com_thm_groups&view=rolemanager',
            $viewName == 'rolemanager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_PROFILEMANAGER'),
            'index.php?option=com_thm_groups&view=profilemanager',
            $viewName == 'profile_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER'),
            'index.php?option=com_thm_groups&view=attribute_manager',
            $viewName == 'structure_item_manager'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_THM_GROUPS_DYNAMIC TYPE MANAGER'),
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
}
