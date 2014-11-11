<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAddRole
 * @description THMGroupsViewAddRole file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewAddRole class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewAddRole extends JViewLegacy
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
        $document   = JFactory::getDocument();
        $document->addStyleSheet("components/com_thm_groups/assets/css/thm_groups.css");

        JToolBarHelper::title(JText::_('COM_THM_GROUPS_ADDROLE_TITLE'), 'rolemanager');
        JToolBarHelper::apply('addrole.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('addrole.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('addrole.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('addrole.cancel', 'JTOOLBAR_CLOSE');

        parent::display($tpl);
    }
}
