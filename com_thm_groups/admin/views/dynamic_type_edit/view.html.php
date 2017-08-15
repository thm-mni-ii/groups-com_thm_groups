<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewDynamic_Type_Edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
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
class THM_GroupsViewDynamic_Type_Edit extends THM_GroupsViewEdit
{
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

		$existingType = !empty(JFactory::getApplication()->input->getInt('id', 0));

		// Disable editing of the selected static type
		if ($existingType)
		{
			$this->get('Form')->setFieldAttribute('static_typeID', 'readonly', 'true');
		}

		parent::display($tpl);
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
		$document->addScript(JUri::root() . "media/com_thm_groups/js/dynamic_type_edit.js");
	}

	/**
	 * Adds the toolbar to the view
	 *
	 * @return void
	 */
	public function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$title = $this->item->id == 0 ? JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_EDIT_NEW_TITLE') : JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_EDIT_EDIT_TITLE');

		JToolBarHelper::title($title, 'edit');

		JToolBarHelper::apply('dynamic_type.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('dynamic_type.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('dynamic_type.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('dynamic_type.cancel', 'JTOOLBAR_CLOSE');
	}
}
