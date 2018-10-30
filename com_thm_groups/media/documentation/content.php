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
        <h3><?php echo $lang->_('COM_THM_GROUPS_CONTENT'); ?></h3>
        <p><?php echo $lang->_('COM_THM_GROUPS_CONTENT_DESC_LONG'); ?></p>
        <div class="list">
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_ORDER'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_CONTENT_ORDERING_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_ID'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_CONTENT_ID_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_TITLE'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_CONTENT_TITLE_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_PROFILE'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_CONTENT_PROFILE_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_PROFILE_MENU'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_PROFILE_MENU_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_STATUS'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_CONTENT_STATUS_DESC_LONG'); ?></div>
            <div class="clearFix"></div>
        </div>
    </body>
</html>
