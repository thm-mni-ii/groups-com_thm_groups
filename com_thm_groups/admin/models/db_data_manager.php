<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelRole
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/install.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/update.php';

/**
 * THM_GroupsModelDB_Data_Manager class for handling of migration actions
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelDB_Data_Manager extends JModelLegacy
{

	/**
	 * Facade
	 *
	 * @return  bool   true on success, false otherwise
	 *
	 * @throws Exception
	 */
	public function execute()
	{
		$action = JFactory::getApplication()->input->get('migration_action', array(), 'array');

		switch ($action[0])
		{
			case 'copy_data_from_joomla25_thm_groups_tables':
				$success = self::restoreData(true, 'customUpdate.sql');
				break;
			case 'copy_data_for_w_page_from_joomla25_thm_groups_tables':
				$success = self::restoreData(false, 'customUpdateForW.sql');
				break;
			case 'sync_users':
				$success = self::syncUsers();
				break;
			case 'convert_tables_in_new_textfields':
				$success = self::convertTablesInTextFields();
				break;
			default:
				$success = true;
		}

		return $success;
	}

	/**
	 * We copied with update script the data from _thm_groups_table_extra and _thm_groups_table.
	 * Table functionality will no more supported, because you can create, edit and delete tables
	 * with help of integrated editors for text fields.
	 *
	 * This function needs two tables from the old structure:
	 *  prefix_thm_groups_table
	 *  prefix_thm_groups_table_extra
	 *
	 * The function converts the old self-defined structure for tables into normal
	 * HTML tables, which will be saved as HTML-Code in new Attributes with the
	 * static type TEXTFIELD (see also dynamic type -> TEXTFIELD)
	 *
	 * @return  bool   true on success, false otherwise
	 *
	 * @throws Exception
	 */
	private static function convertTablesInTextFields()
	{
		$dbo = JFactory::getDbo();
		$dbo->transactionStart();

		$tableStructures = self::getTableStructures();

		// There is no structures of the type TABLE to process
		if (empty($tableStructures))
		{
			$dbo->transactionCommit();

			return true;
		}

		$tableStructuresCreated = self::createNewTextFieldFromTableStructure($tableStructures);

		if (empty($tableStructuresCreated))
		{
			$dbo->transactionRollback();

			return false;
		}

		$dbo->transactionCommit();

		return true;
	}

	/**
	 * Creates TEXTFIELD attributes from the table types
	 *
	 * @param   array $tableStructures Array with all structures from type TABLE
	 *
	 * @return  bool   true in success, false otherwise
	 */
	private static function createNewTextFieldFromTableStructure($tableStructures)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		foreach ($tableStructures as $structure)
		{
			$query->insert("#__thm_groups_attribute (dynamic_typeID, name, options, published)");

			// 2 as dynamicType, because by update it's a standard value for TEXTFIELD
			$value = array(2, $dbo->q($structure->field . 'COPY'), $dbo->q('{ "length" : "80", "required" : "false" }'), 1);
			$query->values(implode(',', $value));
			$dbo->setQuery($query);

			try
			{
				$dbo->execute();
			}
			catch (Exception $exception)
			{
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}

			$attrID = $dbo->insertid();

			$header       = self::getTableHeader($structure->id);
			$tableContent = self::getTableContent($structure->id);

			if (empty($tableContent) OR empty($header))
			{
				continue;
			}

			foreach ($tableContent as $table)
			{
				$validTable = !empty($table->value) && $table->value != '[]';
				$validUser  = $table->userid != 0;

				if ($validTable && $validUser)
				{
					$newTableContent = self::convertTableContent($header, $table->value);
					self::saveNewTableContentForUser($table->userid, $attrID, $dbo->q($newTableContent), $table->publish);
				}
			}
		}

		return true;
	}

	/**
	 * Saves the new, converted to HTML, tables for users which had previously structures of the type TABLE
	 *
	 * @param   int    $userID          User ID
	 * @param   int    $attrID          New attribute ID
	 * @param   string $newTableContent HTML Table
	 * @param   int    $publish         0 or 1
	 *
	 * @return  bool  true on success, false otherwise
	 */
	private static function saveNewTableContentForUser($userID, $attrID, $newTableContent, $publish)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->insert('#__thm_groups_users_attribute');
		$query->columns('usersID, attributeID, value, published');
		$value = array($userID, $attrID, $dbo->q($newTableContent), $publish);
		$query->values(implode(',', $value));
		$dbo->setQuery($query);

		try
		{
			$dbo->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Return header of a table by structure ID
	 *
	 * @param   int $structID structure ID
	 *
	 * @return  object on success, false otherwise
	 */
	private static function getTableHeader($structID)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('value')
			->from('#__thm_groups_table_extra')
			->where("structid = $structID");
		$db->setQuery($query);

		try
		{
			return $db->loadObject();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Converts old table structure in new one
	 *
	 * @param   object $tableHeader  Object with header string semicolon separated
	 *
	 * @param   string $tableContent JSON string with table's content
	 *
	 * @return  string  New table representation
	 */
	private static function convertTableContent($tableHeader, $tableContent)
	{
		$newContent = '<table class="table table-striped">';
		$newContent .= '<thead>';
		$newContent .= '<tr>';
		$head = explode(';', $tableHeader->value);

		if (empty($head))
		{
			return '';
		}

		foreach ($head as $headItem)
		{
			$newContent .= "<th>$headItem</th>";
		}

		$newContent .= '</tr>';
		$newContent .= '</thead>';
		$newContent .= '<tbody>';

		$arrValue = json_decode($tableContent);

		foreach ($arrValue as $row)
		{
			$newContent .= '<tr>';
			foreach ($row as $rowItem)
			{
				$newContent .= '<td>' . $rowItem . '</td>';
			}
			$newContent .= '</tr>';
		}

		$newContent .= '</tbody>';
		$newContent .= '</table>';

		return $newContent;
	}

	/**
	 * Retrieve content of tables
	 *
	 * @param   int $structID TABLE-Structure ID
	 *
	 * @return  array on success, false otherwise
	 */
	private static function getTableContent($structID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->select('userid, structid, value, publish')
			->from('#__thm_groups_table')
			->where("structid = $structID");

		$dbo->setQuery($query);

		try
		{
			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Returns all structures of type TABLE
	 *
	 * @return  array on success, false otherwise
	 */
	private static function getTableStructures()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->select('id, field, type, `order`')
			->from('#__thm_groups_structure')
			->where('type LIKE "TABLE"');
		$dbo->setQuery($query);
		try
		{
			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Fixes a problem with the not copied users after migration
	 * Example:
	 * You migrated some Joomla instance, but the old joomla instance become new users
	 * You copy this new users from the old joomla instance with J2XML to your new instance.
	 * You have users only in Joomla, but not in THM Groups.
	 *
	 * This method uses the algorithm of sync plugin to copy all basic user attributes like first name,
	 * second name, email and username. It creates also all other existing attributes, but without information.
	 * It assigns also group-role mapping to users.
	 *
	 * @return  bool   true on success, false otherwise
	 *
	 */
	private static function syncUsers()
	{
		$idsAndGroups = self::getUsersIDsAndGroups();

		if (empty($idsAndGroups))
		{
			return false;
		}

		return self::copyUserData($idsAndGroups);
	}

	/**
	 * Retrieves user IDs and groups
	 *
	 * @return  array  on success, false otherwise
	 */
	private static function getUsersIDsAndGroups()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->select('a.id, a.name, a.username, a.email, a.block, group_concat(c.group_id) AS groups')
			->from('#__users AS a')
			->leftJoin('#__thm_groups_users AS b ON a.id = b.id')
			->innerJoin('#__user_usergroup_map AS c ON a.id = c.user_id')
			->where('b.id is NULL')
			->group('a.id');

		$dbo->setQuery($query);

		try
		{
			return $dbo->loadAssocList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Copies users from Joomla into THM Groups using sync-plugin algorithm
	 *
	 * @param   array $users Array with users and groups
	 *
	 * @return  bool   true on success, false otherwise
	 *
	 * @throws  Exception
	 */
	private static function copyUserData($users)
	{
		$dbo = JFactory::getDbo();

		foreach ($users as $user)
		{
			$dbo->transactionStart();

			$userid = $user['id'];
			$name   = $user['name'];
			$email  = $user['email'];

			/* Joomla show a user if he is not blocked, "groups" show him if he is published.
			   So we have to invert the logic from blocked to published */
			$userPublished = $user['block'] == 0 ? 1 : 0;
			$groups        = explode(',', $user['groups']);

			// Cut the Name
			$nameArray = explode(" ", $name);
			$lastName  = end($nameArray);
			array_pop($nameArray);

			$deletefromname = array("(", ")", "Admin", "Webmaster");
			$namesplit      = explode(" ", str_replace($deletefromname, '', $name));
			array_pop($namesplit);
			$firstName = implode(" ", $nameArray);

			$query = $dbo->getQuery(true);
			$query
				->insert("`#__thm_groups_users` (`id`, `published`, `injoomla`, `canEdit`)")
				->values("'$userid', '$userPublished', '1', '0'");

			$dbo->setQuery($query);

			try
			{
				$dbo->execute();
			}
			catch (Exception $exception)
			{
				$dbo->transactionRollback();
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}

			$arr_attribute   = array(1, 2, 4);
			$attribute_query = $dbo->getQuery(true);
			$attribute_query
				->select('id')
				->from('#__thm_groups_attribute')
				->where('id NOT IN (' . implode(",", $arr_attribute) . ')');
			$dbo->setQuery($attribute_query);

			try
			{
				$attributes = $dbo->loadObjectList();
			}
			catch (Exception $exception)
			{
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}

			$query = $dbo->getQuery(true);
			$query
				->insert("#__thm_groups_users_attribute (usersID, attributeID, value, published)")
				->values(" $userid , 1 , '" . ucfirst($firstName) . "',1")
				->values(" $userid , 2 , '" . ucfirst($lastName) . "',1")
				->values(" $userid , 4 , '" . $email . "',1");

			if (!empty($attributes))
			{
				foreach ($attributes AS $attribute)
				{
					$query->values("$userid , $attribute->id , ' ', 0");
				}
			}

			$dbo->setQuery($query);

			try
			{
				$dbo->execute();
			}
			catch (Exception $exception)
			{
				$dbo->transactionRollback();
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}

			$userGroupsInserted = self::insertUserGroups($userid, $groups);
			if (!$userGroupsInserted)
			{
				$dbo->transactionRollback();

				return false;
			}

			$dbo->transactionCommit();
		}

		return true;
	}

	/**
	 * Method to insert the User groups
	 *
	 * @param   int   $userID     User id
	 *
	 * @param   array $userGroups User groups
	 *
	 * @return  bool   true on success, false otherwise
	 */
	private function insertUserGroups($userID, $userGroups)
	{
		$dbo              = JFactory::getDBO();
		$userGroupsIDList = array();

		foreach ($userGroups as $index => $value)
		{
			$query = $dbo->getQuery(true);
			$query
				->select("ID AS id")
				->from("#__thm_groups_usergroups_roles")
				->where("rolesID = 1")
				->where("usergroupsID =" . $value);
			$dbo->setQuery($query);

			try
			{
				$groupsRolesID = $dbo->loadObject()->id;
			}
			catch (Exception $exception)
			{
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}

			if (empty($groupsRolesID))
			{
				$insertGroupsQuery = $dbo->getQuery(true);
				$insertGroupsQuery->insert("#__thm_groups_usergroups_roles (usergroupsID,rolesID)");
				$insertGroupsQuery->values("$value, 1");
				$dbo->setQuery($insertGroupsQuery);

				try
				{
					$dbo->execute();
				}
				catch (Exception $exception)
				{
					JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

					return false;
				}

				$groupsRolesID = $dbo->insertid();
			}
			$userGroupsIDList[] = $groupsRolesID;
		}

		if (!empty($userGroupsIDList))
		{
			$setUserGroupsQuery = $dbo->getQuery(true);
			$setUserGroupsQuery->insert('#__thm_groups_users_usergroups_roles');
			$setUserGroupsQuery->columns('usersID, usergroups_rolesID');

			foreach ($userGroupsIDList as $id => $value)
			{
				$setUserGroupsQuery->values("$userID, $value");
			}
			$dbo->setQuery($setUserGroupsQuery);

			try
			{
				$dbo->execute();
			}
			catch (Exception $exception)
			{
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Processes and migrates structures and user data from the old THM Groups component for joomla 2.5
	 *
	 * @param   bool   $fixCategoriesTable Fix an old bug with categories for quickpages
	 *
	 * @param   string $scriptName         SQL-Script which will be launched, e.g. "customUpdate.sql", place script in /admin/sql/updates/
	 *
	 * @return  bool    true on success, false otherwise
	 */
	private static function restoreData($fixCategoriesTable = true, $scriptName)
	{
		$dbo = JFactory::getDbo();
		$dbo->transactionStart();

		if ($fixCategoriesTable)
		{
			$tablesFixed = self::fixCategoriesTable();
			if (!$tablesFixed)
			{
				$dbo->transactionRollback();

				return false;
			}
		}

		// TODO better names for both, functions and variables
		$dataCopied      = self::runUpdateSQLScript($scriptName);
		$updateCompleted = THM_Groups_Update_Script::update();

		if (!$dataCopied OR !$updateCompleted)
		{
			$dbo->transactionRollback();

			return false;
		}

		$dbo->transactionCommit();

		return true;
	}

	/**
	 * Changes created_user_id for all quickpages categories before data's import.
	 * It's an old bug by creation of categories for users
	 *
	 * @return  bool   true on success, false otherwise
	 */
	private static function fixCategoriesTable()
	{
		$dbo = JFactory::getDbo();

		// Get QP main category
		$query = $dbo->getQuery(true);
		$query
			->select('id')
			->from('#__categories')
			->where('path = "persoenliche-seiten" OR path = "quickpages"');
		$dbo->setQuery($query);

		try
		{
			$mainCat = $dbo->loadObject();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		// TODO check two conditions, because not sure if only "empty($mainCat->id)" is ok
		if (empty($mainCat) OR empty($mainCat->id))
		{
			JFactory::getApplication()->enqueueMessage('Quickpages root category is not found.', 'error');

			return false;
		}

		// Get all qp users categories
		$query = $dbo->getQuery(true);
		$query
			->select('id, path')
			->from('#__categories')
			->where("parent_id = $mainCat->id")
			->where('published = 1');
		$dbo->setQuery($query);

		try
		{
			$categories = $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		// No categories to process
		if (empty($categories))
		{
			return true;
		}

		foreach ($categories as $cat)
		{
			// persoenliche-seiten/max-mustermann-90
			$temp = explode('/', $cat->path);

			// $temp[1] = max-mustermann-90
			$temp = $temp[1];
			$temp = explode('-', $temp);

			// $userID = 90
			$userID = $temp[count($temp) - 1];

			// Change create_user_id for all qp categories
			$query = $dbo->getQuery(true);
			$query
				->update('#__categories')
				->set("created_user_id = $userID")
				->from('#__categories')
				->where("id = $cat->id");
			$dbo->setQuery($query);

			try
			{
				$dbo->execute();
			}
			catch (Exception $exception)
			{
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Launches the given SQL-Script
	 *
	 * @param   string $scriptName SQL-Script name
	 *
	 * @return  bool   true on success, false otherwise
	 */
	private static function runUpdateSQLScript($scriptName)
	{
		$dbo    = JFactory::getDbo();
		$buffer = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/sql/updates/' . $scriptName);
		if ($buffer === false)
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), 'error');

			return false;
		}
		// Create an array of queries from the sql file
		$queries = JDatabaseDriver::splitSql($buffer);

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query != '' && $query{0} != '#')
			{
				$dbo->setQuery($query);

				try
				{
					$dbo->execute();
				}
				catch (Exception $exception)
				{
					JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

					return false;
				}
			}
		}

		return true;
	}
}