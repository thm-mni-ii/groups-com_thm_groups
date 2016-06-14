<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewTemplate_Edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/views/edit.php';

/**
 * THM_GroupsViewDynamic_Type_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewTemplate_Edit extends THM_GroupsViewEdit
{

	public $model;

	public $profilid;

	/**
	 * Loads model data into the view context
	 *
	 * @param   string $tpl the name of the template to be used
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$canCreate = JFactory::getUser()->authorise('core.create', 'com_thm_groups');
		$canEdit   = JFactory::getUser()->authorise('core.create', 'com_thm_groups');
		$hasAccess = ($canCreate OR $canEdit);
		if (!$hasAccess)
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->templateID = JFactory::getApplication()->input->getInt('id', 0);
		$this->model      = $this->getModel();

		parent::display($tpl);
	}

	/**
	 * Adds items to the toolbar
	 *
	 * @return  void  modifies the JToolbar
	 */
	protected function addToolbar()
	{
		$isNew = JFactory::getApplication()->input->getInt('id', 0) == 0;
		if ($isNew)
		{
			$title         = JText::_('COM_THM_GROUPS_TEMPLATE_EDIT_NEW_TITLE');
			$closeConstant = 'JTOOLBAR_CLOSE';
		}
		else
		{
			$title         = JText::_('COM_THM_GROUPS_TEMPLATE_EDIT_EDIT_TITLE');
			$closeConstant = 'JTOOLBAR_CANCEL';
		}

		JToolBarHelper::title($title, 'test');
		JToolBarHelper::apply('template.apply');
		JToolBarHelper::save('template.save');
		if (!$isNew)
		{
			JToolBarHelper::save2copy('template.save2copy');
		}
		JToolBarHelper::save2new('template.save2new');
		JToolBarHelper::cancel('template.cancel', $closeConstant);
	}

	/**
	 * Adds styles and scripts to the document
	 *
	 * @return  void  modifies the document
	 */
	protected function modifyDocument()
	{
		parent::modifyDocument();
		JHtml::_('jquery.framework', true, true);
		JHtml::_('jquery.ui');
		JHtml::_('jquery.ui', array('sortable'));
		JHtml::script(JURI::root() . 'media/jui/js/sortablelist.js');
		JHTML::stylesheet(JURI::root() . 'media/jui/css/sortablelist.css');
	}
}
