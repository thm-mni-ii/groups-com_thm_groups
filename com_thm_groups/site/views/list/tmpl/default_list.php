<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

// If the template was called on its own use all profiles, otherwise only the profiles of a particular letter.
if (empty($this->letterProfiles))
{
    $profilesArray = $this->profiles;
    $tolerance = 3;
    $totalUsers = THM_GroupsHelperGroup::getUserCount($this->groupID);
}
else
{
    $profilesArray = $this->letterProfiles;
    $tolerance = 0;
    $totalUsers = count(array_values($this->letterProfiles)[0]);
}
$columns = $this->params->get('columnCount', 3);

// TODO: make this configurable
$maxColumnSize = ceil(($totalUsers) / $columns) + $tolerance;

$currentProfilesCount = 0;
$currentRowCount = 1;
$totalLettersOutput = 0;
$totalRowsOutput = 0;


echo '<div class="profiles-container">';
foreach ($profilesArray as $letter => $profiles)
{
    foreach ($profiles as $profile)
    {
        if ($currentRowCount == 1)
        {
            echo '<div class="ym-g33 ym-gl">';
        }
        $showLetter = ($currentProfilesCount == 0 OR ($currentRowCount == 1 AND $currentProfilesCount));
        if ($showLetter)
        {
            echo '<ul>';
            echo '<li class="letter">' . $letter . '</li>';
            echo '<li><ul>';
        }

        echo '<li>';
        echo $this->getProfileLink($profile);
        echo '</li>';

        $currentProfilesCount++;
        $letterDone = $currentProfilesCount == count($profiles);
        if ($letterDone)
        {
            echo '</ul></li></ul>';
            $currentProfilesCount = 0;
            $totalLettersOutput++;

            // A little more complicated then it should be because of associative array use
            $temp = array_slice($this->profiles, $totalLettersOutput, 1);
            $next = array_shift($temp);
            $nextSize = count($next);
        }

        $currentRowCount++;
        $totalRowsOutput++;
        $rowsAvailable = $maxColumnSize - $currentRowCount;
        $startNextLetter = (!empty($nextSize) AND $nextSize < $rowsAvailable);
        $maxRowsReached = $maxColumnSize == $currentRowCount;
        $done = $totalLettersOutput == count($this->profiles);
        if (($letterDone AND !$startNextLetter) OR $maxRowsReached OR $done)
        {
            echo '</div>';
            $currentRowCount = 1;
        }
    }
}
echo '</div>';
