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
/** @noinspection PhpIncludeInspection */
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/groups.php';
/** @noinspection PhpIncludeInspection */
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profiles.php';

/**
 * Class provides methods to retrieve data for pool ajax calls
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

        if (empty($groupID)) {
            return '[]';
        }

        $profiles   = [];
        $profileIDs = THM_GroupsHelperGroups::getProfileIDs($groupID);

        foreach ($profileIDs as $profileID) {
            $surname = THM_GroupsHelperProfiles::getSurname($profileID);

            if (empty($surname)) {
                continue;
            }

            $surname     = JFilterOutput::stringURLSafe($surname);
            $displayName = THM_GroupsHelperProfiles::getDisplayName($profileID, true);
            $link        = "?option=com_thm_groups&view=profile&profileID=$profileID&groupID=$groupID&name=$surname";

            $profiles[$profileID] = ['id' => $profileID, 'name' => $displayName, 'link' => $link];
        }

        return json_encode($profiles);
    }
}
