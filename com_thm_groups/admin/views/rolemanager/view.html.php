<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewRolemanager
 *@description THMGroupsViewRolemanager file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@authors      Niklas Simonis, niklas.simonis@mni.thm.de
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
 * THMGroupsViewRolemanager class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsViewRolemanager extends JView
{

	protected $state;

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
		$document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");

		JToolBarHelper::title(JText::_('COM_THM_GROUPS_ROLEMANAGER_TITLE'), 'membermanager.png', JPATH_COMPONENT . DS . 'img' . DS . 'membermanager.png');
		JToolBarHelper::custom('rolemanager.addRole', 'moderate.png', JPATH_COMPONENT . DS . 'img' . DS . 'moderate.png', 'COM_THM_GROUPS_ROLEMANAGER_ADD', false, false);
		JToolBarHelper::editListX('rolemanager.edit', 'COM_THM_GROUPS_ROLEMANAGER_EDIT');
		JToolBarHelper::deleteList('COM_THM_GROUPS_REALLY_DELETE', 'rolemanager.remove', 'JTOOLBAR_DELETE');
		JToolBarHelper::cancel('rolemanager.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');

		$uri =& JFactory::getURI();
		$query = $uri->getQuery();

 		$mainframe = Jfactory::getApplication('Administrator');

		$this->state = $this->get('State');

		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('request_url', $uri->toString());

		parent::display($tpl);
	}
}
