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
require_once JPATH_ROOT . '/media/com_thm_groups/templates/list.php';
?>
    <script
            type="text/javascript">var noItemsSelected = '<?php echo JText::_('COM_THM_GROUPS_NO_GROUP_SELECTED'); ?>'
    </script>
<?php
THM_GroupsTemplateList::render($this);
