<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsVieweditgroup
 *@description THMGroupsVieweditgroup file from com_thm_groups
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
 * THMGroupsVieweditgroup class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsVieweditgroup extends JView
{

	protected $form;

	/**
	 * Method to get display
	 *
	 * @param   Object  $tpl  template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		JToolBarHelper::title(JText::_('COM_THM_GROUPS_EDITGROUP_TITLE'), 'generic.png');

		JToolBarHelper::apply('editgroup.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('editgroup.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('editgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('editgroup.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');

		$model =& $this->getModel();
		$item =& $this->get('Data');
		$this->assignRef('item', $item);

		$groups =& $this->get('AllGroups');
		$this->assignRef('groups', $groups);

		$parent_id =& $this->get('ParentId');
		$this->assignRef('item_parent_id', $parent_id);

		$this->form = $this->get('Form');
		$info = array();
		$info['groupinfo'] = $item[0]->info;

		if (!empty($info))
		{
			$this->form->bind($info);
		}

		parent::display($tpl);
	}
}
