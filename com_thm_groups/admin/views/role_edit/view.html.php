<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/views/edit.php';

/**
 * Class loads persistent color information into display context
 */
class THM_GroupsViewRole_Edit extends THM_GroupsViewEdit
{
    /**
     * Method to get display
     *
     * @param   Object $tpl template  (default: null)
     *
     * @return  void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        parent::display($tpl);
    }

    /**
     * Method to generate buttons for user interaction
     *
     * @return  void
     */
    protected function addToolBar()
    {
        $isNew = ($this->item->id == 0);
        $title = $isNew ? JText::_('COM_THM_GROUPS_ROLE_EDIT_NEW_TITLE') : JText::_('COM_THM_GROUPS_ROLE_EDIT_EDIT_TITLE');
        JToolbarHelper::title($title, 'test');
        JToolBarHelper::apply('role.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('role.save');
        JToolBarHelper::custom('role.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolbarHelper::cancel('role.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
