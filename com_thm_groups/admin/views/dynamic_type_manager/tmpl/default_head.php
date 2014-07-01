<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
    <th width="20">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th width="10">
        <?php echo JHtml::_('grid.sort', 'ID', 'dynamic.id', $this->sortDirection, $this->sortColumn); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'Name', 'dynamic.name', $this->sortDirection, $this->sortColumn); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'Static_Type_Name', 'static.name', $this->sortDirection, $this->sortColumn); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'Regular expression', 'regex', $this->sortDirection, $this->sortColumn); ?>
    </th>
</tr>