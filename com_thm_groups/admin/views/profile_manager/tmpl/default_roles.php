<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

?>
<div class="modal hide fade" id="collapseModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&#215;</button>
        <h3><?php echo JText::_('COM_THM_GROUPS_BATCH_SELECT_GROUPS_ROLES'); ?></h3>
    </div>
    <div class="modal-body modal-batch form-horizontal">
        <div class="control-group">
            <div class="control-label">
                <label for="batch-groups-id">
                    <?php echo JText::_('COM_THM_GROUPS_GROUPS') ?>
                </label>
            </div>
            <div class="controls">
                <select name="batch-groups" id="batch-groups-id">
                    <option value=""><?php echo JText::_('JSELECT') ?></option>
                    <?php echo JHtml::_('select.options', $this->groups); ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label for="batch-roles-id">
                    <?php echo JText::_('COM_THM_GROUPS_ROLES') ?>
                </label>
            </div>
            <div class="controls" id="roles-div-id">
                <select name="batch-roles" id="batch-roles-id" multiple></select>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button type="button" class="btn btn-success" id="batch-add-btn">
                    <?php echo JText::_('COM_THM_GROUPS_ADD_TO_SELECTED_ASSOCIATION'); ?>
                </button>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label for="batch-group">
                    <?php echo JText::_('COM_THM_GROUPS_SELECTED_ASSOCIATIONS') ?>:
                </label>
            </div>
            <div class="controls">
                <div id="group-roles-id"><?php echo JText::_('JNONE'); ?></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('profile.batch');">
            <?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
        </button>
        <button type="button" class="btn" data-dismiss="modal">
            <?php echo JText::_('JCANCEL'); ?>
        </button>
    </div>
    <input type="hidden" name="batch-data" id="batch-data" value="">
</div>

