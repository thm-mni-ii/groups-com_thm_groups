<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_thm_groups
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 *
 *
 *
 **/('_JEXEC') or die ('Restricted access');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="index.php" method="post" name="adminForm">
<div id="editcell">
<table class="adminlist">
	<thead>
		<tr>
<!--    	<th width="1" ><?php echo JHTML::_('grid.sort', 'Gruppen ID', 'id', $listDirn, $listOrder );?></th> -->
			<th width="1%" ><input type="checkbox" name="toggle" value=""
				onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>

			<th width="95%" align="center">
				<?php echo JHTML::_('grid.sort', 'NAME', 'rname', $listDirn, $listOrder ); ?>
			</th>
		</tr>
	</thead>

	<?php
	$k = 0;
	for ($i=0, $n=count($this->items); $i < $n; $i++){
		$row = $this->items[$i];
        $link='index.php?option=com_thm_groups&view=editrole&task=rolemanager.edit&cid='.$row->id;
		$checked  = JHTML::_('grid.id',   $i, $row->id );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td> <?php echo $checked; ?> </td>
			<td> <a href="<?php echo $link; ?>"> <?php echo $row->rname; ?></a> </td>
		    </td>
		</tr>
		<?php
			$k = 1 -   $k;
	}
	?>
	<tfoot>
    	<tr>
    		<td colspan="9">
    			<?php echo $this->pagination->getListFooter(); ?>
    		</td>
    	</tr>
  	</tfoot>
</table>
</div>

<input type="hidden" name="option" value="com_thm_groups" />
<input type="hidden" name="task"   value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="rolemanager" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>