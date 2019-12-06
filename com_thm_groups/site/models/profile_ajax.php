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
require_once HELPERS . 'groups.php';
require_once HELPERS . 'profiles.php';

/**
 * Class provides methods to retrieve data for pool ajax calls
 */
class THM_GroupsModelProfile_Ajax extends JModelLegacy
{

	/**
	 * Gets profile options for use in content
	 *
	 * @return string the concatenated profile options
	 * @throws Exception
	 */
	public function getContentOptions()
	{
		$groupID    = JFactory::getApplication()->input->getInt('groupID', 0);
		$profiles   = [];
		$profileIDs = THM_GroupsHelperGroups::getProfileIDs($groupID);

		foreach ($profileIDs as $profileID)
		{
			$displayName = THM_GroupsHelperProfiles::getDisplayName($profileID, true);
			if (empty($displayName))
			{
				continue;
			}

			$lnfName = THM_GroupsHelperProfiles::getLNFName($profileID);

			$link = JUri::base() . "?option=com_thm_groups&view=profile&profileID=$profileID";

			$profiles[$lnfName] = [
				'id'          => $profileID,
				'displayName' => $displayName,
				'link'        => $link,
				'sortName'    => $lnfName
			];
		}

		ksort($profiles);

		return json_encode($profiles);
	}
}
