<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewArticles Template
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
?>

<div id="j-main-container" class="manager-page">
    <form action="index.php" id="adminForm"  method="post"
          name="adminForm" xmlns="http://www.w3.org/1999/html">
        <div class="page-header">
            <h2 class="groups-toolbar">
                <?php echo JText::_('COM_THM_GROUPS_QUICKPAGE_MANAGER_TITLE'); ?>
            </h2>
        </div>
        <div class="toolbar">
            <?php echo $this->getNewButton(); ?>
            <?php echo $this->getProfileButton(); ?>
        </div>
        <div class="thm_table_area">
            <table class="table table-striped" id="quickpage_manager-list">
                <thead>
                    <tr>
                        <th>
                            <?php echo JText::_('COM_THM_GROUPS_TITLE'); ?>
                        </th>
                        <th class="hasTip" title="<?php echo JText::_('COM_THM_GROUPS_STATUS_TIP')?>">
                            <?php echo JText::_('COM_THM_GROUPS_STATUS'); ?>
                        </th>
                        <th class="hasTip btn-column" title="<?php echo JText::_('COM_THM_GROUPS_MENU_TIP')?>">
                            <?php echo JText::_('COM_THM_GROUPS_MENU'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="ui-sortable">
<?php if (!empty($this->items)): ?>
    <?php foreach ($this->items as $key => $item): ?>
                <tr sortable-group-id="<?php echo $item->id; ?>" class="dndlist-sortable">
                    <?php echo $this->getRow($key, $item); ?>
                </tr>
    <?php endforeach; ?>
<?php endif; ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="option" value="com_thm_groups" />
        <input type="hidden" name="view" value="quickpage_manager" />
        <input type="hidden" name="Itemid" value="<?php echo $this->menuID; ?>" />
        <?php echo JHtml::_('form.token');?>
    </form>
</div>