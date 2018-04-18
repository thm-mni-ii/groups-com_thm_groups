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

        $query->select("DISTINCT profile.id AS id, sname.value as surname");
        $query->select("fname.value as forename");
        $query->select("allAttr.published as published");
        $query->select("profile.injoomla as injoomla");
        $query->select("pretitle.value as title");
        $query->select("posttitle.value as posttitle");
        $query->from("#__thm_groups_role_associations as roleAssoc");
        $query->leftJoin("#__thm_groups_associations AS assoc ON roleAssoc.ID = assoc.role_assocID");
        $query->leftJoin("#__thm_groups_profiles AS profile ON profile.id = assoc.profileID");
        $query->leftJoin("#__thm_groups_profile_attributes AS allAttr ON allAttr.profileID = profile.id");
        $query->leftJoin("#__thm_groups_profile_attributes AS sname ON sname.profileID = profile.id AND sname.attributeID = 2");
        $query->leftJoin("#__thm_groups_profile_attributes AS fname ON fname.profileID = profile.id AND fname.attributeID = 1");
        $query->leftJoin("#__thm_groups_profile_attributes AS pretitle ON pretitle.profileID = profile.id AND pretitle.attributeID = '5'");
        $query->leftJoin("#__thm_groups_profile_attributes AS posttitle ON posttitle.profileID = profile.id AND posttitle.attributeID = '7'");
        $query->where("allAttr.published = 1");
        $query->where("profile.published = 1");

        $query->where("roleAssoc.usergroupsID = " . $groupID);
        $query->order("surname");
        $dbo->setQuery($query);

        try {
            $items = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return array();
        }

        $profiles = array();
        foreach ($items as $profile) {
            // Normal substring messes up special characters
            $letter = strtoupper(mb_substr($profile->surname, 0, 1));
            switch ($letter) {
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
            if (!array_key_exists($letter, $profiles)) {
                $profiles[$letter] = array();
            }
            $profiles[$letter][] = $profile;
        }

        return $profiles;
    }
}
