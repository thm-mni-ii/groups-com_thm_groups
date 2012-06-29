<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        JFormFieldStructureSelect
 *@description JFormFieldStructureSelect file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * JFormFieldStructureSelect class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class JFormFieldStructureSelect extends JFormField
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 * 
	 * @return html
	 */
	public function getInput()
	{
		$scriptDir = str_replace(JPATH_SITE . DS, '', "administrator/components/com_thm_groups/elements/");

		// $sortButtons = true;

		// Add script-code to the document head
		JHTML::script('structureselect.js', $scriptDir, false);

		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->name . '"' . $class . '>';

		// Get selected items
		$selected = $this->value;

		// Parse selected items
		$selectedItems = array();
		if ($selected != "")
		{
			foreach ($selected as $item)
			{
				$tempItem = array();
				$tempItem['id'] = substr($item, 0, strlen($item) - 2);
				$tempItem['showName'] = substr($item, -2, 1);
				$tempItem['wrapAfter'] = substr($item, -1, 1);
				$selectedItems[] = $tempItem;
			}
		}
		else
		{
		}

		// Get the field options.
		$options = $this->getOptions($selectedItems);

		// Build the checkbox field output.
		$html[] = '<table>' .
				'<thead>' .
				'<tr><th>' .
				JText::_('Attribute') .
				'</th><th>' .
				JText::_('Show') .
				'</th><th>' .
				JText::_('Name') .
				'</th><th>' .
				JText::_('Wrap') .
				'</th></tr>' .
				'</thead>' .
				'<tbody>';

		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$value = null;
			$checked = '';
			$checkedShowName = '';
			$checkedWrapAfter = '';
			$disabled = '';

			foreach ($selectedItems as $item)
			{
				if ($item['id'] == $option->value)
				{
					$checked = ' checked="checked"';
					if ($item['showName'] == "1")
					{
						$checkedShowName = ' checked="checked"';
					}
					else
					{
						$checkedShowName = '';
					}

					if ($item['wrapAfter'] == "1")
					{
						$checkedWrapAfter = ' checked="checked"';
					}
					else
					{
						$checkedWrapAfter = '';
					}

					$value = $option->value . $item['showName'] . $item['wrapAfter'];
				}
			}
			if (!isset($value))
			{
				$value = $option->value . "00";
				$disabled = ' disabled="disabled"';
			}

			$html[] = '<tr>'
				. '<td>'
					. '<label for="' . $this->name . $i . '"' . $class . '>' . JText::_($option->text) . '</label>'
				. '</td>'
				. '<td>'
					. '<input type="checkbox" '
						. 'id="' . $this->name . $i . '" '
						. 'name="' . $this->name . '[' . $i . ']"' . ' '
						. 'value="' . $value . '" '
						. 'onchange="switchEnablingAdditionalAttr(' . "'" . $this->name . $i . "'" . ')"' . $checked
					. ' />'
				. '</td>'
				. '<td>'
					. '<input type="checkbox" '
						. 'id="' . $this->name . $i . 'ShowName" '
						. 'onchange="switchAttributeName(' . "'" . $this->name . $i . "'" . ')"' . $checkedShowName . $disabled
					. ' />'
				. '</td>'
				. '<td>'
					. '<input type="checkbox" '
						. 'id="' . $this->name . $i . 'WrapAfter" '
						. 'onchange="switchAttributeWrap(' . "'" . $this->name . $i . "'" . ')"' . $checkedWrapAfter . $disabled
					. ' />'
				. '</td>'
				. '</tr>';
		}
		$html[] = '</tbody>' .
				'</table>';

		// End the checkbox field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 * 
	 * @param   String  $selected  Selected field
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions($selected)
	{
		$query = "SELECT a.id, a.field FROM `#__thm_groups_structure` as a Order by a.order";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$list = $db->loadObjectList();

		// Initialize variables.
		$options = array();

		// OLD: foreach ($list as $i => $structure)
		foreach ($list as $structure)
		{
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', $structure->id, $structure->field);

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($structure);

		return $options;
	}

}
