<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
require_once 'framework.php';
$lang = THM_GroupsHelperLanguage::getLanguage(); ?>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo JUri::root() . 'css/documentation.css'; ?>">
    </head>
    <body class="groups-documentation">
        <h3><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPE_EDIT'); ?></h3>
        <p><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPE_EDIT_DESC_LONG'); ?></p>
        <div class="list">
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_NAME'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPE_NAME_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_FIELD'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPE_FIELD_DESC_LONG'); ?></div>
            <div class="clearFix"></div>
        </div>
        <h4><?php echo $lang->_('COM_THM_GROUPS_TEXT_BASED_ATTRIBUTE_TYPES'); ?></h4>
        <p><?php echo $lang->_('COM_THM_GROUPS_TEXT_BASED_ATTRIBUTE_TYPES_DESC_LONG'); ?></p>
        <div class="list">
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_HINT'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_HINT_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_MAX_LENGTH'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_MAX_LENGTH_DESC'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_REGEX'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_REGEX_DESC_LONG'); ?></div>
            <div class="clearFix"></div>
        </div>
        <h4><?php echo $lang->_('COM_THM_GROUPS_CALENDAR_TYPES'); ?></h4>
        <p><?php echo $lang->_('COM_THM_GROUPS_CALENDAR_TYPES_DESC_LONG'); ?></p>
        <div class="list">
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_CALENDAR_FORMAT'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_CALENDAR_FORMAT_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_SHOW_TIME'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_SHOW_TIME_DESC'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_TIME_FORMAT'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_TIME_FORMAT_DESC'); ?></div>
            <div class="clearFix"></div>
        </div>
        <h4><?php echo $lang->_('COM_THM_GROUPS_EDITOR_TYPES'); ?></h4>
        <p><?php echo $lang->_('COM_THM_GROUPS_EDITOR_TYPES_DESC_LONG'); ?></p>
        <div class="list">
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_EDITOR_BUTTONS'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_EDITOR_BUTTONS_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_HIDDEN_BUTTONS'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_HIDDEN_BUTTONS_DESC_LONG'); ?></div>
            <div class="clearFix"></div>
        </div>
        <h4><?php echo $lang->_('COM_THM_GROUPS_IMAGE_TYPES'); ?></h4>
        <p><?php echo $lang->_('COM_THM_GROUPS_IMAGE_TYPES_DESC_LONG'); ?></p>
        <div class="list">
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_ACCEPTED_FORMATS'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_ACCEPTED_FORMATS_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_CROPPING'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_CROPPING_DESC_LONG'); ?></div>
            <div class="clearFix"></div>
        </div>
    </body>
</html>
