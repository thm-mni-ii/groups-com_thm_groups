<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once HELPERS . 'content.php';
require_once HELPERS . 'groups.php';
require_once HELPERS . 'profiles.php';
require_once HELPERS . 'roles.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelProfile extends JModelLegacy
{
	const IMAGE_PATH = JPATH_ROOT . IMAGE_PATH;

	/**
	 * Associates a group and potentially multiple roles with the selected users
	 *
	 * @return  bool true on success, otherwise false.
	 * @throws Exception
	 */
	public function batch()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$newProfileData  = $app->getUserStateFromRequest('.profiles', 'profiles', [], 'array');
		$requestedAssocs = json_decode(urldecode($app->input->getString('batch-data')), true);
		$selectedIDs     = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));

		if ($selectedIDs and !empty($requestedAssocs))
		{
			return $this->batchRoles($selectedIDs, $requestedAssocs);
		}
		elseif (!empty($newProfileData['groupIDs']) and !empty($newProfileData['profileIDs']))
		{
			return $this->batchProfiles($newProfileData['groupIDs'], $newProfileData['profileIDs']);
		}

		$app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 'error');

		return false;
	}

	/**
	 * Associates the profiles with the given groups default group/role association.
	 *
	 * @param   array  $groupIDs    the ids of the selected groups
	 * @param   array  $profileIDs  the ids of the selected profiles
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function batchProfiles($groupIDs, $profileIDs)
	{
		foreach ($groupIDs as $groupID)
		{
			$memberAssocID = THM_GroupsHelperRoles::getAssocID(MEMBER, $groupID, 'group');

			foreach ($profileIDs as $profileID)
			{
				if (!THM_GroupsHelperProfiles::associateRole($profileID, $memberAssocID))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Associates the selected profiles with the selected group/role assocations.
	 *
	 * @param   array  $profileIDs       the selected profileIDs
	 * @param   array  $requestedAssocs  the ids of the group/role association with which the profiles should be associated
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function batchRoles($profileIDs, $requestedAssocs)
	{
		if (!$this->setJoomlaAssociations($profileIDs, $requestedAssocs))
		{
			return false;
		}

		return $this->setGroupsAssociations($profileIDs, $requestedAssocs);
	}

	/**
	 * Deletes user from a group both in Joomla and in THM Groups.
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function deleteGroupAssociation()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$profileID = $app->input->getInt('profileID', 0);
		$groupID   = $app->input->getInt('groupID', 0);

		if (count(JFactory::getUser($profileID)->groups) < 2)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_CANNOT_DELETE_SOLE_GROUP'), 'error');

			return false;
		}

		// Allow deletion of Joomla user group association if the user is associated with more than one user group.
		$joomlaQuery = $this->_db->getQuery(true);
		$joomlaQuery->delete('#__user_usergroup_map')->where("user_id = $profileID AND group_id = $groupID");
		$this->_db->setQuery($joomlaQuery);

		try
		{
			$success = (bool) $this->_db->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		if (empty($success))
		{
			return false;
		}

		$profileAssocs    = THM_GroupsHelperProfiles::getRoleAssociations($profileID);
		$groupAssocs      = THM_GroupsHelperGroups::getRoleAssocIDs($groupID);
		$disposableAssocs = array_intersect($profileAssocs, $groupAssocs);

		$groupsQuery = $this->_db->getQuery(true);
		$groupsQuery->delete('#__thm_groups_profile_associations')
			->where("role_associationID IN ('" . implode("','", $disposableAssocs) . "')")
			->where("profileID = $profileID");
		$this->_db->setQuery($groupsQuery);

		try
		{
			$success = (bool) $this->_db->execute();
		}
		catch (Exception $exception)
		{
			$app->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		if (empty($success))
		{
			return false;
		}

		if (!THM_GroupsHelperProfiles::getRoleAssociations($profileID))
		{
			return $this->updateBinaryValue($profileID, 'published', 0);
		}

		return true;
	}

	/**
	 * Deletes the value for a specific profile picture attribute
	 *
	 * @param   int  $profileID    the id of the profile with which the picture is associated.
	 * @param   int  $attributeID  the id of the attribute under which the value is stored.
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function deletePicture($profileID = 0, $attributeID = 0)
	{
		$app         = JFactory::getApplication();
		$profileID   = $app->input->getInt('profileID', $profileID);
		$attributeID = $app->input->getString('attributeID', $attributeID);

		if (!THM_GroupsHelperProfiles::canEdit($profileID))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$selectQuery = $this->_db->getQuery(true);
		$selectQuery->select('value')
			->from('#__thm_groups_profile_attributes')
			->where("profileID = '$profileID'")
			->where("attributeID = '$attributeID'");
		$this->_db->setQuery($selectQuery);

		try
		{
			$fileName = $this->_db->loadResult();
		}
		catch (Exception $exc)
		{
			$app->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		// Button was pushed although there was no saved picture?
		if (empty($fileName))
		{
			return true;
		}

		if (file_exists(self::IMAGE_PATH . $fileName))
		{
			unlink(self::IMAGE_PATH . $fileName);
		}

		// Update new picture filename
		$updateQuery = $this->_db->getQuery(true);

		// Update the database with new picture information
		$updateQuery->update('#__thm_groups_profile_attributes')
			->set("value = ''")
			->where("profileID = '$profileID'")
			->where("attributeID = '$attributeID'");
		$this->_db->setQuery($updateQuery);

		try
		{
			$this->_db->execute();
		}
		catch (Exception $exc)
		{
			$app->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Deletes one user role from a group
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function deleteRoleAssociation()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$groupID   = $app->input->getInt('groupID', 0);
		$profileID = $app->input->getInt('profileID', 0);
		$roleID    = $app->input->getInt('roleID', 0);

		$idToDelete = THM_GroupsHelperRoles::getAssocID($roleID, $groupID, 'group');

		$query = $this->_db->getQuery(true);

		$query
			->delete('#__thm_groups_profile_associations')
			->where("profileID = '$profileID' AND role_associationID = '$idToDelete'");

		$this->_db->setQuery($query);

		try
		{
			$success = $this->_db->execute();
		}
		catch (Exception $exception)
		{
			$app->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return empty($success) ? false : true;
	}

	/**
	 * Returns a list of group assoc ids matching the request data
	 *
	 * @param   array  $requestedAssocs  An array with groups and roles
	 *
	 * @return  array with ids
	 * @throws Exception
	 */
	private function getGroupAssociations($requestedAssocs)
	{
		$assocs = [];

		foreach ($requestedAssocs as $requestedAssoc)
		{
			foreach ($requestedAssoc['roles'] as $role)
			{
				$query = $this->_db->getQuery(true);
				$query->select('id')
					->from('#__thm_groups_role_associations')
					->where("groupID = '{$requestedAssoc['id']}'")
					->where("roleID = {$role['id']}");
				$this->_db->setQuery($query);

				try
				{
					$assocID = $this->_db->loadResult();
				}
				catch (Exception $exception)
				{
					JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

					return [];
				}

				$assocs[$assocID] = $assocID;
			}
		}

		return $assocs;
	}

	/**
	 * Allows the public display of the user's profile. Access checks are performed in toggle.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function publish()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$profileIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));
		foreach ($profileIDs as $profileID)
		{
			if (!$this->updateBinaryValue($profileID, 'published', 1))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Allows public display of personal content. Access checks are performed in toggle.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function publishContent()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$profileIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));
		foreach ($profileIDs as $profileID)
		{
			if (!$categoryID = THM_GroupsHelperProfiles::getCategoryID($profileID))
			{
				$categoryID = THM_GroupsHelperCategories::create($profileID);
			}

			if (!THM_GroupsHelperProfiles::isPublished($profileID))
			{
				return false;
			}

			$categoryEnabled = $this->updateCategoryPublishing($categoryID, 1);
			$contentEnabled  = $this->updateBinaryValue($profileID, 'contentEnabled', 1);

			if (!$categoryEnabled or !$contentEnabled)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Saves user profile information
	 *
	 * @return  mixed  int profile ID on success, otherwise false
	 * @throws Exception
	 */
	public function save()
	{
		$app  = JFactory::getApplication();
		$data = $app->input->get('jform', [], 'array');

		// Ensuring int will fail access checks on manipulated ids.
		$profileID = $data['profileID'];

		if (!THM_GroupsHelperProfiles::canEdit($profileID))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$dbo = JFactory::getDbo();
		$dbo->transactionStart();

		$success = $this->saveValues($data);

		if (!$success)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
			$dbo->transactionRollback();

			return false;
		}

		THM_GroupsHelperProfiles::setAlias($profileID);
		$user       = \JFactory::getUser($profileID);
		$user->name = THM_GroupsHelperProfiles::getDisplayName($profileID);
		$user->save(true);

		$dbo->transactionCommit();

		return $profileID;
	}

	/**
	 * Saves the cropped image that was uploaded via ajax in the profile_edit.view
	 *
	 * @return  bool|mixed|string
	 * @throws Exception
	 */
	public function saveCropped()
	{
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$profileID = $input->getInt('profileID');

		if (!THM_GroupsHelperProfiles::canEdit($profileID))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$file = $app->input->files->get('data');

		if (empty($file))
		{
			return false;
		}

		$filename = $input->get('filename');

		// TODO: Make these configurable
		$allowedExtensions = ['bmp', 'gif', 'jpg', 'jpeg', 'png', 'BMP', 'GIF', 'JPG', 'JPEG', 'PNG'];
		$invalid           = ($file['size'] > 10000000 or !in_array(pathinfo($filename, PATHINFO_EXTENSION),
				$allowedExtensions));

		if ($invalid)
		{
			return false;
		}

		$attributeID = $input->get('attributeID');
		$newFileName = $profileID . "_" . $attributeID . "." . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$path        = self::IMAGE_PATH . "/$newFileName";

		// Out with the old
		$deleted = $this->deletePicture($profileID, $attributeID);
		JFactory::getApplication()->enqueueMessage("Deleted: $deleted!", 'message');

		if (!$deleted)
		{
			return false;
		}

		// Upload new cropped image
		$uploaded = JFile::upload($file['tmp_name'], $path, false);

		// Create thumbs and send back prev image to the form
		if ($uploaded)
		{
			$position      = strpos($path, 'images' . DIRECTORY_SEPARATOR);
			$convertedPath = substr($path, $position);

			// Adding a random number ensures that the browser no longer uses the cached image.
			$random   = rand(1, 100);
			$newImage = "<img  src='" . JURI::root() . $convertedPath . "?force=$random" . "'/>";

			return $newImage;
		}

		return false;
	}

	/**
	 * Updates all profile attribute values and publication statuses.
	 *
	 * @param   array  $formData  the submitted form data
	 *
	 * @return  bool true on success, otherwise false
	 * @throws Exception
	 */
	private function saveValues($formData)
	{
		$profileID = $formData['profileID'];

		foreach ($formData as $attributeID => $attribute)
		{
			if (is_string($attribute))
			{
				continue;
			}

			$rawValue     = empty(strip_tags(trim($attribute['value']))) ? '' : trim($attribute['value']);
			$cleanedValue = THM_GroupsHelperComponent::removeEmptyTags($rawValue);

			$query = $this->_db->getQuery(true);
			$query->update('#__thm_groups_profile_attributes');

			$value = $this->_db->q($cleanedValue);
			$query->set("value = $value");

			$published = empty($attribute['published']) ? 0 : 1;
			$query->set("published = '$published'");

			$query->where("profileID = '$profileID'");
			$query->where("attributeID = '$attributeID'");

			$this->_db->setQuery($query);

			try
			{
				$success = $this->_db->execute();
			}
			catch (Exception $exc)
			{
				JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

				return false;
			}

			if (empty($success))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Associates the profile with the given groups/roles
	 *
	 * @param   array  $profileIDs       the profile IDs which assignments are being edited
	 * @param   array  $requestedAssocs  an array of groups and roles
	 *
	 * @return  boolean  True on success, false on failure
	 * @throws Exception
	 */
	private function setGroupsAssociations($profileIDs, $requestedAssocs)
	{
		// Can only occur by manipulation.
		if (empty($profileIDs) or empty($requestedAssocs))
		{
			return true;
		}

		$roleAssociations = $this->getGroupAssociations($requestedAssocs);

		// Can only occur by manipulation.
		if (empty($roleAssociations))
		{
			return false;
		}

		$completeSuccess = true;
		$partialSuccess  = false;

		foreach ($profileIDs as $profileID)
		{

			$profileAssociations = THM_GroupsHelperProfiles::getRoleAssociations($profileID);

			foreach ($roleAssociations as $assocID)
			{
				if (!in_array($assocID, $profileAssociations))
				{
					$success         = THM_GroupsHelperProfiles::associateRole($profileID, $assocID);
					$completeSuccess = ($completeSuccess and $success);
					$partialSuccess  = ($partialSuccess or $success);
				}
			}
		}

		if ($completeSuccess)
		{
			return $completeSuccess;
		}

		// Is also a partial fail.
		if ($partialSuccess)
		{
			JFactory::getApplication()->enqueueMessage('COM_THM_GROUPS_PARTIAL_ASSOCIATION_FAIL', 'warning');

			return $partialSuccess;
		}

		return false;
	}

	/**
	 * Maps users to Joomla user groups.
	 *
	 * @param   array  $profileIDs  an array with profile ids (joomla user ids)
	 * @param   array  $batchData   an array with groups and roles
	 *
	 * @return bool true on success, otherwise false
	 * @throws Exception
	 */
	private function setJoomlaAssociations($profileIDs, $batchData)
	{
		$existingQuery = $this->_db->getQuery(true);
		$existingQuery->select('id')->from('#__user_usergroup_map')
			->where("user_id IN ('" . implode("','", $profileIDs) . "')");
		$query = $this->_db->getQuery(true);
		$query->insert('#__user_usergroup_map')->columns('user_id, group_id');
		$values = [];

		foreach ($profileIDs as $profileID)
		{
			foreach ($batchData as $groupData)
			{
				$values[] = "'$profileID', '{$groupData['id']}'";
			}
		}

		$query->values($values);
		$this->_db->setQuery($query);

		try
		{
			$success = $this->_db->execute();
		}
		catch (Exception $exception)
		{
			// Ignore duplicate entry exception
			if ($exception->getCode() === 1062)
			{
				return true;
			}
			else
			{
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}
		}

		return empty($success) ? false : true;
	}

	/**
	 * Toggles a binary entity property value
	 *
	 * @return  boolean  true on success, otherwise false
	 * @throws Exception
	 */
	public function toggle()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$input = $app->input;

		if (!$profileID = $input->getInt('id', 0))
		{
			return false;
		}

		// Toggle is called using the current value.
		$value = !$input->getBool('value', false);

		switch ($input->getCmd('attribute'))
		{
			case 'canEdit':
				return $this->updateBinaryValue($profileID, 'canEdit', $value);
			case 'contentEnabled':

				if (!$categoryID = THM_GroupsHelperProfiles::getCategoryID($profileID))
				{
					$categoryID = THM_GroupsHelperCategories::create($profileID);
				}

				if (!THM_GroupsHelperProfiles::isPublished($profileID))
				{
					return false;
				}

				if ($value)
				{
					$categoryEnabled = $this->updateCategoryPublishing($categoryID, 1);
					$contentEnabled  = $this->updateBinaryValue($profileID, 'contentEnabled', 1);

					return ($categoryEnabled and $contentEnabled) ? true : false;
				}

				$categoryDisabled = $this->updateCategoryPublishing($categoryID, 0);
				$contentDisabled  = $this->updateBinaryValue($profileID, 'contentEnabled', 0);

				return ($categoryDisabled and $contentDisabled) ? true : false;
			case 'published':

				if ($value)
				{
					return $this->updateBinaryValue($profileID, 'published', 1);
				}

				if ($categoryID = THM_GroupsHelperProfiles::getCategoryID($profileID))
				{
					$this->updateCategoryPublishing($categoryID, 0);
				}

				$profileDisabled = $this->updateBinaryValue($profileID, 'published', 0);
				$contentDisabled = $this->updateBinaryValue($profileID, 'contentEnabled', 0);

				return ($contentDisabled and $profileDisabled) ? true : false;
			default:
				return false;
		}
	}

	/**
	 * Updates a binary value.
	 *
	 * @param   int     $profileID  the profile id
	 * @param   string  $column     the name of the column to update
	 * @param   mixed   $value      the new value to assign
	 *
	 * @return bool true if the query executed successfully, otherwise false
	 * @throws Exception
	 */
	private function updateBinaryValue($profileID, $column, $value)
	{
		$value = empty($value) ? 0 : 1;
		$query = $this->_db->getQuery(true);
		$query->update('#__thm_groups_profiles')->set("$column = $value")->where("id = $profileID");
		$this->_db->setQuery($query);

		try
		{
			return (bool) $this->_db->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	private function updateCategoryPublishing($categoryID, $value)
	{
		$query = $this->_db->getQuery(true);
		$query->update('#__categories')->set("published = $value")->where("id = $categoryID");
		$this->_db->setQuery($query);

		try
		{
			return (bool) $this->_db->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Hides the public display of the user's profile. Access checks are performed in toggle.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function unpublish()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$profileIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));
		foreach ($profileIDs as $profileID)
		{
			if ($categoryID = THM_GroupsHelperProfiles::getCategoryID($profileID))
			{
				$this->updateCategoryPublishing($categoryID, 0);
			}

			$profileDisabled = $this->updateBinaryValue($profileID, 'published', 0);
			$contentDisabled = $this->updateBinaryValue($profileID, 'contentEnabled', 0);

			if (!$contentDisabled or !$profileDisabled)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Hides the public display of the user's profile. Access checks are performed in toggle.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function unpublishContent()
	{
		$app = JFactory::getApplication();

		if (!THM_GroupsHelperComponent::isManager())
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$profileIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));
		foreach ($profileIDs as $profileID)
		{
			if (!$categoryID = THM_GroupsHelperProfiles::getCategoryID($profileID))
			{
				continue;
			}

			$categoryDisabled = $this->updateCategoryPublishing($categoryID, 0);
			$contentDisabled  = $this->updateBinaryValue($profileID, 'contentEnabled', 0);

			if (!$categoryDisabled or !$contentDisabled)
			{
				return false;
			}
		}

		return true;
	}
}
