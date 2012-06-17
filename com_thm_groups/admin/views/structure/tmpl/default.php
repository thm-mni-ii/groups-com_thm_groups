<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewStructure
 *@description THMGroupsViewStructure file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@authors      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die;


JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'a.order';
?>

<form action="<?php echo JRoute::_('index.php?option=com_thm_groups&view=structure'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="44%">
					<?php echo JHtml::_('grid.sort', 'COM_THM_GROUPS_STRUCTURE_HEADING_FIELD', 'a.field', $listDirn, $listOrder); ?>
				</th>
				<th width="44%">
					<?php echo JHtml::_('grid.sort', 'COM_THM_GROUPS_STRUCTURE_HEADING_TYPE', 'a.type', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.order', $listDirn, $listOrder); ?>
					<?php 
					if ($saveOrder)
					{
						echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'structure.saveorder');
					}
					?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $item)
		{
			$ordering	= $listOrder == 'a.order';
			$link = JRoute::_('index.php?option=com_thm_groups&task=structure.edit&cid[]=' . $item->id);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td align="center">
					<?php
						if ($item->id < 7)
						{
							echo $item->field;
						}
						else
						{
							echo "<a href='$link'>" . $item->field . "</a>";
						}
					?>
				</td>
				<td align="center">
					<?php echo $item->type; ?>
				</td>
				<td class="order">
						<?php 
						if ($saveOrder)
						{
						?>
							<?php 
							if ($listDirn == 'asc')
							{
							?>
								<span>
									<?php echo $this->pagination->orderUpIcon($i, 1, 'structure.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?>
								</span>
								<span>
									<?php 
										echo $this->pagination->orderDownIcon(
												$i, $this->pagination->total, 1, 'structure.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering
												);
									?>
								</span>
							<?php 
							}
							elseif ($listDirn == 'desc')
							{
							?>
								<span>
									<?php echo $this->pagination->orderUpIcon($i, 1, 'structure.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?>
								</span>
								<span>
									<?php 
										echo $this->pagination->orderDownIcon(
												$i, $this->pagination->total, 1, 'structure.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering
												);
									?>
								</span>
							<?php 
							}
							?>
						<?php 
						}
						?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" 
						value="<?php echo $item->order;?>" 
						<?php echo $disabled; ?> class="text-area-order" />
				</td>
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
		<?php 
		}
		?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
