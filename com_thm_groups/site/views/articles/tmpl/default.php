<?php

/**
 * @version     v3.2.5
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . DS . 'helper' . DS . 'html');
JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'a.ordering';

// Get menu id
$menuItemID = JRequest :: getVar('Itemid', 0);

// Calc return URI encoding
$currURI = JFactory::getURI();
$retCryptURI = base64_encode($currURI->toString());
$itemParam = '&Itemid=' . $menuItemID;
$staticParams = '&' . $this->profileIdentData['ParamName'] . '=' . $this->profileIdentData['Id'] . '&return=' . $retCryptURI;

// Define basic extension of ACL rights
define('EXTENSION_RIGHTS', 'com_content');

// Check for authorization to create article in current category
$currCategoryID = $this->state->get('filter.category_id');
$canCreate = $this->hasUserRightToCreateArticle($currCategoryID);

?>

<form action="<?php echo JRoute::_('index.php?option=com_thm_groups&view=articles' . $itemParam); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="qp_filter_select">
			<label class="qp_filter_label" for="filter_category_id"><?php echo JText::_('COM_THM_QUICKPAGES_CATEGORY_LABEL'); ?></label>
			<select name="filter_category_id" class="qp_inputbox" onchange="this.form.submit()">
				<?php /* echo JHtml::_('select.options', 
					JHtml::_('category.options', 'com_content'), 
					'value', 
					'text',
					$this->state->get('filter.category_id')
					); 
				*/?>
				<?php echo JHtml::_('select.options', $this->categories, 'id', 'title', $this->state->get('filter.category_id'));?>
			</select>

			<select name="filter_published" class="qp_inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_THM_QUICKPAGES_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_(
						'select.options', 
						JHtml::_('jgrid.publishedOptions'), 
						'value', 
						'text', 
						$this->state->get('filter.published'), true
						);?>
			</select>
		</div>
		<div class="qp_filter_search">
			<label class="qp_filter_label" for="filter_search"><?php echo JText::_('COM_THM_QUICKPAGES_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php 
				echo $this->escape($this->state->get('filter.search')); 
			?>" title="<?php 
			echo JText::_('COM_THM_QUICKPAGES_FILTER_DESC'); 
			?>" />

			<button type="submit" class="qp_button"><?php echo JText::_('COM_THM_QUICKPAGES_FILTER_SUBMIT'); ?></button>
		</div>
	</fieldset>
	<div class="qp_clear"> </div>

	<table class="qp_mainlist">
		<thead>
			<tr>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_THM_QUICKPAGES_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="*">
					<?php echo JHtml::_('grid.sort', 'COM_THM_QUICKPAGES_PUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="18%">
					<?php echo trim(JHtml::_('grid.sort',  'COM_THM_QUICKPAGES_ORDERING', 'a.ordering', $listDirn, $listOrder));
					if ($saveOrder)
					{
						echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'articles.saveorder');
					}
					?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_THM_QUICKPAGES_DATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_THM_QUICKPAGES_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th>
				<th width="15%">
					<?php /* echo JHtml::_('grid.sort', 'COM_THM_QUICKPAGES_EDIT', 'a.state', $listDirn, $listOrder); */ ?>
					<?php /* echo JHtml::_('grid.sort', 'COM_THM_QUICKPAGES_TRASH', 'a.state', $listDirn, $listOrder); */ ?>
					<?php
						if ($canCreate AND $currCategoryID != 0)
						{
							/*$editURL = JRoute::_('index.php?option=com_content&task=article.add&catid='.$currCategoryID.$itemParam.$staticParams);*/
							$editURL = JRoute::_('index.php?option=com_content&view=form&layout=edit&catid=' 
									. $currCategoryID 
									. $itemParam 
									. $staticParams
									);

							$imgSpanTag = '<span class="qp_icon_big qp_create_icon"><span class="qp_invisible_text">New</span></span>';

							echo JHTML::_('link', $editURL, $imgSpanTag, 'title="' 
									. JText::_('COM_THM_QUICKPAGES_HTML_CREATE') 
									. '" class="qp_icon_link"'
									);
						}
						else
						{
							echo '<span class="qp_icon_big qp_create_icon_disabled"><span class="qp_invisible_text">New</span></span>';
						}

					?>
				</th>
			</tr>
		</thead>
		<!--
		<tfoot>
			<tr>
				<td colspan="15">
					<?php /* echo $this->pagination->getListFooter(); */ ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		-->
		<?php foreach ($this->items as $i => $item)
		{
			$item->max_ordering = 0;
			$ordering	= ($listOrder == 'a.ordering');

			$canEdit	= $this->hasUserRightTo('Edit', $item);
			$canCheckin	= $this->hasUserRightTo('Checkin', $item);
			$canChange	= $this->hasUserRightTo('EditState', $item);
			$canDelete	= $this->hasUserRightTo('Delete', $item);
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<span class="qp_invisible_text"><?php echo JHtml::_('grid.id', $i, $item->id); ?></span>
					<?php
						if ($item->state > 0)
						{
							echo JHTML::_('link', THMLibThmQuickpages::getQuickpageRoute($item, $staticParams), $this->escape($item->title));
						}
						else
						{
							echo $this->escape($item->title);
						}
					?>
				</td>
				<td class="center">
					<?php 
						echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down);
					?>
				</td>
				<td class="order">
					<?php if ($canChange)
						  {
						  	if ($saveOrder)
						  	{
						  		if ($listDirn == 'asc')
						  		{ 
						  	?>
								<span>
								<?php 
									echo $this->pagination->orderUpIcon(
											$i, 
											($item->catid == @$this->items[$i - 1]->catid), 
											'articles.orderup', 
											'JLIB_HTML_MOVE_UP', 
											$ordering
											); 
								?>
								</span>
								<span>
								<?php 
									echo $this->pagination->orderDownIcon(
											$i, 
											$this->pagination->total, 
											($item->catid == @$this->items[$i + 1]->catid), 
											'articles.orderdown', 
											'JLIB_HTML_MOVE_DOWN', 
											$ordering
											);
								?>
								</span>
							<?php   
						  		}
								elseif ($listDirn == 'desc') 
								{
							?>
								<span>
								<?php 
									echo $this->pagination->orderUpIcon(
											$i, 
											($item->catid == @$this->items[$i - 1]->catid), 
											'articles.orderdown', 
											'JLIB_HTML_MOVE_UP', 
											$ordering
											);
								?>
								</span>
								<span>
								<?php 
									echo $this->pagination->orderDownIcon(
											$i, 
											$this->pagination->total, 
											($item->catid == @$this->items[$i + 1]->catid), 
											'articles.orderup', 
											'JLIB_HTML_MOVE_DOWN', 
											$ordering
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
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php 
						echo $disabled ?> class="text-area-order" />
					<?php 
}
						  else 
						  { 
							echo $item->ordering;
						  } 
				    ?>
				</td>
				<td class="center nowrap">
					<?php echo JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->hits; ?>
				</td>
				<td class="center">
					<?php
						// Output checkin icon
						if ($item->checked_out)
						{
							echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin);
						}
						else
						{
						}

						// Output edit icon
						if ($canEdit)
						{
							$editURL = JRoute::_('index.php?option=com_content&task=article.edit&a_id=' . $item->id . $itemParam . $staticParams);
							/* $editURL = JRoute::_('index.php?option=com_content&view=form&layout=edit&a_id='.$item->id.$itemParam.$staticParams); */
							$imgSpanTag = '<span class="state edit"><span class="text">Edit</span></span>';

							echo JHTML::_('link', $editURL, $imgSpanTag, 'title="' 
									. JText::_('COM_THM_QUICKPAGES_HTML_EDIT_ITEM') 
									. '" class="jgrid"'
									);
							echo "\n";
						}
						else
						{
							echo '<span class="jgrid"><span class="state edit_disabled"><span class="text">Edit</span></span></span>';
						}

						// Output trash icon
						if ($item->state >= 0)
						{
							// Define state changes needed by JHtmlJGrid.state(), see also JHtmlJGrid.published()
							$states	= array(
								0	=> array(),		// Dummy: Wird nicht gebraucht, erzeugt aber sonst Notice
								3	=> array(
										'trash',
										'JPUBLISHED',
										'COM_THM_QUICKPAGES_HTML_TRASH_ITEM',
										'JPUBLISHED',
										false,
										'trash',
										'trash_disabled'
										),
								-3	=> array(
										'publish',
										'JTRASHED',
										'COM_THM_QUICKPAGES_HTML_UNTRASH_ITEM',
										'JTRASHED',
										false,
										'untrash',
										'untrash'
										),
							);
							$button = JHtml::_('jgrid.state', $states, ($item->state < 0 ? -3 : 3), $i, 'articles.', $canDelete);
							$button = str_replace("onclick=\"", "onclick=\"if (confirm('" . JText::_('COM_THM_GROUPS_REALLY_DELETE') . "')) ", $button);
							echo $button;
						}
					?>
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
