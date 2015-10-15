<?php
/**
 * @version     v1.0.0
 * @package     THM_Groups
 * @subpackage  THM_Groups
 * @name        HelperPage
 * @author      Mehmet-Ali Pamukci, <mehmet.ali.pamukci@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 *
 */

defined('_JEXEC') or die ('Restricted access');
require_once ('helper.php');
?>

<div class="modal-header" rel="height=1000px;">

    <h3><?php echo JText::_('PLG_TITLE'); ?></h3>
</div>


<div class="modal-body">
    <?php echo JHtml::_('bootstrap.startTabSet', 'mySliders', array('active' => 'slider_1')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'mySliders', 'slider_1', JText::_('PLG_TAB_USERS')); ?>

    <div class="row-fluid">
        <div class="span5">
            <label id="batch-choose-action-lbl" class="control-label" for="batch-choose-action">
                <?php echo JText::_('PLG_LABEL_SUFFIX'); ?>
            </label>
            <div style='width:15em'>
            <?php echo createSelectFieldParamsUsers();?>
                </div>
        </div>

        <div class="span4">
            <label id="batch-choose-action-lbl" class="control-label" for="batch-choose-action">
                <?php echo JText::_('PLG_LABEL_PROFILE'); ?>
            </label>
            <div style='width:15em'>
                <?php echo createSelectFieldProfiles() ?>
            </div>
        </div>
    </div>
    <label id="batch-choose-action-lbl" class="control-label" for="batch-choose-action">
        <?php echo JText::_('PLG_LABEL_USERS'); ?>
    </label>
    <div  style="overflow: auto; height:240px;">
    <?php echo createSelectFieldUsers() ?>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php echo JHtml::_('bootstrap.addTab', 'mySliders', 'slider_2', JText::_('PLG_TAB_GROUPS')); ?>
    <div  style="overflow: auto; height:300px;">
        <label id="batch-choose-action-lbl" class="control-label" for="batch-choose-action">
        <?php echo JText::_('PLG_LABEL_SUFFIX'); ?>
            </label>
            <?php echo createSelectFieldParamsGroups() ?>
            <br>
        </br>
        <label id="batch-choose-action-lbl" class="control-label" for="batch-choose-action">
            <?php echo JText::_('PLG_LABEL_GROUPS'); ?>
        </label>

        <?php echo createSelectFieldGroups() ?>
        </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php echo JHtml::_('bootstrap.endTabSet'); ?>
</div>
<div class="modal-footer">
    <button class="btn" type="button" onclick="window.parent.jModalClose()" data-dismiss="modal">
        <?php echo JText::_('JCANCEL'); ?>
    </button>
    <button class="btn btn-primary" type="button" onclick="insert()">
        <?php
        echo JText::_('PLG_BUTTON_INSERT');
        ?>
    </button>
</div>