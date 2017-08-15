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

$profileCount = 0;
$lastProfile  = count($this->profiles) - 1;
$row          = '';
$rowIndex     = 0;

foreach ($this->profiles as $profileID => $profileAttributes)
{
	// Skip profiles with no surname
	if (empty($profileAttributes[2]))
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

	$row .= $this->getProfileContainer($profileID, $profileAttributes, $this->columns == 2);

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