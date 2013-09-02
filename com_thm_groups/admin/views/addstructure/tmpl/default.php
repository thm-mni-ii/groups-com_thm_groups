<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAddStructure
 * @description THMGroupsViewAddStructure file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');
$scriptDir = "libraries/thm_groups/assets/js/";
JHTML::script('jquery-1.9.1.min.js', $scriptDir);
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root(true) . "/libraries/thm_groups/assets/elements/explorer.css");
?>

<script type="text/javascript">
jstructadd = jQuery.noConflict();

jstructadd.fn.getFieldExtras = function(){

    var field = jstructadd('#relation option:selected').text();

    //$('#jquery-select option:selected').text();
 if(field.length == null)
     return null;
    jstructadd.ajax({
        type: "POST",
        url: "index.php?option=com_thm_groups&controller=editstructure&task=addstructure.getFieldExtrasLabel&field="
            +field,

            success: function(response) {
                jstructadd('#ajax-container').html(response);
        }
    });

    jstructadd.ajax({
        type: "POST",
        url: "index.php?option=com_thm_groups&controller=editstructure&task=addstructure.getFieldExtras&sid="
            +0+"&field="+field,
        datatype:"HTML",
        success: function(response)
        {
            jstructadd('#ajax-container2').html(response);
        }
    });
}
jstructadd(document).ready(function(){jstructadd.fn.getFieldExtras();});
</script >

<form action="index.php" method="post" name="adminForm">
<div>
    <fieldset class="adminform">
        <legend>
            <?php echo   JText::_('COM_THM_GROUPS_ADDSTRUCTURE'); ?>
        </legend>
        <table class="admintable">
            <tr>
                <td width="310" class="key">
                    <label for="title">
                          <?php echo JText::_('COM_THM_GROUPS_STRUCTURE_HEADING_FIELD'); ?>:
                    </label>
                </td>
                <td>
                    <input class="inputbox" type="text" name="name" id="name" size="60"/>
                </td>
            </tr>
            <tr>
                <td width="310" class="key">
                    <label for="title">
                          <?php echo JText::_('COM_THM_GROUPS_STRUCTURE_HEADING_TYPE'); ?>:
                    </label>
                </td>
                <td>
                    <select name="relation" id="relation" size="1" onchange='jstructadd.fn.getFieldExtras();'>
                    <?php
                        foreach ($this->items as $item)
                        {
                            $optionbox = "<option value=";
                            $optionbox .= $item->Type;
                            $optionbox .= ">" . $item->Relation . '</option>';
                            echo($optionbox);
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo '--- ' . JText::_('COM_THM_GROUPS_STRUCTURE_EXTRA_PARAMS') . ' ---'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="ajax-container">
                     </div>
                </td>
                <td>
                    <div id="ajax-container2">
                     </div>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<input type="hidden" name="option" value="com_thm_groups" />
<input type="hidden" name="task"   value="" />
<input type="hidden" name="id" value="1" />
<input type="hidden" name="controller" value="addstructure" />
</form>