<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once HELPERS . 'notices.php';
$labelingNotice    = THM_GroupsHelperNotices::getLabelingNotice();
$orderingNotice    = THM_GroupsHelperNotices::getOrderingNotice();
$publicationNotice = THM_GroupsHelperNotices::getPublicationNotice();
$suppressionNotice = THM_GroupsHelperNotices::getSuppressionNotice();

$specialAttributes  = [FORENAME, SURNAME, TITLE, POSTTITLE];
$specialTypes       = [IMAGE];
$noSuppression      = [FORENAME, SURNAME];
$limitedSuppression = [TITLE, POSTTITLE];

foreach ($this->attributes as $key => $attribute) {
    $attributeID = $attribute['id'];
    $special = (in_array($attributeID, $specialAttributes) or in_array($attribute['typeID'], $specialTypes));
    ?>

    <tr class="ui-state-default">
        <td class="order nowrap center hidden-phone">
                <span class="sortable-handler" style="cursor: move;">
                    <?php
                    if ($special) {
                        echo $orderingNotice;
                    } else {
                        echo '<span class="icon-menu"></span>';
                    }
                    ?>
                </span>
        </td>
        <td>
            <?php
            echo $attribute['label'];
            echo "<input type='hidden' name='jform[attributes][$attributeID][attribute]' value='{$attribute['label']}' />";
            ?>
        </td>
        <td>
            <?php
            if (in_array($attributeID, $noSuppression)) {
                echo $publicationNotice;
            } elseif (in_array($attributeID, $limitedSuppression)) {
                echo $suppressionNotice;
            } else {
                echo $this->renderRadioBtn('published', $attribute, $attribute['published']);
            }
            ?>
        </td>
        <td>
            <?php
            if ($special) {
                echo $labelingNotice;
            } else {
                echo $this->renderRadioBtn('showIcon', $attribute, $attribute['showIcon']);
            }
            ?>
        </td>
        <td>
            <?php
            if ($special) {
                echo $labelingNotice;
            } else {
                echo $this->renderRadioBtn('showLabel', $attribute, $attribute['showLabel']);
            }
            ?>
        </td>
    </tr>
    <?php
}

