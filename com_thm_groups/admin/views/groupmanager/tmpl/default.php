<?php
/**
 * @version     v3.2.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewgroupmanager
 * @description THMGroupsViewgroupmanager file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$user = JFactory::getUser();
?>
<form action="index.php" method="post" name="adminForm">
<div id="editcell">
<table class="adminlist">
    <thead>
        <tr>
<!--    	<th width="1" ><?php echo JHTML::_('grid.sort', 'Gruppen ID', 'id', $listDirn, $listOrder);?></th> -->
            <th width="1%" ><input type="checkbox" name="toggle" value=""
                onclick="checkAll(<?php echo count($this->items); ?>);" /></th>

            <th width="30%" align="center">
                <?php echo JHTML::_('grid.sort', 'Name', 'name', $listDirn, $listOrder); ?>
            </th>
            <th width="40%" align="center">
                <?php echo JHTML::_('grid.sort', 'INFO', 'info', $listDirn, $listOrder); ?>
            </th>
            <th width="10%" align="center">
                <?php echo JHTML::_('grid.sort', 'PICTURE', 'picture', $listDirn, $listOrder); ?>
            </th>
            <th width="10%" align="center">
                <?php echo JHTML::_('grid.sort', 'MODE', 'mode', $listDirn, $listOrder); ?>
            </th>
            <th width="5%" align="center">
                <?php echo JHTML::_('grid.sort', 'ID', 'picture', $listDirn, $listOrder); ?>
            </th>
            <th width="5%" align="center">
                <?php echo JHTML::_('grid.sort', 'COM_THM_GROUPS_USERS_COUNT', 'picture', $listDirn, $listOrder); ?>
            </th>
            <th width="5%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_THM_GROUPS_IN_JOOMLA', 'injoomla', $listDirn, $listOrder); ?>
            </th>

        </tr>
    </thead>

    <?php
    $k = 0;
    for ($i = 0, $n = count($this->items); $i < $n; $i++)
    {
        $row = $this->items[$i];
        $link = 'index.php?option=com_thm_groups&view=editgroup&task=groupmanager.edit&cid=' . $row->id;
        $checked = JHTML::_('grid.id', $i, $row->id);

        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td> <?php echo $checked; ?> </td>
            <td>
                <?php
                    $tempgroup = $row;
                    $gap = 0;
                    while ($tempgroup->parent_id != 0)
                    {
                        $gap++;
                        foreach ($this->jgroups as $actualgroup)
                        {
                            if ($tempgroup->parent_id == $actualgroup->id)
                            {
                                $tempgroup = $actualgroup;
                            }
                        }
                    }
                    while ($gap > 0)
                    {
                        $gap--;
                        echo "<span style='color: #D7D7D7; font-weight: bold; margin-right: 5px;'>|&mdash;</span>";
                    }
                    if ($user->authorise('core.edit', 'com_users') && $user->authorise('core.manage', 'com_users'))
                    {
                        echo "<a href='$link'>";
                        if ($row->name == null)
                        {
                            echo $row->title;
                        }
                        else
                        {
                            echo $row->name;
                        }
                        echo "</a>";
                    }
                    else
                    {
                        if ($row->name == null)
                        {
                            echo $row->title;
                        }
                        else
                        {
                            echo $row->name;
                        }
                    }
                    ?>
            </td>
            <td> <?php echo $row->info; ?> </td>
            <td> <?php echo $row->picture?> </td>
            <td> <?php echo $row->mode?> </td>
            <td align="center"> <?php echo $row->id?> </td>
            <td align="center">
            <?php
                echo $this->model->getGroupUserCount($row->id);
            ?>
            </td>
            <td valign="top" align="center">
            <?php
            if ($row->injoomla == '0')
            {
                echo JHtml::_('jgrid.published', 0, 'groupmanager.', 1);
            }
            if ($row->injoomla == '1')
            {
                echo JHtml::_('jgrid.published', 1, 'groupmanager.', 1);
            }
            ?></td>
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
<input type="hidden" name="view" value="groupmanager" />
<input type="hidden" name="controller" value="groupmanager"   />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
