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

// Import Joomla view library
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
    public $userContent = null;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template (default: null)
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->item = JFactory::getApplication()->input->get('id');
        $componentDir = "/administrator/components/com_thm_groups";

        JHtml::_('jquery.framework');
        JHtml::_('behavior.framework', true);
        JHtml::_('behavior.formvalidation');
        JHtml::_('formbehavior.chosen', 'select');
        //JHtml::_('script', JUri::root() . $componentDir . '/assets/js/tabReload.js');
        JHtml::_('script', JUri::root() . $componentDir . '/assets/js/cropbox.js');
        JHtml::_('script', JUri::root() . $componentDir . '/assets/js/inputValidation.js');
        JHtml::_('script', JUri::root() . $componentDir . '/assets/js/user_edit.js');

        $doc = JFactory::getDocument();
        $doc -> addStyleSheet(JURI::root(true) . $componentDir . '/assets/css/cropbox.css');
        $doc -> addStyleSheet(JURI::root(true) . $componentDir . '/assets/css/edit.css');
        $doc -> addStyleSheet(JUri::root() . "libraries/thm_core/fonts/iconfont.css");
        $doc -> addScript(JUri::root() . "libraries/thm_core/js/formbehaviorChosenHelper.js");

        $this->userContent = $this->get('Content');
        //var_dump($this->userContent);
        //die();
        //$this->addToolBar();

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

        $title = $this->item == 0 ? 'New' : 'Edit';

        JToolBarHelper::title($title, 'title');

        JToolBarHelper::apply('user.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('user.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::cancel('user.cancel', 'JTOOLBAR_CLOSE');
    }
}