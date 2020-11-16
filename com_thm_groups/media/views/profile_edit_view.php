<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once HELPERS . 'profiles.php';


/**
 * THM_GroupsViewProfile_Edit class for component com_thm_groups
 */
class THM_GroupsViewProfile_Edit_View extends JViewLegacy
{
	public $profileID;

	public $name;

	public $attributes = null;

	/**
	 * Method to get display
	 *
	 * @param   Object  $tpl  template (default: null)
	 *
	 * @return  void
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$input           = JFactory::getApplication()->input;
		$selectedIDs     = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', [], 'array'));
		$this->profileID = $selectedIDs ? $selectedIDs[0] : $input->getInt('profileID', $input->getInt('id', 0));

		if (!THM_GroupsHelperProfiles::canEdit($this->profileID))
		{
			$exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
			JErrorPage::render($exc);
		}

		$this->modifyDocument();

		if (method_exists($this, 'addToolBar'))
		{
			$this->addToolBar();
		}

		parent::display($tpl);
	}

	/**
	 * Modifies the document
	 *
	 * @return void
	 */
	protected function modifyDocument()
	{
		JHtml::_('jquery.framework');
		JHtml::_('bootstrap.tooltip');
		JHtml::_('behavior.framework', true);
		JHtml::_('behavior.formvalidator');
		JHtml::_('formbehavior.chosen', 'select');

		JHtml::stylesheet('media/com_thm_groups/css/profile_edit.css');
		JHtml::script('media/com_thm_groups/js/cropbox.js');
		JHtml::script('media/com_thm_groups/js/validators.js');

		JText::script('COM_THM_GROUPS_INVALID_DATE_EU');
		JText::script('COM_THM_GROUPS_INVALID_EMAIL');
		JText::script('COM_THM_GROUPS_INVALID_FORM');
		JText::script('COM_THM_GROUPS_INVALID_NAME');
		JText::script('COM_THM_GROUPS_INVALID_NAME_SUPPLEMENT');
		JText::script('COM_THM_GROUPS_INVALID_REQUIRED');
		JText::script('COM_THM_GROUPS_INVALID_TELEPHONE');
		JText::script('COM_THM_GROUPS_INVALID_TEXT');
		JText::script('COM_THM_GROUPS_INVALID_URL');

		// Close modal after editing
		JFactory::getDocument()->addScriptDeclaration("window.onbeforeunload = function() { window.parent.location.reload(); };");
	}
}
