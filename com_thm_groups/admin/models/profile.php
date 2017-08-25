<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        user model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/content.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelProfile extends JModelLegacy
{
	/**
	 * Associates a group and potentially multiple roles with the selected users
	 *
	 * @return  bool true on success, otherwise false.
	 */
	public function batch()
	{
		$app = JFactory::getApplication();

		if (!JFactory::getUser()->authorise('core.admin', 'com_thm_groups'))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$selectedUsers = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', array(), 'array'));

		if (empty($selectedUsers))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_PROFILE_SELECTED'), 'error');

			return false;
		}

		$requestedAssocs = json_decode(urldecode($app->input->getString('batch-data')), true);

		if (empty($requestedAssocs))
		{
			$app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

			return false;
		}

		$usersMapped = $this->setJoomlaAssociations($selectedUsers, $requestedAssocs);

		if (!$usersMapped)
		{
			return false;
		}

		$success = $this->setGroupsAssociations($selectedUsers, $requestedAssocs);

		$this->cleanCache();

		return $success;
	}

	/**
	 * Fixes file path resolution problems stemming from incorrect directory separators.
	 *
	 * @param   string $path the configured local path
	 *
	 * @return  string  the corrected path
	 */
	private function correctPathDS($path)
	{
		if (DIRECTORY_SEPARATOR == '/')
		{
			return str_replace('\\', '/', $path);
		}
		elseif (DIRECTORY_SEPARATOR == '\\')
		{
			return str_replace('/', '\\', $path);
		}

		return $path;
	}

	/**
	 * Create content category for user(s)
	 *
	 * @param   array $profileIDs array with ids
	 *
	 * @return  void
	 */
	private function createCategory($profileIDs)
	{
		foreach ($profileIDs as $profileID)
		{
			if (!THM_GroupsHelperContent::profileCategoriesExist($profileID))
			{
				THM_GroupsHelperContent::createProfileCategory($profileID);
			}
		}
	}

	/**
	 * Deletes user from a group both in Joomla and in THM Groups
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function deleteGroupAssociation()
	{
		$app = JFactory::getApplication();

		if (!JFactory::getUser()->authorise('core.admin', 'com_thm_groups'))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$profileID = $app->input->getInt('profileID', 0);
		$groupID   = $app->input->getInt('groupID', 0);

		$userAssocs       = $this->getUserAssociations(array($profileID));
		$assocIDs         = $this->getAssocIDs($groupID);
		$disposableAssocs = array();

		foreach ($userAssocs as $key => $assoc)
		{
			if (in_array($assoc['assocID'], $assocIDs))
			{
				array_push($disposableAssocs, $assoc['id']);
			}
		}

		$groupsQuery = $this->_db->getQuery(true);
		$groupsQuery->delete('#__thm_groups_users_usergroups_roles')
			->where("id IN ('" . implode("','", $disposableAssocs) . "')");
		$this->_db->setQuery($groupsQuery);

		try
		{
			$success = $this->_db->execute();
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

		// Allow deletion of Joomla user group association if the user is associated with more than one user group.
		if (count(JFactory::getUser($profileID)->groups) > 1)
		{
			$joomlaQuery = $this->_db->getQuery(true);
			$joomlaQuery->delete('#__user_usergroup_map')->where("user_id = $profileID AND group_id = $groupID");
			$this->_db->setQuery($joomlaQuery);

			try
			{
				$this->_db->execute();
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
		}

		return true;
	}

	/**
	 * Deletes the value for a specific profile picture attribute
	 *
	 * @param int $profileID   the id of the profile with which the picture is associated.
	 * @param int $attributeID the id of the attribute under which the value is stored.
	 *
	 * @return mixed
	 */
	public function deletePicture($profileID = 0, $attributeID = 0)
	{
		$app         = JFactory::getApplication();
		$profileID   = $app->input->getInt('profileID', $profileID);
		$attributeID = $app->input->getString('attributeID', $attributeID);

		if (!THM_GroupsHelperComponent::canEditProfile($profileID))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$selectQuery = $this->_db->getQuery(true);
		$selectQuery->select('value')
			->from('#__thm_groups_users_attribute')
			->where("usersID = '$profileID'")
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

		$filePath = $this->getPicturePath($attributeID);

		if (file_exists(realpath(JPATH_ROOT . $filePath . $fileName)))
		{
			unlink(realpath(JPATH_ROOT . $filePath . $fileName));
		}

		// Update new picture filename
		$updateQuery = $this->_db->getQuery(true);

		// Update the database with new picture information
		$updateQuery->update('#__thm_groups_users_attribute')
			->set("value = ''")
			->where("usersID = '$profileID'")
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

		if (!JFactory::getUser()->authorise('core.admin', 'com_thm_groups'))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$groupID   = $app->input->getInt('groupID', 0);
		$profileID = $app->input->getInt('profileID', 0);
		$roleID    = $app->input->getInt('roleID', 0);

		$idToDelete = $this->getAssocID($groupID, $roleID);

		$query = $this->_db->getQuery(true);

		$query
			->delete('#__thm_groups_users_usergroups_roles')
			->where("usersID = $profileID AND usergroups_rolesID = $idToDelete");

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
	 * Retrieves the id of a specific usergroup/role association.
	 *
	 * @param   int $groupID the id of the Joomla / THM Groups user group
	 * @param   int $roleID  the id of the role
	 *
	 * @return int the id of the association on success, otherwise 0
	 */
	private function getAssocID($groupID, $roleID)
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('ID')
			->from('#__thm_groups_usergroups_roles')
			->where("usergroupsID = '$groupID'")
			->where("rolesID = '$roleID'");

		$this->_db->setQuery($query);

		try
		{
			$result = $this->_db->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return 0;
		}

		return empty($result) ? 0 : $result;
	}

	/**
	 * Returns a list of usergroup/role association ids.
	 *
	 * @param   int $groupID the Joomla / THM Groups user group ids
	 *
	 * @return array the ids associated with the group
	 */
	private function getAssocIDs($groupID)
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('ID')
			->from('#__thm_groups_usergroups_roles')
			->where("usergroupsID = '$groupID'");

		$this->_db->setQuery($query);

		try
		{
			$assocIDs = $this->_db->loadColumn();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return array();
		}

		return empty($assocIDs) ? array() : $assocIDs;
	}

	/**
	 * Returns a list of group assoc ids matching the request data
	 *
	 * @param   array $requestedAssocs An array with groups and roles
	 *
	 * @return  array with ids
	 */
	private function getGroupAssociations($requestedAssocs)
	{
		$assocs = array();

		foreach ($requestedAssocs as $requestedAssoc)
		{
			foreach ($requestedAssoc['roles'] as $role)
			{
				$query = $this->_db->getQuery(true);
				$query->select('ID as id')
					->from('#__thm_groups_usergroups_roles')
					->where("usergroupsID = '{$requestedAssoc['id']}'")
					->where("rolesID = {$role['id']}");
				$this->_db->setQuery($query);

				try
				{
					$assocID = $this->_db->loadResult();
				}
				catch (Exception $exception)
				{
					JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

					return array();
				}

				$assocs[$assocID] = $assocID;
			}
		}

		return $assocs;
	}

	/**
	 * Gets the local path that is needed to save the picture to the filesystem.
	 *
	 * @param   int $attributeID the attribute id of the picture
	 *
	 * @return  mixed
	 */
	private function getPicturePath($attributeID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('options')->from('#__thm_groups_attribute')->where("id = '$attributeID'");
		$dbo->setQuery($query);

		try
		{
			$optionsString = $dbo->loadResult();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		$options = json_decode($optionsString, true);

		if (empty($options) OR empty($options['path']))
		{
			return true;
		}

		$configuredPath = $options['path'];
		$position       = strpos($options['path'], '/images/');

		if ($position === false)
		{
			return true;
		}

		return substr($configuredPath, $position);
	}

	/**
	 * Returns an array with profile associations matching the request data
	 *
	 * @param   array $profileIDs An array with user ids
	 *
	 * @return array
	 */
	private function getUserAssociations($profileIDs)
	{
		$query = $this->_db->getQuery(true);

		// First, we need to check if the group-role relationship is already assigned to the user
		$query->select('ID AS id, usersID AS profileID, usergroups_rolesID AS assocID')
			->from('#__thm_groups_users_usergroups_roles')
			->where("usersID IN ('" . implode(',', $profileIDs) . "')")
			->order('usersID');

		$this->_db->setQuery($query);

		try
		{
			$assocs = $this->_db->loadAssocList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return array();
		}

		return empty($assocs) ? array() : $this->_db->loadAssocList();
	}

	/**
	 * Allows the public display of the user's profile. Access checks are performed in toggle.
	 *
	 * @return bool
	 */
	public function publish()
	{
		$input = JFactory::getApplication()->input;
		$input->set('attribute', 'published');
		$input->set('value', '1');

		return $this->toggle();
	}

	/**
	 * Allows public display of personal content. Access checks are performed in toggle.
	 *
	 * @return bool
	 */
	public function publishContent()
	{
		$input = JFactory::getApplication()->input;
		$input->set('attribute', 'qpPublished');
		$input->set('value', '1');

		return $this->toggle();
	}

	/**
	 * Replaces blank characters in array keys generated by parsing div ids with underscore characters.
	 *
	 * @param   array &$array the array to be processed
	 *
	 * @TODO: Find a more elegant solution to this problem before it becomes one, or disallow the use of such characters
	 *      in the names of profile attributes.
	 *
	 * @return  void modifies the given array by reference
	 */
	private function replaceBlanks(&$array)
	{
		$array = array_combine(
			array_map(
				function ($str) {
					return str_replace("_", " ", $str);
				},
				array_keys($array)
			),
			array_values($array)
		);
	}

	/**
	 * Saves user profile information
	 *
	 * @TODO  Add handling of failures
	 *
	 * @return  mixed  int profile ID on success, otherwise false
	 */
	public function save()
	{
		$app  = JFactory::getApplication();
		$data = $app->input->get('jform', array(), 'array');

		// Ensuring int will fail access checks on manipulated ids.
		$profileID = $data['profileID'];

		if (!THM_GroupsHelperComponent::canEditProfile($profileID))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		// Profile attribute names can have blanks, which makes their handling as key names problematic.
		$this->replaceBlanks($data);

		$dbo = JFactory::getDbo();
		$dbo->transactionStart();

		$success = $this->saveValues($data);

		if (!$success)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
			$dbo->transactionRollback();

			return false;
		}

		$dbo->transactionCommit();

		return $profileID;
	}

	/**
	 * Saves the cropped image that was uploaded via ajax in the profile_edit.view
	 *
	 * @return  bool|mixed|string
	 */
	public function saveCropped()
	{
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$profileID = $input->getInt('profileID');

		if (!THM_GroupsHelperComponent::canEditProfile($profileID))
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
		$allowedExtensions = array('bmp', 'gif', 'jpg', 'jpeg', 'png', 'BMP', 'GIF', 'JPG', 'JPEG', 'PNG');
		$invalid           = ($file['size'] > 10000000 OR !in_array(pathinfo($filename, PATHINFO_EXTENSION), $allowedExtensions));

		if ($invalid)
		{
			return false;
		}

		$attributeID = $input->get('attributeID');
		$newFileName = $profileID . "_" . $attributeID . "." . pathinfo($filename, PATHINFO_EXTENSION);
		$pathAttr    = $this->getPicturePath($attributeID);
		$path        = $this->correctPathDS(JPATH_ROOT . $pathAttr . $newFileName);

		$profile = THM_GroupsHelperProfile::getProfile($profileID);

		// Out with the old
		$deleted = $this->deletePicture($profile, $attributeID);
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
	 * @param   array $formData the submitted form data
	 *
	 * @return  bool true on success, otherwise false
	 */
	private function saveValues($formData)
	{
		$profileID = $formData['profileID'];

		foreach ($formData as $fieldName => $values)
		{
			if (is_string($values))
			{
				continue;
			}

			$query = $this->_db->getQuery(true);
			$query->update('#__thm_groups_users_attribute');

			$value = $this->_db->q(trim($values['value']));
			$query->set("value = $value");

			$published = empty($values['published']) ? 0 : 1;
			$query->set("published = '$published'");

			$query->where("usersID = '$profileID'");

			$attributeID = (int) $values['attributeID'];
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
	 * Perform batch operations.
	 * The main idea for this function is to assign groups-roles
	 * relationships to a user.
	 * The function can be extended to perform another
	 * batch operations.
	 *
	 * @param   array $profileIDs      the profile IDs which assignments are being edited
	 * @param   array $requestedAssocs an array of groups and roles
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 */
	private function setGroupsAssociations($profileIDs, $requestedAssocs)
	{
		$app = JFactory::getApplication();

		$userAssocs  = $this->getUserAssociations($profileIDs);
		$groupAssocs = $this->getGroupAssociations($requestedAssocs);

		$performInsert = false;
		$query         = $this->_db->getQuery(true);

		foreach ($profileIDs as $profileID)
		{
			foreach ($groupAssocs as $groupAssocID)
			{
				$assocExists = false;

				foreach ($userAssocs as $userAssoc)
				{
					$notUser = $profileID != $userAssoc['profileID'];

					if ($notUser)
					{
						continue;
					}

					$notAssoc = $groupAssocID != $userAssoc['assocID'];

					if ($notAssoc)
					{
						continue;
					}

					$assocExists = true;
				}

				if (!$assocExists)
				{
					$performInsert = true;
					$query->values("'$profileID', '$groupAssocID'");
				}
			}
		}

		// All requested associations already exist.
		if (!$performInsert)
		{
			return true;
		}

		$query->insert('#__thm_groups_users_usergroups_roles')->columns(array('usersID', 'usergroups_rolesID'));
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
	 * Maps users to Joomla user groups.
	 *
	 * @param   array $profileIDs an array with profile ids (joomla user ids)
	 * @param   array $batchData  an array with groups and roles
	 *
	 * @return bool true on success, otherwise false
	 */
	private function setJoomlaAssociations($profileIDs, $batchData)
	{
		$existingQuery = $this->_db->getQuery(true);
		$existingQuery->select('id')->from('#__user_usergroup_map')
			->where("user_id IN ('" . implode("','", $profileIDs) . "')");
		$query = $this->_db->getQuery(true);
		$query->insert('#__user_usergroup_map')->columns('user_id, group_id');
		$values = array();

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
	 */
	public function toggle()
	{
		$app = JFactory::getApplication();

		if (!JFactory::getUser()->authorise('core.admin', 'com_thm_groups'))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$input         = $app->input;
		$selectedUsers = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', array(), 'array'));
		$toggleID      = $input->getInt('id', 0);
		$value         = $input->getBool('value', false);

		if (empty($selectedUsers) AND empty($toggleID))
		{
			// No selection, should not occur.
			return false;
		}

		// Toggle button was used.
		elseif (empty($selectedUsers))
		{
			$selectedUsers = array($toggleID);

			// Toggled values reflect the current value not the desired value
			$value = !$value;
		}

		// The binary attribute to toggle and the value to set it to
		$column = $input->getString('attribute', '');

		// We don't know what to toggle
		if (empty($column))
		{
			return false;
		}

		if ($column == 'qpPublished')
		{
			$this->createCategory($selectedUsers);
		}

		$query = $this->_db->getQuery(true);

		$selectedString = implode("','", $selectedUsers);
		$query->update('#__thm_groups_users')->set("$column = '$value'")->where("id IN ( '$selectedString' )");
		$this->_db->setQuery($query);

		try
		{
			$success = $this->_db->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return empty($success) ? false : true;
	}

	/**
	 * Hides the public display of the user's profile. Access checks are performed in toggle.
	 *
	 * @return bool
	 */
	public function unpublish()
	{
		$input = JFactory::getApplication()->input;
		$input->set('attribute', 'published');
		$input->set('value', '0');

		return $this->toggle();
	}

	/**
	 * Hides public display of personal content. Access checks are performed in toggle.
	 *
	 * @return bool
	 */
	public function unpublishContent()
	{
		$input = JFactory::getApplication()->input;
		$input->set('attribute', 'qpPublished');
		$input->set('value', '0');

		return $this->toggle();
	}
}
   