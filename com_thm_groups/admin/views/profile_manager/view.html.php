<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewProfile_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';

require_once JPATH_SITE . '/media/com_thm_groups/helpers/batch.php';

/**
 * THM_GroupsViewProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsViewProfile_Manager extends THM_GroupsViewList
{

	public $items;

	public $pagination;

	public $state;

	public $batch;

	public $groups;

	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
		{
			$exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
			JErrorPage::render($exc);
		}

		// Set batch template path
		$this->batch = array('batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/profile_manager/tmpl/default_batch.php');

		$this->groups = THM_GroupsHelperBatch::getGroupOptions();

		parent::display($tpl);
	}

	/**
	 * Add Joomla ToolBar with add edit delete options.
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		$user = JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_THM_GROUPS_PROFILE_MANAGER_TITLE'), 'profile_manager');

		if ($user->authorise('core.edit', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
		{
			JToolBarHelper::editList('profile.edit');
			JToolBarHelper::publishList('profile.publish', 'COM_THM_GROUPS_PUBLISH_PROFILE');
			JToolBarHelper::unpublishList('profile.unpublish', 'COM_THM_GROUPS_UNPUBLISH_PROFILE');
			JToolBarHelper::publishList('profile.publishContent', 'COM_THM_GROUPS_ACTIVATE_QPS');
			JToolBarHelper::unpublishList('profile.unpublishContent', 'COM_THM_GROUPS_DEACTIVATE_QPS');
			JToolBarHelper::divider();

			$bar = JToolBar::getInstance('toolbar');
			JHtml::_('bootstrap.modal', 'myModal');
			$title = JText::_('COM_THM_GROUPS_ADD_ROLES');

			// Instantiate a new JLayoutFile instance and render the batch button

			$html = "<button data-toggle='modal' data-target='#collapseModal' class='btn btn-small'>";
			$html .= "<i class='icon-new' title='$title'></i> $title</button>";
			$bar->appendButton('Custom', $html, 'batch');
		}

		if ($user->authorise('core.admin', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_thm_groups');
		}
	}

	/**
	 * Adds styles and scripts to the document
	 *
	 * @return  void  modifies the document
	 */
	protected function modifyDocument()
	{
		parent::modifyDocument();

		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true) . '/media/com_thm_groups/js/lib/jquery.chained.remote.js');
		$document->addScript(JURI::root(true) . '/media/com_thm_groups/js/profile_manager.js');
	}
}
