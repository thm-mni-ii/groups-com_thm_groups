<?php
/**
 * @category    Joomla library
 * @package     THM_Core
 * @subpackage  lib_thm_core.site
 * @name        JFormFieldFields
 * @author      Lavinia Popa-Rössel, <lavinia.popa-roessel@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Class loads a list of fields for selection
 *
 * @category    Joomla.Library
 * @package     thm_core
 * @subpackage  lib_thm_core.site
 */
class JFormFieldIconPicker extends JFormField
{
	/**
	 * Type
	 *
	 * @var    String
	 */
	public $type = 'iconpicker';

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JForm $form The form to attach to the form field object.
	 */
	public function __construct()
	{
		parent::__construct();
		JHtml::_('bootstrap.framework');
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		JFactory::getDocument()->addScript(JUri::root() . "libraries/thm_core/js/iconpicker.js");
		$lang = JFactory::getLanguage();
		$lang->load('lib_thm_core', JPATH_SITE);

		// Generate the content of the button.
		// If an icon has been saved, it will be put at the button, otherwise "Select an Icon" will appear
		$labelContent = empty($this->value) ?
			JText::_('LIB_THM_CORE_SELECT') :
			'<span class="' . $this->value . '"></span><span class="iconName">' . str_replace("icon-", "", $this->value) . '</span>';

		$path = JPATH_ROOT . '/media/jui/css/icomoon.css';
		$file = fopen($path, 'r');

		$select = '<div class="btn-group iconPicker">';
		$select .= '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">';
		$select .= '<span data-bind="label">' . $labelContent . '</span></span>';
		$select .= '<span class="icon-arrow-down-3"></span></a>';
		$select .= '<ul class="dropdown-menu">';

		$select .= '<li><a onclick="selectIcon(event)">' . JText::_('JNONE') . '</a></li>';

		while (($line = fgets($file)) !== false)
		{
			if (strpos($line, '.icon-') !== false)
			{
				$suchMuster        = array("/(\.)/", "/(:before\s*{?,?)\s*/");
				$iconClassName     = preg_replace($suchMuster, "", $line);
				$displayedIconName = str_replace("icon-", "", $iconClassName);
				$selected          = (!empty($this->value) AND ($this->value === $iconClassName));
				$active            = $selected ? 'class="selected"' : '';

				$select .= '<li>';
				$select .= '<a onclick="selectIcon(event)"' . $active . '>';
				$select .= '<span class="' . $iconClassName . '"></span>';
				$select .= '<span class="iconName">' . $displayedIconName . '</span>';
				$select .= '</a></li>';
			}
		}

		$select .= '</ul>';
		$select .= '</div>';
		$select .= '<input type="hidden" name="jform[iconpicker]" id="jform_iconpicker" value="' . $this->value . '" />';

		fclose($file);

		return $select;
	}
}
