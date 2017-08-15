<?php
/**
 * @category    Joomla component
 * @package     THM_Organizer
 * @subpackage  com_thm_groups.site
 * @name        template for modal display of crop box
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
$name        = $this->pictureName;
$attributeID = $this->attributeID;
?>
<div class='modal fade modalFade' id='<?php echo $name; ?>_Modal' role='dialog' aria-labelledby='modal-title'
	 aria-hidden='true'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<div class="header-title">
					<h3 id='modal-title'>
						<span class="icon-upload"></span>
						<?php echo JText::_('COM_THM_GROUPS_PICTURE_UPLOAD_DIALOGUE'); ?>
					</h3>
					<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
						<span aria-hidden='true'><span class="icon-remove"></span></span>
					</button>
					<div class="clear"></div>
				</div>
				<div class="toolbar">
					<input type='file' id='jform_<?php echo $name; ?>' class='file'
						   name='jform1[Picture][<?php echo $attributeID; ?>]'/>
				</div>
			</div>
			<div id='<?php echo $name; ?>_Modal_Body' class='modal-body modalPicture'>
				<div id='<?php echo $name; ?>_leftContent' class='crop-container'>
					<div id='<?php echo $name; ?>_imageBox' class='imageBox'>
						<div id='<?php echo $name; ?>_thumbBox' class='thumbBox'></div>
						<div id='<?php echo $name; ?>_spinner' class='spinner' style='display: none'></div>
					</div>
				</div>
			</div>
			<div class='modal-footer'>
				<button type='button' id='<?php echo $name; ?>_btnZoomIn' class='btn hasTip'
						title='<?php echo JTEXT::_('COM_THM_GROUPS_ZOOM_IN_TIP'); ?>'>
					<span class="icon-zoom-in"></span>
				</button>
				<button type='button' id='<?php echo $name; ?>_btnZoomOut' class='btn hasTip'
						title='<?php echo JTEXT::_('COM_THM_GROUPS_ZOOM_OUT_TIP'); ?>'>
					<span class="icon-zoom-out"></span>
				</button>
				<button type='button' id='<?php echo $name; ?>_switch' class='btn hasTip'
						title='<?php echo JTEXT::_('COM_THM_GROUPS_TOGGLE_CROP_DIRECTION_TIP'); ?>'>
					<span class="icon-redo-2"></span><?php echo JTEXT::_('COM_THM_GROUPS_TOGGLE_CROP_DIRECTION'); ?>
				</button>
				<button type='button' id='<?php echo $name; ?>_saveChanges' class='btn hasTip upload'
						title='<?php echo JTEXT::_('COM_THM_GROUPS_CROP_UPLOAD_TIP'); ?>'>
					<span class="icon-upload"></span><?php echo JText::_('COM_THM_GROUPS_CROP_UPLOAD'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
