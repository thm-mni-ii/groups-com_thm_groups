<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerProfile_Edit_Controller
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * THM_GroupsControllerProfile_Edit_Controller class for component com_thm_groups
 *
 * @category  Joomla.Component
 * @package   com_thm_groups
 */
class THM_GroupsControllerProfile_Edit_Controller extends JControllerLegacy
{
	/**
	 * Calls saveCropped() in profile_edit model and gets back the new image path/false if
	 * saveCropped() fails. Handles ajax call.
	 *
	 * Cropped images will be saved in the folder: '../images/' with the filename:
	 * 'cropped_xyz.file-extension'. Other thumbnails will be created from original image
	 * when save is pressed in profile_edit.view -> this will trigger the upload of the original
	 * image. -> see save() in profile_edit model.
	 *
	 * @param   string $area   the area which calls the function. backend is set as default because it was used in the
	 *                         creation of this file.
	 *
	 * @TODO  make a flipping ajax view
	 *
	 * @return  string  the name of the saved file on success, otherwise empty
	 */
	public function saveCropped()
	{
		$input     = JFactory::getApplication()->input;
		$modelPath = JPATH_SITE . '/media/com_thm_groups/models';
		$this->addModelPath($modelPath);
		$model       = $this->getModel('profile_edit');
		$attributeID = $input->get('attrID');
		$file        = $input->files->get('data');
		$fileName    = $input->get('filename');
		$userID      = $input->get('id');

		$savedName = $model->saveCropped($attributeID, $file, $fileName, $userID);
		echo empty($savedName) ? '' : $savedName;
	}

	/**
	 * Calls delete function for picture in the model
	 *
	 *
	 * @param   string $area   the area which calls the function. backend is set as default under the assumption that
	 *                         the coding there is more complete
	 *
	 * @return  string  the name of the default file on success, otherwise empty
	 */
	public function deletePicture($area = 'backend')
	{
		$input     = JFactory::getApplication()->input;
		$modelPath = ($area == 'backend') ? JPATH_ADMINISTRATOR : JPATH_SITE;
		$modelPath .= '/components/com_thm_groups/models';
		$this->addModelPath($modelPath);
		$model       = $this->getModel('profile_edit');
		$attributeID = $input->getString('attrID');
		$userID      = $input->getInt('id');

		$defaultName = $model->deletePicture($attributeID, $userID);
		echo empty($defaultName) ? '' : $defaultName;
	}
}