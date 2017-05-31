<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/profile_edit_view.php';

/**
 * THMGroupsViewProfile_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewProfile_Edit extends THM_GroupsViewProfile_Edit_View
{
    public $userID;

    public $groupID;

    public $attributes = null;

    /**
     * Method to get display
     *
     * @param   Object $tpl template (default: null)
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $input        = JFactory::getApplication()->input;
        $this->userID = $input->getInt('userID', 0);
        $canEdit      = THM_GroupsHelperComponent::canEditProfile($this->userID);
        if (!$canEdit)
        {
            THM_GroupsHelperComponent::noAccess();
        }

        // Get user data for edit view.
        $this->attributes = $this->get('Attributes');

        $this->modifyDocument();
        $this->addToolBar();

        parent::display($tpl);
    }

    /**
     * Method to generate buttons for user interaction
     *
     * @return  void
     */
    protected function addToolBar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        JToolBarHelper::title(JText::_('COM_THM_GROUPS_PROFILE_EDIT_EDIT_TITLE'), 'title');

        JToolBarHelper::apply('profile.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('profile.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');
    }
}