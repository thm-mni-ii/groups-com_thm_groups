<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewRole_Edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('thm_core.edit.view');

/**
 * Class loads persistent color information into display context
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsViewRole_Edit extends THM_CoreViewEdit
{
    /**
     * Method to get display
     *
     * @param   Object  $tpl  template  (default: null)
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
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
        $title = $isNew? JText::_('COM_THM_GROUPS_ROLE_EDIT_NEW_TITLE') : JText::_('COM_THM_GROUPS_ROLE_EDIT_EDIT_TITLE');
        JToolbarHelper::title($title, 'test');
        JToolBarHelper::apply('role.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('role.save');
        JToolBarHelper::custom('role.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolbarHelper::cancel('role.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
