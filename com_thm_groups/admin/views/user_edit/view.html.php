<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewUser_Edit
 * @description THMGroupsViewUser_Edit file from com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die();

// import Joomla view library
jimport('thm_core.edit.view');

/**
 * THMGroupsViewUser_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewUser_Edit extends THM_CoreViewEdit
{
    public $item = null;

    /**
     * Method to get display
     *
     * @param   Object $tpl template  (default: null)
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->item = JFactory::getApplication()->input->get('id');
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
        $title = $isNew ? JText::_('x') : JText::_('User');
        JToolbarHelper::title($title, 'title');
        JToolbarHelper::apply('user.apply', $isNew ? 'x' : 'Save');
        JToolbarHelper::save('user.save');
        JToolbarHelper::cancel('user.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}