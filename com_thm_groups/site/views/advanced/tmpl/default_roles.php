<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      James Antrim,  <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

$rows     = '';
$rowIndex = 0;

// One index is taken by the name
$groupCount = count($this->profiles) - 1;

foreach ($this->profiles as $groupID => $assocs) {
    $groupSpan = $groupCount > 1 ? '<span class="group-title">' . $this->profiles[$groupID]['title'] . '</span>' : '';

    // One index is taken up by the name
    $assocCount = count($assocs) - 1;

    foreach ($assocs as $assocID => $data) {
        if ($assocID == 'title') {
            continue;
        }

        $roleSpan = (empty($data['name']) or $assocCount == 1) ?
            '' : '<span class="role-title">' . $data['name'] . '</span>';

        if (empty($groupSpan)) {
            $assocName = $roleSpan;
        } elseif (empty($roleSpan)) {
            $assocName = $groupSpan;
        } else {
            $assocName = "$groupSpan: $roleSpan";
        }

        // Only print headings if there are differing groups/roles. Role count reduced because of the group name index.
        if ($groupCount > 1 or $assocCount > 1) {
            echo '<div class="role-heading">' . $assocName . '</div>';
        }

        $profileCount = 0;
        $lastProfile  = count($data['profiles']) - 1;
        $half         = ($this->columns == 2 and count($data['profiles']) > 1);

        foreach ($data['profiles'] as $profileID => $attributes) {
            // Skip profiles with no surname
            if (empty($attributes[2])) {
                // Reduce the end profile count to compensate for lack of output
                $lastProfile = $lastProfile - 1;
                continue;
            }

            $startRow = ($profileCount % $this->columns == 0);

            if ($startRow) {
                $rowIndex++;
                $row = '<div class="row-container">';
            }

            $row .= $this->getProfileContainer($profileID, $attributes, $half, $groupID);

            $endRow = ($profileCount % $this->columns == $this->columns - 1 or $profileCount == $lastProfile);

            if ($endRow) {
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
