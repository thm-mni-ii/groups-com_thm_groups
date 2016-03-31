<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name		THMGroupsModelProfile
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Dieudonne Timma, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';

/**
 * THMGroupsModelProfile class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 */
class THM_GroupsModelProfile extends JModelItem
{
    public $groupID;

    public $userID;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $input = JFactory::getApplication()->input;
        $profileID = $input->getint('userID', 0);
        $published = empty($profileID)? false : THM_GroupsHelperProfile::isPublished($profileID);
        if (!$published)
        {
            $exc = new Exception(JText::_('COM_THM_GROUPS_PROFILE_NOT_FOUND'), '404');
            JErrorPage::render($exc);
        }
        $this->profileID = $profileID;
        $this->groupID = $input->getint('groupID', 1);
        parent::__construct();
    }


    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        return THM_GroupsHelperProfile::getProfile($this->profileID, $this->groupID);
    }
}
