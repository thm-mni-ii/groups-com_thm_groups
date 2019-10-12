<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

?>
<form id="adminForm" name="adminForm" class="form-horizontal form-validate site-edit"
      action="<?php echo JURI::base(); ?>" method="post" enctype="multipart/form-data">
    <?php echo $this->getToolbar(); ?>
    <div class="form-horizontal groups-site">
        <?php foreach (THM_GroupsHelperAttributes::getAttributeIDs() as $attributeID) : ?>
            <?php echo THM_GroupsHelperAttributes::getInput($attributeID, $this->profileID); ?>
        <?php endforeach; ?>
    </div>
    <input type='hidden' id='jform_name' name='jform[name]' value='<?php echo $this->name; ?>'/>
    <input type='hidden' id='jform_profileID' name='jform[profileID]' value='<?php echo $this->profileID; ?>'/>
    <input type="hidden" name="option" value="com_thm_groups"/>
    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
    <?php echo $this->getToolbar(); ?>
</form>
