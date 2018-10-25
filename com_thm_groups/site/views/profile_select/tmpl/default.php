<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined("_JEXEC") or die;

$filters = $this->filterForm->getGroup('filter');
?>
<script type="text/javascript">
    const rootURI = '<?php echo JUri::root(); ?>',
        editor = '<?php echo JFactory::getApplication()->input->getString('editor')?>';
</script>
<div id="profile-select-container" class="profile-select-container">
    <header class="header">
        <div class="container-title">
            <h2 class="page-title">
                <span class="icon-users" aria-hidden="true"></span>
                <?php echo JText::_('COM_THM_GROUPS_PROFILE_SELECT_TITLE'); ?>
            </h2>
        </div>
    </header>
    <div class="subhead">
        <div class="row-fluid">
            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar" id="toolbar">
                <div class="btn-wrapper">
                    <button id="insertLinkButton" class="btn btn-small" onclick="insertProfileLinks();return false;">
                        <span class="icon-link"></span>
                        <span class="buttonText"><?php echo JText::_('COM_THM_GROUPS_INSERT_LINKS'); ?></span>
                    </button>
                </div>
                <div class="btn-wrapper">
                    <button id="insertParameterButton" class="btn btn-small"
                            onclick="insertProfileParameters();return false;">
                        <span class="icon-cogs"></span>
                        <span
                                class="buttonText"><?php echo JText::_('COM_THM_GROUPS_INSERT_MODULE_PARAMETERS'); ?></span>
                    </button>
                </div>
                <div class="btn-wrapper">
                    <button id="insertParameterButton" class="btn btn-small"
                            onclick="resetProfiles();return false;">
                        <span class="icon-undo"></span>
                        <span class="buttonText"><?php echo JText::_('COM_THM_GROUPS_RESET'); ?></span>
                    </button>
                </div>
                <div class="btn-wrapper">
                    <button id="cancelButton" class="btn btn-small" onclick="window.parent.SqueezeBox.close();">
                        <span class="icon-cancel"></span>
                        <span class="buttonText"><?php echo JText::_('JCANCEL'); ?></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row-fluid">
        <div class="btn-toolbar" role="listbox" aria-label="Listbox" id="listbox">
            <div class="filter-row">
                <div class="btn-wrapper row-label">
                    <?php echo $filters['filter_groups']->label; ?>
                </div>
                <div class="btn-wrapper input-append">
                    <?php echo $filters['filter_groups']->input; ?>
                </div>
            </div>
            <div class="filter-row">
                <div class="btn-wrapper row-label">
                    <?php echo $filters['filter_templates']->label; ?>
                </div>
                <div class="btn-wrapper input-append">
                    <?php echo $filters['filter_templates']->input; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="profile-selection-container">
        <table class="adminlist selected-profiles-sortable">
            <thead>
            <tr>
                <th colspan="2"><?php echo JText::_('COM_THM_GROUPS_SELECTED'); ?></th>
            </tr>
            </thead>
            <tbody id="selected-profiles">
            </tbody>
        </table>
        <table class="adminlist profiles-sortable">
            <thead>
            <tr>
                <th colspan="2"><?php echo JText::_('COM_THM_GROUPS_SELECTABLE'); ?></th>
            </tr>
            </thead>
            <tbody id="selectable-profiles">
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(function () {
            jQuery(".selected-profiles-sortable tbody").sortable().disableSelection();
        });
        jQuery().popover({container: 'body'})
    });
</script>
