<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

foreach ($this->attributes as $key => $attribute) {
    if (!empty($attribute['params'])) {
        $params = json_decode($attribute['params'], true);
    }

    $published = isset($attribute['published']) ? $attribute['published'] : 1;
    $useParams = !empty($params);

    $showIcon = $showLabel = 1;

    if ($useParams) {
        $showIcon  = isset($params['showIcon']) ? $params['showIcon'] : 1;
        $showLabel = isset($params['showLabel']) ? $params['showLabel'] : 1;
    }

    ?>

    <tr class="ui-state-default">
        <td class="order nowrap center hidden-phone">
                <span class="sortable-handler" style="cursor: move;">
                    <span class="icon-menu"></span>
                </span>
        </td>
        <td>
            <?php
            echo $attribute['field'];
            echo "<input type='hidden' name='jform[attributes][{$attribute['id']}][attribute]' value='{$attribute['field']}' />";
            echo "<input type='hidden' name='jform[attributes][{$attribute['id']}][attributeID]' value='{$attribute['id']}' />";
            if (!empty($attribute->ID)) {
                echo "<input type='hidden' name='jform[attributes][{$attribute['id']}][ID]' value='{$attribute['id']}' />";
            }
            ?>
        </td>
        <td>
            <?php
            echo $this->renderRadioBtn('published', $attribute, $published);
            ?>
        </td>
        <td>
            <?php
            echo $this->renderRadioBtn('show_icon', $attribute, $showIcon);
            ?>
        </td>
        <td>
            <?php
            echo $this->renderRadioBtn('show_label', $attribute, $showLabel);
            ?>
        </td>
    </tr>
    <?php
}

