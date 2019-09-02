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

?>
<div class="modal hide fade" id="modal-profiles">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&#215;</button>
        <h3><?php echo JText::_('COM_THM_GROUPS_ADD_PROFILES'); ?></h3>
    </div>
    <div class="modal-body modal-batch form-horizontal">
        <?php foreach ($this->filterForm->getGroup('profiles') as $profilesField) : ?>
            <div class='control-group'>
                <div class='control-label'>
                    <?php echo $profilesField->label; ?>
                </div>
                <div class='controls'>
                    <?php echo $profilesField->input; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('profile.batch');">
            <?php echo JText::_('JSAVE'); ?>
        </button>
        <button class="btn" type="button" data-dismiss="modal">
            <?php echo JText::_('JCANCEL'); ?>
        </button>
    </div>
</div>