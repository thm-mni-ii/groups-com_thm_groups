<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsVieweditStructure
 * @description THMGroupsVieweditStructure file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.mootools');
?>

<script type="text/javascript">
function getFieldExtras(){

	var field = $('relation');

	var b = new Request.HTML({

		url: "index.php?option=com_thm_groups&controller=editstructure&task=editstructure.getFieldExtrasLabel&field="
			+field[field.selectedIndex].value,

    	onComplete: function( response ) {
            $('ajax-container').empty().adopt(response);
		}
	}).send();

	var a = new Request.HTML({
		url: "index.php?option=com_thm_groups&controller=editstructure&task=editstructure.getFieldExtras&sid="
			+<?php echo $this->rowItem->id;?>+"&field="+field[field.selectedIndex].value,

    	onComplete: function( response ) {
            $('ajax-container2').empty().adopt(response);

                        }
	}).send();
}

window.addEvent( 'domready', function(){ getFieldExtras();});
</script >

<form action="index.php" method="post" name="adminForm">
<div>
	<fieldset class="adminform">
		<legend>
			<?php echo   JText::_('COM_THM_GROUPS_EDITSTRUCTURE'); ?>
		</legend>
		<table class="admintable">
			<tr>
				<td width="310" class="key">
					<label for="title">
	  					<?php echo JText::_('COM_THM_GROUPS_ID'); ?>:
					</label>
				</td>
				<td>
					<label for="title">
	  					<?php echo $this->rowItem->id;?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="310" class="key">
					<label for="title">
	  					<?php echo JText::_('COM_THM_GROUPS_STRUCTURE_HEADING_FIELD'); ?>:
					</label>
				</td>
				<td>
					<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->rowItem->field;?>" />
				</td>
			</tr>
			<tr>
				<td width="310" class="key">
					<label for="title">
	  					<?php echo JText::_('COM_THM_GROUPS_STRUCTURE_HEADING_TYPE'); ?>:
					</label>
				</td>
				<td>
					<select name="relation" id="relation" size="1" onchange='getFieldExtras();' >
			    	<?php
						foreach ($this->items as $item)
						{
							$optionbox = "<option value=";
							$optionbox .= $item->Type;
							if ($this->rowItem->type == $item->Type)
							{
								$optionbox .= " selected='selected'";
							}
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
					<span id="ajax-container">
	             	</span>
				</td>
				<td>
					<span id="ajax-container2">
	             	</span>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<input type="hidden" name="option" value="com_thm_groups" />
<input type="hidden" name="task"   value="" />
<input type="hidden" name="cid[]" value="<?php echo $this->rowItem->id;?>" />
<input type="hidden" name="controller" value="editstructure" />
</form>