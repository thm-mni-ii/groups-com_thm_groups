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
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/groups.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profiles.php';

/**
 * THMGroupsModelProfile class for component com_thm_groups
 */
class THM_GroupsModelProfile extends JModelItem
{
    public $profile;

    public $profileID;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $input = JFactory::getApplication()->input;

        $this->profileID = $input->getint('profileID', 0);

        $published = empty($this->profileID) ? false : THM_GroupsHelperProfiles::isPublished($this->profileID);

        if (!$published) {
            $exc = new Exception(JText::_('COM_THM_GROUPS_PROFILE_NOT_FOUND'), '404');
            JErrorPage::render($exc);
        }

        $this->profile = THM_GroupsHelperProfiles::getProfile($this->profileID, true);

        parent::__construct();
    }
}
