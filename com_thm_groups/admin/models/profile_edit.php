<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

use MongoDB\BSON\Type;

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelProfile_Edit extends THM_GroupsModelEdit
{
	/**
	 * Returns all user attributes for the user edit form
	 *
	 * @param   int $profileID the user id
	 *
	 * @return  array  array of arrays containing profile information
	 */
	public function getAttributes($profileID = 0)
	{
		$input     = JFactory::getApplication()->input;
		$profileID = empty($profileID) ? $input->getInt('id', 0) : $profileID;
		if (empty($profileID))
		{
			return array();
		}

		return THM_GroupsHelperProfile::getProfile($profileID);
	}

	/**
	 * Method to load the form data
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	protected function loadFormData()
	{
		$input       = JFactory::getApplication()->input;
		$selectedIDs = $input->get('cid', array(), 'array');
		$id          = (empty($selectedIDs)) ? $input->getInt('id', 0) : $selectedIDs[0];

		return $this->getItem($id);
	}
}
