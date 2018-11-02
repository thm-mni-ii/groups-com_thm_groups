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
$special = [TITLE, FORENAME, SURNAME, POSTTITLE];
?>
<div class="toolbar">
    <?php echo $this->getEditLink('class="btn btn-toolbar-thm"'); ?>
</div>
<div id="profile-container" class="profile-container row-fluid">
    <div class="page-header">
        <h2><?php echo THM_GroupsHelperProfiles::getDisplayName($this->profileID, true); ?></h2>
    </div>
    <?php echo THM_GroupsHelperProfiles::getDisplay($this->profileID); ?>
    <div class="clearFix"></div>
</div>




