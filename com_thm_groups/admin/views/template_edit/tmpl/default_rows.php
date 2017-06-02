<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @description rows view default template
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;


foreach ($this->attributes as $key => $attribute)
{
	$params = null;
	if (!empty($attribute->params))
	{
		$params = json_decode($attribute->params);
	}

	$defaultPublished = 1;
	if (!empty($params))
	{
		$defaultPublished = empty($attribute->published) ? $attribute->published : 1;
	}

	$defaultShowIcon = 1;
	if (!empty($params))
	{
		$defaultShowIcon = empty($params->showIcon) ? $params->showIcon : 1;
	}

	$defaultShowTitle = 1;
	if (!empty($params))
	{
		$defaultShowTitle = empty($params->showLabel) ? $params->showLabel : 1;
	}

	$defaultWrap = 1;
	if (!empty($params))
	{
		$defaultWrap = empty($params->wrap) ? $params->wrap : 1;
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
			echo $attribute->field;
			echo "<input type='hidden' name='jform[attributes][$attribute->id][attribute]' value='$attribute->field' />";
			echo "<input type='hidden' name='jform[attributes][$attribute->id][attribute_id]' value='$attribute->id' />";
			if (!empty($attribute->ID))
			{
				echo "<input type='hidden' name='jform[attributes][$attribute->id][ID]' value='$attribute->ID' />";
			}
			?>
		</td>
		<td>
			<?php
			echo $this->renderRadioBtn('published', $attribute, $defaultPublished);
			?>
		</td>
		<td>
			<?php
			echo $this->renderRadioBtn('show_icon', $attribute, $defaultShowIcon);
			?>
		</td>
		<td>
			<?php
			echo $this->renderRadioBtn('show_label', $attribute, $defaultShowTitle);
			?>
		</td>
		<td>
			<?php
			echo $this->renderRadioBtn('wrap', $attribute, $defaultWrap);
			?>
		</td>
	</tr>
	<?php
}

