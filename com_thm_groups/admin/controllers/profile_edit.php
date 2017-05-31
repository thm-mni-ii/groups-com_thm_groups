<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerDynamic_Type
 * @description THMGroupsControllerDynamic_Type class from com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/controllers/profile_edit_controller.php';


/**
 * THMGroupsControllerProfile_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerProfile_Edit extends THM_GroupsControllerProfile_Edit_Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Calls saveCropped() in profile_edit model and gets back the new image path/false if
     * saveCropped() fails. Handles ajax call.
     *
     * Cropped images will be saved in the folder: '../images/' with the filename:
     * 'cropped_xyz.file-extension'. Other thumbnails will be created from original image
     * when save is pressed in profile_edit.view -> this will trigger the upload of the original
     * image. -> see save() in profile_edit model.
     *
     * @return   mixed
     */
    public function saveCropped()
    {
        $app      = JFactory::getApplication();
        $model    = $this->getModel('profile_edit');
        $file     = $app->input->files->get('data');
        $attrID   = $app->input->get('attrID');
        $filename = $app->input->get('filename');
        $userID   = $app->input->get('id');

        $success = $model->saveCropped($attrID, $file, $filename, $userID);

        if ($success != false)
        {
            // Draw new image preview in profile_edit.view
            echo $success;
            $app->close();
        }
    }

    /**
     * Calls delete function for picture in the model
     *
     * @return   mixed
     */
    public function deletePicture()
    {
        $input       = JFactory::getApplication()->input;
        $model       = $this->getModel();
        $attributeID = $input->getString('attrID');
        $userID      = $input->getInt('userID');

        $pictureName = $model->deletePicture($attributeID, $userID);

        if ($pictureName != 'false')
        {
            echo $pictureName;
        }
    }
}