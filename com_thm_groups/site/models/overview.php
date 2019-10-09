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
jimport('joomla.filesystem.path');

/**
 * Class provides methods to provide information for an overview of profiles associated with a group.
 */
class THM_GroupsModelOverview extends JModelLegacy
{
	/**
	 * Retrieves the profiles available whose surnames sorted by the first letter of the first surname
	 *
	 * @return array an array of profiles grouped by letter
	 * @throws Exception
	 */
	public function getProfiles()
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->select("DISTINCT p.id AS id, sname.value AS surname");
		$query->select("fname.value AS forename");
		$query->select("allAttr.published AS published");
		$query->select("pretitle.value AS title");
		$query->select("posttitle.value AS posttitle");
		$query->from("#__thm_groups_role_associations AS ra");
		$query->leftJoin("#__thm_groups_profile_associations AS pa ON ra.id = pa.role_associationID");
		$query->leftJoin("#__thm_groups_profiles AS p ON p.id = pa.profileID");
		$query->leftJoin("#__thm_groups_profile_attributes AS allAttr ON allAttr.profileID = p.id");
		$query->leftJoin("#__thm_groups_profile_attributes AS sname ON sname.profileID = p.id AND sname.attributeID = 2");
		$query->leftJoin("#__thm_groups_profile_attributes AS fname ON fname.profileID = p.id AND fname.attributeID = 1");
		$query->leftJoin("#__thm_groups_profile_attributes AS pretitle ON pretitle.profileID = p.id AND pretitle.attributeID = '5'");
		$query->leftJoin("#__thm_groups_profile_attributes AS posttitle ON posttitle.profileID = p.id AND posttitle.attributeID = '7'");
		$query->where("allAttr.published = 1");
		$query->where("p.published = 1");


		$app    = JFactory::getApplication();
		$search = $app->input->get('search');
		if ($search !== null) {
			$app->getParams()->set('groupID', null);
		}

		if ($search)
		{
			$search = THM_GroupsHelperComponent::trim($search);

			if (!empty('search'))
			{
				$search      = THM_GroupsHelperComponent::transliterate($search);
				$search      = THM_GroupsHelperComponent::filterText($search);
				$searchTerms = explode('-', $search);
				$query->where("p.alias LIKE '%" . implode("%' AND p.alias LIKE '%", $searchTerms) . "%'");
			}
		}
		elseif ($groupID = $app->getParams()->get('groupID', 0))
		{
			$query->where("ra.groupID = '$groupID'");
		}

		$query->order("surname, forename");
		$dbo->setQuery($query);

		try
		{
			$items = $dbo->loadObjectList();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return [];
		}

		$profiles = [];
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
				$profiles[$letter] = [];
			}
			$profiles[$letter][] = $profile;
		}

		return $profiles;
	}
}
