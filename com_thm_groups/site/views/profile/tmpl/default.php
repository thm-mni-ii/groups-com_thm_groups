<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
$skipIndexes = [1, 2, 5, 7];
?>
<div class="toolbar">
    <?php echo $this->getEditLink('class="btn btn-toolbar-thm"'); ?>
    <?php echo $this->getBackLink(); ?>
</div>
<div id="profile-container" class="profile-container row-fluid template-<?php echo $this->templateName; ?>">
    <div class="page-header">
        <h2><?php echo THM_GroupsHelperProfile::getDisplayName($this->profileID, true); ?></h2>
    </div>
    <?php $this->renderAttributes() ?>
    <div class="clearFix"></div>
</div>




