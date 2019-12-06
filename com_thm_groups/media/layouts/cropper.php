<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * Class provides the image upload / cropper template
 */
class THM_GroupsLayoutCropper
{
	/**
	 * Gets the HTML to be used for the button to close the modal
	 *
	 * @return string the close button HTML
	 */
	private static function getCloseButton()
	{
		$html = '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
		$html .= '<span aria-hidden="true"><span class="icon-remove"></span></span>';
		$html .= '</button>';

		return $html;
	}

	/**
	 * Gets the HTML to be used for the button to zoom into the picture
	 *
	 * @param   int  $attributeID  the id of the attribute
	 *
	 * @return string the upload button HTML
	 */
	private static function getCropDirectionButton($attributeID)
	{
		$html = '<button type="button" id="switch-' . $attributeID . '" class="btn hasTip" ';
		$html .= 'title="' . JTEXT::_('COM_THM_GROUPS_TOGGLE_CROP_DIRECTION_TIP') . '">';
		$html .= '<span class="icon-redo-2"></span>' . JTEXT::_('COM_THM_GROUPS_TOGGLE_CROP_DIRECTION');
		$html .= '</button>';

		return $html;
	}

	/**
	 * Gets the cropper image upload /crop input
	 *
	 * @param   array  $attribute  the attribute with which the uploaded image will be associated
	 *
	 * @return string the HTML of the cropper modal
	 */
	public static function getCropper($attribute)
	{
		$attributeID = $attribute['id'];
		JFactory::getDocument()->addScriptDeclaration("const rootURI = '" . JUri::base() . "';");

		// This changes the crop direction display before the element is loaded.
		if ($attribute['mode'] === '0')
		{
			$landscapeScript = "jQuery(document).ready(function(){\n";
			$landscapeScript .= "  jQuery('#modal-{$attributeID}').on('show.bs.modal', function (e) {\n";
			$landscapeScript .= "    const thumbBox = document.getElementById('thumbBox-{$attributeID}'),\n";
			$landscapeScript .= "          style = window.getComputedStyle(thumbBox),\n";
			$landscapeScript .= "          height = style.getPropertyValue('height'),\n";
			$landscapeScript .= "          width = style.getPropertyValue('width');\n\n";
			$landscapeScript .= "    thumbBox.style.height = width;\n";
			$landscapeScript .= "    thumbBox.style.width = height;\n";
			$landscapeScript .= "  });\n";
			$landscapeScript .= "});";
			JFactory::getDocument()->addScriptDeclaration($landscapeScript);
		}

		// Modal Open
		$html = '<div class="modal fade modalFade" id="modal-' . $attributeID . '" ';
		$html .= 'role="dialog" aria-labelledby="modal-title" aria-hidden="true">';

		// Dialog Open
		$html .= '<div class="modal-dialog">';

		// Content Open
		$html .= '<div class="modal-content">';

		$html .= '<div class="modal-header">';
		$html .= '<div class="header-title">';
		$html .= self::getTitle();
		$html .= self::getCloseButton();
		$html .= '<div class="clear"></div>';
		$html .= '</div>';
		$html .= '<div class="toolbar">';
		$html .= '<input type="file" id="jform_' . $attributeID . '" class="file" accept="' . $attribute['accept'] . '"';
		$html .= 'name="jform[' . $attribute['id'] . '][file]"/>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div id="modal-body-' . $attributeID . '" class="modal-body modalPicture">';
		$html .= '<div id="left-content-' . $attributeID . '" class="crop-container">';
		$html .= '<div id="imageBox-' . $attributeID . '" class="imageBox">';
		$html .= '<div id="thumbBox-' . $attributeID . '" class="thumbBox"></div>';
		$html .= '<div id="spinner-' . $attributeID . '" class="spinner" style="display: none"></div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="modal-footer">';
		$html .= self::getZoomInButton($attributeID);
		$html .= self::getZoomOutButton($attributeID);

		// if a mode is set the button should not be shown
		if ($attribute['mode'] == '-1')
		{
			$html .= self::getCropDirectionButton($attributeID);
		}

		$html .= self::getUploadButton($attributeID);
		$html .= '</div>';

		// End dialog
		$html .= '</div>';

		// End dialog
		$html .= '</div>';

		// End Modal
		$html .= '</div>';

		return $html;
	}

	/**
	 * Gets the HTML to be used for the title
	 *
	 * @return string the title HTML
	 */
	private static function getTitle()
	{
		$html = '<h3 id="modal-title">';
		$html .= '<span class="icon-upload"></span>' . JText::_('COM_THM_GROUPS_IMAGE_BUTTON_UPLOAD');
		$html .= '</h3>';

		return $html;
	}

	/**
	 * Gets the HTML to be used for the button to upload the picture
	 *
	 * @param   int  $attributeID  the id of the attribute
	 *
	 * @return string the upload button HTML
	 */
	private static function getUploadButton($attributeID)
	{
		$html = '<button type="button" id="saveChanges-' . $attributeID . '" class="btn hasTip upload" ';
		$html .= 'title="' . JTEXT::_('COM_THM_GROUPS_CROP_UPLOAD_TIP') . '">';
		$html .= '<span class="icon-upload"></span>' . JText::_('COM_THM_GROUPS_CROP_UPLOAD');
		$html .= '</button>';

		return $html;
	}

	/**
	 * Gets the HTML to be used for the button to zoom into the picture
	 *
	 * @param   int  $attributeID  the id of the attribute
	 *
	 * @return string the upload button HTML
	 */
	private static function getZoomInButton($attributeID)
	{
		$html = '<button type="button" id="btnZoomIn-' . $attributeID . '" class="btn hasTip" ';
		$html .= 'title="' . JTEXT::_('COM_THM_GROUPS_ZOOM_IN_TIP') . '">';
		$html .= '<span class="icon-zoom-in"></span>';
		$html .= '</button>';

		return $html;
	}

	/**
	 * Gets the HTML to be used for the button to zoom out of the picture
	 *
	 * @param   int  $attributeID  the id of the attribute
	 *
	 * @return string the upload button HTML
	 */
	private static function getZoomOutButton($attributeID)
	{
		$html = '<button type="button" id="btnZoomOut-' . $attributeID . '" class="btn hasTip" ';
		$html .= 'title="' . JTEXT::_('COM_THM_GROUPS_ZOOM_OUT_TIP') . '">';
		$html .= '<span class="icon-zoom-out"></span>';
		$html .= '</button>';

		return $html;
	}
}
