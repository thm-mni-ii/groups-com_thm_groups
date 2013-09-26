<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsVieweditgroup
 * @description THMGroupsVieweditgroup file from com_thm_groups
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
 * THMGroupsVieweditgroup class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
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
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$document = JFactory::getDocument();
		$document->addStyleSheet($this->baseurl . "/components/com_thm_groups/assets/css/thm_groups.css");

		if (!($user->authorise('core.edit', 'com_users') && $user->authorise('core.manage', 'com_users')))
		{
			$msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_EDIT_GROUP');
			$app->redirect('index.php?option=com_thm_groups&view=groupmanager', $msg);
		}

		JToolBarHelper::title(JText::_('COM_THM_GROUPS_EDITGROUP_TITLE'), 'mni');

		JToolBarHelper::apply('editgroup.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('editgroup.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('editgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('editgroup.cancel', 'JTOOLBAR_CLOSE');

		// $model =& $this->getModel();
		$item = $this->get('Data');
		$this->assignRef('item', $item);

		$groups = $this->get('AllGroups');
		$this->assignRef('groups', $groups);

		$parent_id = $this->get('ParentId');
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
