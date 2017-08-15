<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsModelProfile
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/group.php';
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

	public $profile;

	public $profileID;

	public $templateID;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$input           = JFactory::getApplication()->input;
		$this->profileID = $input->getint('profileID', 0);

		$published = empty($this->profileID) ? false : THM_GroupsHelperProfile::isPublished($this->profileID);

		if (!$published)
		{
			$exc = new Exception(JText::_('COM_THM_GROUPS_PROFILE_NOT_FOUND'), '404');
			JErrorPage::render($exc);
		}

		$params = JFactory::getApplication()->getParams();

		// Linked > Menu > Default
		$defaultGroupID = THM_GroupsHelperProfile::getDefaultGroup($this->profileID);
		$menuGroupID    = $params->get('groupID', $defaultGroupID);
		$this->groupID  = $input->getInt('groupID', $menuGroupID);

		$defaultTemplateID = THM_GroupsHelperGroup::getTemplateID($this->groupID);
		$this->templateID  = $params->get('templateID', $defaultTemplateID);

		$this->profile = THM_GroupsHelperProfile::getProfile($this->profileID, $this->templateID, true);

		parent::__construct();
	}
}
