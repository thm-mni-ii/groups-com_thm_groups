<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.media
 * @name        THM_GroupsHelperRole
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions role entities
 *
 * @category  Joomla.Component.Media
 * @package   com_thm_groups
 */
class THM_GroupsHelperRole
{
	/**
	 * Retrieves the name of the role by means of its association with a group.
	 *
	 * @param   int  $assocID the id of the group -> role association
	 * @param   bool $block   whether redundant roles ('Mitglied') should be blocked
	 *
	 * @return  string the name of the role referenced in the association
	 */
	public static function getNameByAssoc($assocID, $block)
	{
		$dbo = JFactory::getDbo();

		$query = $dbo->getQuery(true);
		$query
			->select("roles.name, roles.id")
			->from('#__thm_groups_usergroups_roles AS groups')
			->innerJoin('#__thm_groups_roles AS roles ON groups.rolesID = roles.id')
			->where("groups.ID = '$assocID'");

		$dbo->setQuery($query);

		try
		{
			$role = $dbo->loadAssoc();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return '';
		}

		// Role ID 1 is 'Mitglied', which is implied.
		return (empty($role['name']) OR ($block AND $role['id'] == 1)) ? '' : $role['name'];
	}
}