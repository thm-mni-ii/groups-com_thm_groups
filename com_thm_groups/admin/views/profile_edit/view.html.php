<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/profile_edit_view.php';

/**
 * THMGroupsViewProfile_Edit class for component com_thm_groups
 */
class THM_GroupsViewProfile_Edit extends THM_GroupsViewProfile_Edit_View
{
    /**
     * Method to generate buttons for user interaction
     *
     * @return  void
     */
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_THM_GROUPS_PROFILE_EDIT_EDIT_TITLE'), 'title');

        JToolBarHelper::apply('profile.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('profile.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');

        JToolbarHelper::help('COM_THM_GROUPS_TEMPLATES_DOCUMENTATION', '',
            JUri::root() . 'media/com_thm_groups/documentation/profile_edit.php');
    }
}
