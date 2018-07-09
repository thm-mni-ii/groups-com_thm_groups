<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer,  <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis,  <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll,  <alexander.boll@mni.thm.de>
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      James Antrim,  <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

echo '<h2 class="contentheading">' . $this->title . '</h2>';
echo '<div id="advanced-container" class="advanced-container row-fluid">';

if ($this->sort) {
    echo $this->loadTemplate('alpha');
} else {
    echo $this->loadTemplate('roles');
}

echo '</div>';
