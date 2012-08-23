<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewStructure
 * @description THMGroupsViewStructure file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @authors     Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewStructure class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewStructure extends JView
{
	protected $items;

	protected $pagination;

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

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		JToolBarHelper::title(
				JText::_('COM_THM_GROUPS_STRUCTURE_TITLE'),
				'membermanager.png', JPATH_COMPONENT . DS . 'img' . DS . 'membermanager.png'
		);
		JToolBarHelper::custom(
			'structure.add',
			'moderate.png',
			JPATH_COMPONENT . DS . 'img' . DS . 'moderate.png',
			'COM_THM_GROUPS_STRUCTURE_ADD',
			false,
			false
		);
		JToolBarHelper::editListX('structure.edit', 'COM_THM_GROUPS_STRUCTURE_EDIT');
		JToolBarHelper::deleteList('COM_THM_GROUPS_REALLY_DELETE', 'structure.remove', 'JTOOLBAR_DELETE');
		JToolBarHelper::cancel('structure.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');
		parent::display($tpl);

	}
}
