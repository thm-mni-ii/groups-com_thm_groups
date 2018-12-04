<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim,  <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Only show groups if there are multiple, one index is taken by the name
$showGroups = (count($this->profiles) - 1) > 1;

foreach ($this->profiles as $roleAssociations) {

    $groupSpan = $showGroups ? '<span class="group-title">' . $roleAssociations['name'] . '</span>' : '';

    // Only show roles if there are multiple, one index is taken by the name
    $showRoles = (count($roleAssociations) - 1) > 1;

    foreach ($roleAssociations as $assocID => $role) {

        // The group name requires no further processing
        if ($assocID == 'name' or empty($role['profiles'])) {
            continue;
        }

        if ($showGroups or $showRoles) {

            $roleSpan = ($showRoles and !empty($role['name'])) ?
                '<span class="role-title">' . $role['name'] . '</span>' : '';

            if (empty($groupSpan)) {
                $header = $roleSpan;
            } elseif (empty($roleSpan)) {
                $header = $groupSpan;
            } else {
                $header = "$groupSpan: $roleSpan";
            }

            echo '<div class="role-heading">' . $header . '</div>';
        }

        $this->renderRows($role['profiles']);
    }
}
