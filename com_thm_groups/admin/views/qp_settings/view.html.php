<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewUser_Select
 * @description view output file for user lists
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.framework');
JHtml::_('jquery.framework');

/**
 * Class which loads data into the view output context
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 * @link        www.thm.de
 */
class THM_GroupsViewQP_Settings extends JViewLegacy
{

	/**
	 * loads data into view output context and initiates functions creating html
	 * elements
	 *
	 * @param   string $tpl the template to be used
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->form = $this->get('Form');
		$this->addToolBar();

		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true) . '/media/com_thm_groups/js/qp_settings.js');

		parent::display($tpl);
	}

	/**
	 * creates a joomla administrative tool bar
	 *
	 * @return void
	 */
	protected function addToolBar()
	{
		JToolbarHelper::title(JText::_('COM_THM_GROUPS_USER_SELECT_VIEW_TITLE'), 'test');

		JToolbarHelper::apply('qp_settings.apply', 'COM_THM_GROUPS_APPLY');
	}
}
