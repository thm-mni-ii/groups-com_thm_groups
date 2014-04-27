<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewRolemanager
 * @description THMGroupsViewRolemanager file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @authors     Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="editcell">
<table class="table table-striped">
    <thead>
        <tr>
<!--    	<th width="1" ><?php echo JHTML::_('grid.sort', 'Gruppen ID', 'id', $listDirn, $listOrder);?></th> -->
            <th width="1%" ><input type="checkbox" name="toggle" value=""
                onclick="checkAll(<?php echo count($this->items); ?>);" /></th>

            <th width="95%" align="center">
                <?php echo JHTML::_('grid.sort', 'NAME', 'rname', $listDirn, $listOrder); ?>
            </th>
        </tr>
    </thead>

    <?php
    $k = 0;
    for ($i = 0, $n = count($this->items); $i < $n; $i++)
    {
        $row = $this->items[$i];
        $link = 'index.php?option=com_thm_groups&view=editrole&task=rolemanager.edit&cid=' . $row->id;
        $checked = JHTML::_('grid.id',   $i, $row->id);
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td> <?php echo $checked; ?> </td>
            <td>
                <a href="<?php echo $link; ?>">
                <?php echo $row->rname; ?>
                </a>
            </td>
        </tr>
        <?php
            $k = 1 - $k;
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
