<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
    <th width="20">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th width="10">
        <?php echo JHtml::_('grid.sort', 'ID', 'structure.id', $this->sortDirection, $this->sortColumn); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'Name', 'structure.name', $this->sortDirection, $this->sortColumn); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'Dynamic_Type_Name', 'dynamic.name', $this->sortDirection, $this->sortColumn); ?>
    </th>
    <th>
        <?php echo JText::_('Options'); ?>
    </th>
    <th>
        <?php echo JText::_('Description'); ?>
    </th>
</tr>