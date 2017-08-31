<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsModelPool_Ajax
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
/** @noinspection PhpIncludeInspection */
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/group.php';
/** @noinspection PhpIncludeInspection */
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';

/**
 * Class provides methods to retrieve data for pool ajax calls
 *
 * @category    Joomla.Component.Site
 * @package     thm_groups
 * @subpackage  com_thm_groups.site
 */
class THM_GroupsModelProfile_Ajax extends JModelLegacy
{

	/**
	 * Gets profile options for use in content
	 *
	 * @return string the concatenated profile options
	 */
	public function getContentOptions()
	{
		$groupID = JFactory::getApplication()->input->getInt('groupID');

		if (empty($groupID))
		{
			return '[]';
		}

		$profiles   = [];
		$profileIDs = THM_GroupsHelperGroup::getProfileIDs($groupID);

		foreach ($profileIDs as $profileID)
		{
			$surname = THM_GroupsHelperProfile::getSurname($profileID);

			if (empty($surname))
			{
				continue;
			}

			$surname     = JFilterOutput::stringURLSafe($surname);
			$displayName = THM_GroupsHelperProfile::getDisplayName($profileID, true);
			$link        = "?option=com_thm_groups&view=profile&profileID=$profileID&groupID=$groupID&name=$surname";

			$profiles[$profileID] = ['id' => $profileID, 'name' => $displayName, 'link' => $link];
		}

		return json_encode($profiles);
	}
}
