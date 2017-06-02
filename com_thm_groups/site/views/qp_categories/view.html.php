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
class THM_GroupsViewQP_Categories extends JViewLegacy
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
		$user = JFactory::getUser();

		$this->form = $this->get('Form');

		parent::display($tpl);
	}

	function getToolbar()
	{
		jimport('cms.html.toolbar');
		$bar = new JToolBar('toolbar');
		$bar->appendButton('Standard', 'apply', 'Save', 'qp_categories.apply', false);

		return $bar->render();
	}
}
