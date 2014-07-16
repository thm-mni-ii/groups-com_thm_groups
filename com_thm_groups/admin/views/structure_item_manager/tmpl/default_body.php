<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td>
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
            <?php echo $item->id; ?>
        </td>
        <td>
            <?php
            $link = JRoute::_('index.php?option=com_thm_groups&view=structure_item_edit&cid[]=' . $item->id);
            echo "<a href='$link'>" . $item->name . "</a>";
            ?>
        </td>
        <td>
            <?php echo $item->dynamic_type_name; ?>
        </td>
        <td>
            <?php echo $item->options; ?>
        </td>
        <td>
            <?php echo $item->description; ?>
        </td>
    </tr>
<?php endforeach; ?>