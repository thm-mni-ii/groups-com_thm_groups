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
        <h3><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPES'); ?></h3>
        <p><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPES_DESC_LONG'); ?></p>
        <div class="list">
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_NAME'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPES_NAME_DESC_LONG'); ?></div>
            <div class="label"><?php echo $lang->_('COM_THM_GROUPS_FIELD'); ?></div>
            <div class="description"><?php echo $lang->_('COM_THM_GROUPS_ATTRIBUTE_TYPES_FIELD_DESC_LONG'); ?></div>
            <div class="clearFix"></div>
        </div>
    </body>
</html>
