<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';


/**
 * THM_GroupsViewProfile_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.thm.de
 */
class THM_GroupsViewProfile_Edit_View extends JViewLegacy
{
	public $profileID;

	public $groupID;

	public $name;

	public $menuID;

	public $attributes = null;

	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template (default: null)
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$input           = JFactory::getApplication()->input;
		$standardID      = $input->getInt('id', 0);
		$this->profileID = $input->getInt('profileID', $standardID);

		$canEdit = THM_GroupsHelperComponent::canEditProfile($this->profileID);

		if (!$canEdit)
		{
			$exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
			JErrorPage::render($exc);
		}

		$this->menuID  = $input->getInt('Itemid');
		$this->groupID = $input->getInt('groupID', 1);

		// Get user data for edit view.
		$this->attributes = $this->getModel()->getAttributes($this->profileID);
		$this->name       = empty($this->attributes[2]['value']) ? $input->get('name', '') : $this->attributes[2]['value'];
		$this->modifyDocument();

		if (method_exists($this, 'addToolBar'))
		{
			$this->addToolBar();
		}

		parent::display($tpl);
	}

	/**
	 * Creates the HTML for the crop button
	 *
	 * @param   string $name        the name of the attribute
	 * @param   int    $attributeID the id of the attribute type
	 * @param   int    $profileID   the id of the user profile being edited
	 *
	 * @return  string  the HTML output of the crop button
	 */
	public function getChangeButton($name, $attributeID, $profileID)
	{
		$button = '<button type="button" id="' . $name . '_upload" class="btn image-button" ';
		$button .= 'onclick="bindImageCropper(\'' . $name . '\', \'' . $attributeID . '\', \'' . $profileID . '\');" ';
		$button .= 'data-toggle="modal" data-target="#' . $name . '_Modal">';
		$button .= '<span class="icon-edit"></span>' . JText::_('COM_THM_GROUPS_EDITGROUP_BUTTON_PICTURE_CHANGE');
		$button .= '</button>';

		return $button;
	}

	/**
	 * Creates the HTML for the picture delete button
	 *
	 * @param   string $name        the name of the attribute
	 * @param   int    $attributeID the id of the attribute type
	 * @param   int    $id          the id of the user profile being edited
	 *
	 * @return  string  the HTML output of the delete button
	 */
	public function getPicDeleteButton($name, $attributeID, $id)
	{
		$button = '<button id="' . $name . '_del" class="btn image-button" ';
		$button .= 'onclick="deletePic(\'' . $name . '\', \'' . $attributeID . '\', \'' . $id . '\');" ';
		$button .= 'type="button">';
		$button .= '<span class="icon-delete"></span>' . JText::_('COM_THM_GROUPS_DELETE');
		$button .= '</button>';

		return $button;
	}

	/**
	 * Creates an advanced image upload field
	 *
	 * @param   array $attribute      the attribute being iterated
	 * @param   array $nameAttributes the name attributes for the profile being edited
	 *
	 * @return  string  the HTML output of the image field
	 */
	public function getPicture($attribute, $nameAttributes)
	{
		$name        = $attribute['name'];
		$attributeID = $attribute['structid'];
		$value       = !empty(trim($attribute['value'])) ? trim($attribute['value']) : '';

		$html = '<div id="' . $name . '_IMG" class="image-container">';

		if (!empty($value))
		{
			$options = $attribute['options'];

			$position     = explode('images/', $options['path'], 2);
			$relativePath = 'images/' . $position[1];
			$filePath     = JPATH_ROOT . "/$relativePath{$attribute['value']}";

			if (file_exists($filePath))
			{
				$file = JURI::root() . $relativePath . $value;
				$alt  = (count($nameAttributes) == 2) ? implode(', ', $nameAttributes) : end($nameAttributes);
				$html .= JHtml::image($file, $alt, array('class' => 'edit_img'));
			}
		}

		$html .= '</div>';
		$html .= '<input id="jform_' . $name . '_value" name="jform[' . $name . '][value]" type="hidden" value="' . $value . '" />';

		$html .= '<div class="image-button-container">';
		$html .= $this->getChangeButton($name, $attributeID, $this->profileID);
		$html .= $this->getPicDeleteButton($name, $attributeID, $this->profileID);
		$html .= '</div>';

		// Load variables into context for the crop modal template
		$this->pictureName = $name;
		$this->attributeID = $attributeID;
		$this->addTemplatePath(JPATH_ROOT . '/media/com_thm_groups/templates');
		$html .= $this->loadTemplate('crop');
		unset($this->pictureName);
		unset($this->attributeID);

		return $html;
	}

	/**
	 * Creates a checkbox for the published status of the attribute being iterated
	 *
	 * @param   string $inputName the name of the attribute
	 * @param   bool   $published whether or not the attribute is already published
	 *
	 * @return  string  the HTML checkbox output
	 */
	public function getPublishBox($inputName, $published)
	{
		$html = '<input type="checkbox" ';
		$html .= 'id="jform_' . $inputName . '_published" ';
		$html .= 'name="jform[' . $inputName . '][published]" ';
		if ($published)
		{
			$html .= 'checked="checked" ';
		}
		$html .= '/>';

		return $html;
	}

	/**
	 * Creates a checkbox for the structid of the attribute being iterated
	 *
	 * @param   string $inputName  the name of the attribute
	 * @param   string $structName the name of the property
	 * @param   mixed  $value      the property value (string|int)
	 *
	 * @return  string  the HTML checkbox output
	 */
	public function getStructInput($inputName, $structName, $value)
	{
		$html = '<input type="hidden" ';
		$html .= 'id="jform_' . $inputName . '_' . $structName . '" ';
		$html .= 'name="jform[' . $inputName . '][' . $structName . ']" ';
		$html .= 'value="' . $value . '" ';
		$html .= '/>';

		return $html;
	}

	/**
	 * Creates a text input
	 *
	 * @param   object $attribute the attribute being iterated
	 *
	 * @return  string  the HMTL text field output
	 */
	public function getText($attribute)
	{
		$options  = $attribute['options'];
		$required = (!empty($options) AND !empty($options['required'])) ? $options['required'] : '';
		$html     = '<input type="text" ';
		$html     .= 'id="jform_' . $attribute['name'] . '" ';
		$html     .= 'name="jform[' . $attribute['name'] . '][value]" ';
		$html     .= 'class="hasTooltip" ';
		$html     .= 'data="" ';
		$html     .= 'data-original-title="' . $attribute['description'] . '" ';
		$html     .= 'data-placement="right" ';
		$html     .= 'data-req="' . $required . '" ';
		$html     .= 'onchange="validateInput(\'' . $attribute['regex'] . '\', \'jform_' . $attribute['name'] . '\')" ';
		$html     .= 'value="' . $attribute['value'] . '" />';

		return $html;
	}

	/**
	 * Modifies the document
	 *
	 * @return void
	 */
	protected function modifyDocument()
	{
		JHtml::_('jquery.framework');
		JHtml::_('bootstrap.tooltip');
		JHtml::_('behavior.framework', true);
		JHtml::_('behavior.formvalidation');
		JHtml::_('formbehavior.chosen', 'select');

		JHtml::stylesheet('media/com_thm_groups/css/profile_edit.css');
		JHtml::script('media/com_thm_groups/js/formbehaviorChosenHelper.js');
		JHtml::script('media/com_thm_groups/js/cropbox.js');

		// TODO: Are both of these necessary? Assuming that the second one checks against the dyntype regex...
		JHtml::script('media/com_thm_groups/js/validators.js');
		JHtml::script('media/com_thm_groups/js/inputValidation.js');

		// Close modal after editing
		JFactory::getDocument()->addScriptDeclaration("window.onbeforeunload = function() { window.parent.location.reload(); };");
	}
}
