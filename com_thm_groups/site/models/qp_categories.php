<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelQp_Categories
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.categories');
require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_quickpages_data.php";

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelQp_Categories extends JModelForm
{

	/**
	 * Method to get the record form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return mixed A JForm object on success, false on failure
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_thm_groups.qp_categories', 'qp_categories', array());

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Creates a new category to a user quickpages root category
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function save()
	{
		$input    = JFactory::getApplication()->input;
		$catTitle = $input->get('qp_name', '', 'STRING');

		THM_GroupsQuickpagesData::createQuickpageSubcategoryForProfile(JFactory::getUser()->id, $catTitle);

		// TODO check if a category was successfully added
		return true;
	}
}