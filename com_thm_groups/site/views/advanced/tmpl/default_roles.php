<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @author      James Antrim,  <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

$rows     = '';
$rowIndex = 0;

foreach ($this->profiles as $groupID => $assocs)
{
	$groupSpan = '<span class="group-title">' . $this->profiles[$groupID]['title'] . '</span>';

	foreach ($assocs as $assocID => $data)
	{
		if ($assocID == 'title')
		{
			continue;
		}

		$roleSpan = empty($data['name'])? '' : '<span class="role-title">' . $data['name'] . '</span>';

		if (empty($groupSpan))
		{
			$assocName = $roleSpan;
		}
		elseif (empty($roleSpan))
		{
			$assocName = $groupSpan;
		}
		else
		{
			$assocName = "$groupSpan: $roleSpan";
		}

		// Only print headings if there are differing groups/roles. Role count reduced because of the group name index.
		if (count($this->profiles) > 1 OR (count($assocs) - 1) > 1)
		{
			echo '<div class="role-heading">' . $assocName . '</div>';
		}

		$profileCount = 0;
		$lastProfile  = count($data['profiles']) - 1;
		$half         = ($this->columns == 2 AND count($data['profiles']) > 1);

		foreach ($data['profiles'] AS $profileID => $attributes)
		{
			// Skip profiles with no surname
			if (empty($attributes[2]))
			{
				// Reduce the end profile count to compensate for lack of output
				$lastProfile = $lastProfile - 1;
				continue;
			}

			$startRow = ($profileCount % $this->columns == 0);

			if ($startRow)
			{
				$rowIndex++;
				$row = '<div class="row-container">';
			}

			$row .= $this->getProfileContainer($profileID, $attributes, $half, $groupID);

			$endRow = ($profileCount % $this->columns == $this->columns - 1 OR $profileCount == $lastProfile);

			if ($endRow)
			{
				// Ensure the row container wraps around the profiles
				$row .= '<div class="clearFix"></div>';

				// Close the row
				$row .= '</div>';

				echo $row;
			}

			$profileCount++;
		}

		$rowIndex = 0;
	}
}
