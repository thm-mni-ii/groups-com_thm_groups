<?php
/**
 * @version		$Id: mod_giessen_latestnews.php 190 2009-01-24 01:17:06Z kernelkiller $
 * @package		Joomla
 * @subpackage	GiessenLatestNews
 * @author		Frithjof Kloes
 * @copyright	Copyright (C) 2008 FH Giessen-Friedberg / University of Applied Sciences
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Provides a selectbox with all available menuitems.
 *
 * @param boolean multiple	If set to '1', more than one item can be selected
 * parameter_pattern:	Comma-seperated items ids (selected_itemid_1,selected_itemid_2)
 */
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldStructureSelect extends JFormField {
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */


	function getInput() {
		$scriptDir = str_replace(JPATH_SITE.DS,'',"administrator/components/com_thm_groups/elements/");
		$sortButtons = true;

		// add script-code to the document head
		JHTML::script('structureselect.js', $scriptDir, false);

     	// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes '.(string) $this->element['class'].'"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="'.$this->name.'"'.$class.'>';

		// Get selected items
        $selected = $this->value;

        // Parse selected items
        $selectedItems = array();
        if ($selected != "") {
	        foreach ($selected as $item) {
				$tempItem = array();
				$tempItem['id'] = substr($item, 0, strlen($item) - 2);
				$tempItem['showName'] = substr($item, -2, 1);
				$tempItem['wrapAfter'] = substr($item, -1, 1);
				$selectedItems[] = $tempItem;
	        }
        }

		// Get the field options.
		$options = $this->getOptions($selectedItems);

		// Build the checkbox field output.
		$html[] = '<table>' .
				'<thead>' .
				'<tr><th>' .
				JText::_( 'Attribute' ) .
				'</th><th>' .
				JText::_( 'Show' ) .
				'</th><th>' .
				JText::_( 'Name' ) .
				'</th><th>' .
				JText::_( 'Wrap' ) .
				'</th></tr>' .
				'</thead>' .
				'<tbody>';
		foreach ($options as $i => $option) {
			// Initialize some option attributes.
			$value = null;
			$checked = '';
			$checkedShowName = '';
			$checkedWrapAfter = '';
			$disabled = '';
			foreach ($selectedItems as $item) {
				if ($item['id'] == $option->value) {
					$checked = ' checked="checked"';
					if ($item['showName'] == "1") {
						$checkedShowName = ' checked="checked"';
					} else {
						$checkedShowName = '';
					}
					if ($item['wrapAfter'] == "1") {
						$checkedWrapAfter = ' checked="checked"';
					} else {
						$checkedWrapAfter = '';
					}
					$value = $option->value.$item['showName'].$item['wrapAfter'];
				}
			}
			if (!isset($value)) {
				$value = $option->value."00";
				$disabled = ' disabled="disabled"';
			}
			//$class = !empty($option->class) ? ' class="'.$option->class.'"' : '';
			//$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';

			$html[] = '<tr><td>' .
					'<label for="'.$this->name.$i.'"'.$class.'>'.JText::_($option->text).'</label>' .
					'</td><td>' .
					'<input type="checkbox" id="'.$this->name.$i.'" name="'.$this->name.'['.$i.']"'.' value="'.$value.'" onchange="switchEnablingAdditionalAttr('."'".$this->name.$i."'".')"'.$checked.' />' .
					'</td><td>' .
					'<input type="checkbox" id="'.$this->name.$i.'ShowName" onchange="switchAttributeName('."'".$this->name.$i."'".')"'.$checkedShowName.$disabled.' />' .
					'</td><td>' .
					'<input type="checkbox" id="'.$this->name.$i.'WrapAfter" onchange="switchAttributeWrap('."'".$this->name.$i."'".')"'.$checkedWrapAfter.$disabled.' />' .
					'</td></tr>';
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
	 * @return	array	The field option objects.
	 */
	protected function getOptions($selected) {
		$query="SELECT a.id, a.field FROM `#__thm_groups_structure` as a Order by a.order";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$list= $db->loadObjectList();

		// Initialize variables.
		$options = array();

		foreach ($list as $i => $structure) {
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', $structure->id, $structure->field);

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($structure);

		return $options;
	}

}
?>