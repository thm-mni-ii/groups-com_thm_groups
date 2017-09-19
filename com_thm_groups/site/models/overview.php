<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsModelOverview
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/group.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';
jimport('joomla.filesystem.path');

/**
 * Class provides methods to provide information for an overview of profiles associated with a group.
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 */
class THM_GroupsModelOverview extends JModelLegacy
{
	/**
	 * Method to get group number
	 *
	 * @return int group id
	 */
	public function getGroupNumber()
	{
		return JFactory::getApplication()->getParams()->get('groupID');
	}

	public function getProfilesByLetter($groupID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->select("DISTINCT user.id AS id, sname.value as surname");
		$query->select("fname.value as forename");
		$query->select("allAttr.published as published");
		$query->select("user.injoomla as injoomla");
		$query->select("pretitle.value as title");
		$query->select("posttitle.value as posttitle");
		$query->from("#__thm_groups_usergroups_roles as groups");
		$query->leftJoin("#__thm_groups_users_usergroups_roles AS userRoles ON groups.ID = userRoles.usergroups_rolesID");
		$query->leftJoin("#__thm_groups_users AS user ON user.id = userRoles.usersID");
		$query->leftJoin("#__thm_groups_users_attribute AS allAttr ON allAttr.usersID = user.id");
		$query->leftJoin("#__thm_groups_users_attribute AS sname ON sname.usersID = user.id AND sname.attributeID = 2");
		$query->leftJoin("#__thm_groups_users_attribute AS fname ON fname.usersID = user.id AND fname.attributeID = 1");
		$query->leftJoin("#__thm_groups_users_attribute AS pretitle ON pretitle.usersID = user.id AND pretitle.attributeID = '5'");
		$query->leftJoin("#__thm_groups_users_attribute AS posttitle ON posttitle.usersID = user.id AND posttitle.attributeID = '7'");
		$query->where("allAttr.published = 1");
		$query->where("user.published = 1");

		$query->where("groups.usergroupsID = " . $groupID);
		$query->order("surname");
		$dbo->setQuery($query);

		try
		{
			$items = $dbo->loadObjectList();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return array();
		}

		$profiles = array();
		foreach ($items as $profile)
		{
			// Normal substring messes up special characters
			$letter = strtoupper(mb_substr($profile->surname, 0, 1));
			switch ($letter)
			{
				case 'Ä':
					$letter = 'A';
					break;
				case 'Ö':
					$letter = 'O';
					break;
				case 'Ü':
					$letter = 'U';
					break;
				default:
					break;
			}
			if (!array_key_exists($letter, $profiles))
			{
				$profiles[$letter] = array();
			}
			$profiles[$letter][] = $profile;
		}

		return $profiles;
	}
}
