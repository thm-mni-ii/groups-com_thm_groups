<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

$saveOrderingUrl = JUri::base() . '?option=com_thm_groups&task=content.saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'content_manager-list', 'adminForm', null, $saveOrderingUrl);
$rowClass = $this->canEdit ? 'order dndlist-sortable' : '';

?>
<div id="j-main-container" class="manager-page">
    <form action="<?php echo JURI::base(); ?>" id="adminForm" method="post" name="adminForm">
        <div class="page-header">
            <h2 class="groups-toolbar">
				<?php echo $this->pageTitle; ?>
            </h2>
        </div>
        <div class="toolbar">
			<?php echo $this->getNewButton(); ?>
        </div>
        <div class="thm_table_area">
            <table class="table table-striped" id="content_manager-list">
                <thead>
                <tr>
					<?php if ($this->canEdit) : ?>
                        <th class="btn-column"></th>
					<?php endif; ?>
                    <th>
						<?php echo JText::_('COM_THM_GROUPS_TITLE'); ?>
                    </th>
					<?php if ($this->canEdit) : ?>
                        <th class="hasTip publish-column" title="<?php echo JText::_('COM_THM_GROUPS_STATUS_TIP') ?>">
							<?php echo JText::_('COM_THM_GROUPS_STATUS'); ?>
                        </th>
                        <th class="hasTip btn-column" title="<?php echo JText::_('COM_THM_GROUPS_MENU_TIP') ?>">
							<?php echo JText::_('COM_THM_GROUPS_MENU'); ?>
                        </th>
					<?php endif; ?>
                </tr>
                </thead>
                <tbody class="ui-sortable">
				<?php foreach ($this->items as $key => $item) : ?>
                    <tr class="nowrap center <?php echo $rowClass; ?>"
                        id="<?php echo $item->id; ?>">
						<?php echo $this->getRow($key, $item); ?>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="option" value="com_thm_groups"/>
        <input type="hidden" name="view" value="content_manager"/>
        <input type="hidden" name="Itemid" value="<?php echo $this->menuID; ?>"/>
        <input type="hidden" name="profileID" value="<?php echo $this->profileID; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
    </form>
</div>