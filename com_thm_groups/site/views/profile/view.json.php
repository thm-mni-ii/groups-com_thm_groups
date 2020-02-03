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

require_once HELPERS . 'profiles.php';
require_once HELPERS . 'menu.php';

/**
 * THMGroupsViewProfile class for component com_thm_groups
 */
class THM_GroupsViewProfile extends JViewLegacy
{
	/**
	 * Method to get display
	 *
	 * @param   Object  $tpl  template
	 *
	 * @return void
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$profileID = JFactory::getApplication()->input->getint('profileID', 0);
		$published = empty($profileID) ? false : THM_GroupsHelperProfiles::isPublished($profileID);

		if (!$published)
		{
			$exc = new Exception(JText::_('COM_THM_GROUPS_PROFILE_NOT_FOUND'), '404');
			JErrorPage::render($exc);
		}

		if (!$profile = THM_GroupsHelperProfiles::getRawProfile($profileID))
		{
			echo json_encode([]);

			return;
		}

		$nameAttributeIDs = [FORENAME, SURNAME, TITLE, POSTTITLE];
		$specialFieldIDs  = [FILE];

		$json = [
			'profileName' => THM_GroupsHelperProfiles::getDisplayName($profileID, true),
			'profileLink' => THM_GroupsHelperRouter::build(['view' => 'profile', 'profileID' => $profileID])
		];

		if (THM_GroupsHelperMenu::contentEnabled($profileID))
		{
			$contentParams = ['view' => 'content', 'profileID' => $profileID];
			$contents = THM_GroupsHelperMenu::getContent($profileID);
			$json['profileContents'] = [];

			foreach ($contents as $content)
			{
				$url = THM_GroupsHelperRouter::build($contentParams + ['id' => $content->id]);
				$json['profileContents'][$content->title] = $url;
			}
		}

		foreach ($profile as $attributeID => $properties)
		{
			// Suppress redundant (name) attributes and files
			if (in_array($attributeID, $nameAttributeIDs) or in_array($properties['fieldID'], $specialFieldIDs))
			{
				continue;
			}

			$json[$properties['label']] = $properties['value'];
		}

		echo json_encode($json);
	}
}
