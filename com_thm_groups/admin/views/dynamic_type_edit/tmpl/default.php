<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewDynamic_Type_Edit
 * @description THMGroupsViewDynamic_Type_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');
?>
<form action='index.php?option=com_thm_groups' method="post" name="adminForm" id="adminForm">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo 'TEST LEGEND'; ?></legend>
            <!--<div class="row-fluid">
                <div class="span6">
                    <?php /*foreach ($this->form->getFieldset() as $field): */ ?>
                        <div class="control-group">
                            <div class="control-label"><?php /*echo $field->label; */ ?></div>
                            <div class="controls"><?php /*echo $field->input; */ ?></div>
                        </div>

                    <?php /*endforeach; */ ?>
                </div>
            </div>-->
            <div class="control-label">
                <div class="control-label">
                    <?php echo $this->form->getLabel('name'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('name'); ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('staticTypeName'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->selectFieldStaticTypes ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('regex'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('regex'); ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('description'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('description'); ?>
                </div>
            </div>
        </fieldset>

        <!-- Hidden field for ID -->
        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>