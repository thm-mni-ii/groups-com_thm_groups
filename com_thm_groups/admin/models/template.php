<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelTemplate
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/database_compare_helper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelTemplate extends JModelLegacy
{
	/**
	 * Method to perform batch operations on an item or a set of items.
	 * TODO make generic function which handle all types of batch operations
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function batch()
	{
		$input = JFactory::getApplication()->input;

		// Array with action command
		$action        = $input->get('batch_action', array(), 'array');
		$groupIDs      = $input->get('batch_id', array(), 'array');
		$rawProfileIDs = $input->get('cid', array(), 'array');

		// Sanitize group ids.
		$profileIDs = array_unique($rawProfileIDs);
		Joomla\Utilities\ArrayHelper::toInteger($profileIDs);

		// Remove any values of zero.
		$zeroIndex = array_search(0, $profileIDs, true);

		if ($zeroIndex !== false)
		{
			unset($profileIDs[$zeroIndex]);
		}

		if (empty($profileIDs))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'warning');

			return false;
		}

		$done = false;
		if (!empty($groupIDs))
		{
			$cmd = $action[0];

			if (!$this->batchProfile($groupIDs, $profileIDs, $cmd))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 'error');

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Perform batch operations
	 *
	 * @param   array  $groupIDs   The role IDs which assignments are being edited
	 * @param   array  $profileIDs An array of group IDs on which to operate
	 * @param   string $action     The action to perform
	 *
	 * @return  boolean  True on success, false on failure
	 */
	public function batchProfile($groupIDs, $profileIDs, $action)
	{
		Joomla\Utilities\ArrayHelper::toInteger($groupIDs);
		Joomla\Utilities\ArrayHelper::toInteger($profileIDs);

		if ($action == 'del')
		{
			return $this->batchDelete($groupIDs, $profileIDs);
		}

		return $this->batchAssociation($groupIDs, $profileIDs);
	}

	/**
	 * Associates groups with the selected profile templates
	 *
	 * @param   array $groupIDs    the ids of the groups to be associated
	 * @param   array $templateIDs the ids of the profiles to which the groups are to be associated
	 *
	 * @return  bool  true on success, otherwise false
	 */
	private function batchAssociation($groupIDs, $templateIDs)
	{
		$query = $this->_db->getQuery(true);

		// First, we need to check if the role is already assigned to a group
		$query->select('profileID, usergroupsID');
		$query->from('#__thm_groups_profile_usergroups');
		$query->where('profileID IN (' . implode(',', $templateIDs) . ')');
		$query->order('profileID');

		$this->_db->setQuery((string) $query);

		try
		{
			$templateGroups = $this->_db->loadObjectList();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		// Build an array with unique templates and their associated groups
		$templates = array();
		foreach ($templateGroups as $templateGroup)
		{
			$templates[$templateGroup->profileID][] = (int) $templateGroup->usergroupsID;
		}

		// Contains groups and roles to insert in DB
		$insertValues = array();
		foreach ($templateIDs as $templateID)
		{
			foreach ($groupIDs as $groupID)
			{
				$insertValues[$templateID][] = $groupID;
			}
		}

		// Removes groups already associated in the manner requested
		THM_GroupsHelperDatabase_Compare::filterInsertValues($insertValues, $templates);

		// All associations to be created already exist
		if (empty($insertValues))
		{
			return true;
		}

		$query = $this->_db->getQuery(true);
		$query->insert('#__thm_groups_profile_usergroups');
		$query->columns(array($this->_db->quoteName('profileID'), $this->_db->quoteName('usergroupsID')));

		$processingNeeded = false;
		foreach ($insertValues as $templateID => $groups)
		{
			if (empty($groups))
			{
				continue;
			}

			foreach ($groups as $groupID)
			{
				$query->values($templateID . ',' . $groupID);
			}
			$processingNeeded = true;
		}

		// If there are no roles to process, throw an error to notify the user
		if (!$processingNeeded)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ASSOCIATIONS_ALREADY_EXIST'), 'message');

			return true;
		}

		$this->_db->setQuery((string) $query);

		try
		{
			$this->_db->execute();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Removes the association of groups with the selected profile templates
	 *
	 * @param   array $groupIDs   the ids of the groups whose associations are to be removed
	 * @param   array $profileIDs the ids of the profiles from which the associations are to be removed
	 *
	 * @return  bool  true on success, otherwise false
	 */
	private function batchDelete($groupIDs, $profileIDs)
	{
		$query = $this->_db->getQuery(true);

		// Remove groups from the profile
		$query->delete('#__thm_groups_profile_usergroups');
		$query->where('profileID' . ' IN (' . implode(',', $profileIDs) . ')');
		$query->where('usergroupsID' . ' IN (' . implode(',', $groupIDs) . ')');

		$this->_db->setQuery((string) $query);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Save the template's basic information
	 *
	 * @return  mixed  int table id on success, otherwise false
	 */
	private function saveTemplate()
	{
		$app      = JFactory::getApplication();
		$formData = $app->input->get('jform', array(), 'array');

		$template = $this->getTable('Template', 'THM_GroupsTable');

		// Only changing the name
		if (!empty($formData['id']))
		{
			try
			{
				$template->load($formData['id']);
				$template->set('name', $formData['name']);
				$template->store();

				return $template->id;
			}
			catch (Exception $exception)
			{
				$app->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}
		}

		$data             = array();
		$data['name']     = $formData['name'];
		$data['ordering'] = $this->getOrdering();

		$success = $template->save($data);

		if (!$success)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_SAVE_TEMPLATE'), 'error');

			return false;
		}

		return $template->id;
	}

	/**
	 * Delete item
	 *
	 * @return mixed
	 */
	public function delete()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		$query = $this->_db->getQuery(true);

		$conditions = array(
			$this->_db->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
		);

		$query->delete($this->_db->quoteName('#__thm_groups_profile'));
		$query->where($conditions);

		$this->_db->setQuery($query);

		return $result = $this->_db->execute();
	}

	/**
	 * Deletes a group from a profile by clicking on
	 * delete icon near profile name
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function deleteGroup()
	{
		$input = JFactory::getApplication()->input;

		$profileID = $input->getInt('p_id');
		$groupID   = $input->getInt('g_id');

		$query = $this->_db->getQuery(true);
		$query
			->delete('#__thm_groups_profile_usergroups')
			->where("profileID = '$profileID'")
			->where("usergroupsID = '$groupID'");
		$this->_db->setQuery((string) $query);

		try
		{
			$this->_db->execute();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Get the integer value for the new ordering position
	 *
	 * @return  int the new ordering position
	 */
	public function getOrdering()
	{
		$query = $this->_db->getQuery(true);

		$query->select("MAX(ordering)")->from('#__thm_groups_profile');
		$this->_db->setQuery($query);

		try
		{
			$last = $this->_db->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_GET_LAST_POSITION'), 'warning');

			return 1;
		}

		return $last + 1;
	}

	/**
	 * Saves the profile templates
	 *
	 * @return  mixed int on success, false otherwise
	 */
	public function save()
	{
		$app     = JFactory::getApplication();
		$isAdmin = JFactory::getUser()->authorise('core.admin', 'com_thm_groups');

		if (!$isAdmin)
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$template = $app->input->get('jform', array(), 'array');

		if (empty($template['name']))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NAME_EMPTY'), 'error');

			return false;
		}

		if (empty($template['attributes']))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NO_ATTRIBUTES_PASSED'), 'error');

			return false;
		}


		$this->_db->transactionStart();
		$templateID = $this->saveTemplate();

		if (empty($templateID))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
			$this->_db->transactionRollback();

			return false;
		}

		$ordering = 1;

		foreach ($template['attributes'] as $attribute)
		{
			$success = $this->saveAttribute($templateID, $attribute, $ordering);

			if (empty($success))
			{
				$app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
				$this->_db->transactionRollback();

				return false;
			}

			$ordering++;
		}

		$this->_db->transactionCommit();


		return $templateID;
	}

	/**
	 * Saves template's attributes
	 *
	 * @param   int   $templateID Template ID
	 * @param   array $attribute  the data for the specific template attribute
	 * @param   int   $ordering   the order in which the template attribute is to be displayed
	 *
	 * @return  mixed
	 */
	private function saveAttribute($templateID, $attribute, $ordering)
	{
		if (empty($attribute['attributeID']))
		{
			return false;
		}

		$attribute['profileID'] = $templateID;
		$attribute['published'] = (bool) $attribute['published'];
		$attribute['ordering']  = $ordering;

		$jsonParams              = [];
		$jsonParams['showLabel'] = isset($attribute['show_label']) ? (int) $attribute['show_label'] : 0;
		$jsonParams['showIcon']  = isset($attribute['show_icon']) ? (int) $attribute['show_icon'] : 0;
		$jsonParams['wrap']      = isset($attribute['wrap']) ? (int) $attribute['wrap'] : 0;

		$attribute['params'] = json_encode($jsonParams);

		$templateAttribute = $this->getTable('Template_Attribute', 'THM_GroupsTable');

		$success = $templateAttribute->save($attribute);

		if (!$success)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_SAVE_TEMPLATE_ATTRIBUTE'), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array   $pks   An array of primary key ids.
	 * @param   integer $order +1 or -1
	 *
	 * @return  mixed
	 *
	 */
	public function saveorder($pks = null, $order = null)
	{
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_thm_groups/tables/');
		$table = $this->getTable('Template', 'THM_GroupsTable');

		$conditions = array();

		if (empty($pks))
		{
			return JError::raiseWarning(400, JText::_('COM_THM_GROUPS_NO_ITEMS_SELECTED'));
		}

		$groupsAdmin = JFactory::getUser()->authorise('core.admin', 'com_thm_groups');

		if (!$groupsAdmin)
		{
			return JError::raiseWarning(403, JText::_('COM_THM_GROUPS_NOT_ALLOWED'));
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load($pk);

			if ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
