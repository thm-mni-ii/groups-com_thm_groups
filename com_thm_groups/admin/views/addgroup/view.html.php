<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAddGroup
 * @description THMGroupsViewAddGroup file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewAddGroup class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewAddGroup extends JView
{
    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        if (!($user->authorise('core.admin')))
        {
            $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_ADD_GROUP');
            $app->redirect('index.php?option=com_thm_groups&view=groupmanager', $msg);
        }

        $document   = JFactory::getDocument();
        $document->addStyleSheet("components/com_thm_groups/assets/css/thm_groups.css");

        // $model =& $this->getModel('addgroup');
        $groups = $this->get('AllGroups');
        $this->assignRef('groups', $groups);

        JToolBarHelper::title(JText::_('COM_THM_GROUPS_ADDGROUP_TITLE'), 'groupmanager');
        JToolBarHelper::apply('addgroup.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('addgroup.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('addgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('addgroup.cancel', 'JTOOLBAR_CLOSE');

        $this->form = $this->get('Form');

        parent::display($tpl);
    }
}
