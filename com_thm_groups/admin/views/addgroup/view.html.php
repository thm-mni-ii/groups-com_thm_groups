<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewAddGroup
 *@description THMGroupsViewAddGroup file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewAddGroup class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
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
		$document   = & JFactory::getDocument();
		$document->addStyleSheet("components/com_staff/css/membermanager/icon.css");

		// $model =& $this->getModel('addgroup');
		$groups =& $this->get('AllGroups');
		$this->assignRef('groups', $groups);

		JToolBarHelper::title(JText::_('COM_THM_GROUPS_ADDGROUP_TITLE'), 'generic.png');
		JToolBarHelper::apply('addgroup.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('addgroup.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('addgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('addgroup.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');

		$this->form = $this->get('Form');

		parent::display($tpl);
	}
}
