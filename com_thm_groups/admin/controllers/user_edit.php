<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerDynamic_Type
 * @description THMGroupsControllerDynamic_Type class from com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * THMGroupsControllerUser_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.5
 */
class THM_GroupsControllerUser_Edit extends JControllerLegacy
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Calls saveCropped() in user_edit model and gets back the new image path/false if
     * saveCropped() fails. Handles ajax call.
     *
     * Cropped images will be saved in the folder: '../images/' with the filename:
     * 'cropped_xyz.file-extension'. Other thumbnails will be created from original image
     * when save is pressed in user_edit.view -> this will trigger the upload of the original
     * image. -> see save() in user_edit model.
     *
     * @return   mixed
     */
    public function saveCropped()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel('user_edit');
        $file = $app->input->files->get('data');
        $attrID = $app->input->get('attrID');
        $filename = $app->input->get('filename');

        $success = $model->saveCropped($attrID, $file, $filename);
        if ($success != false)
        {
            // Draw new image preview in user_edit.view
            echo $success;
        }
    }

    /**
     * Calls delete function for picture in the model
     *
     * @return   mixed
     */
    public function deletePicture()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel('user_edit');
        $attrID = $app->input->get('attrID');

        $success = $model->deletePicture($attrID);
        if ($success != false)
        {
            echo $success;
            $app->close();
        }
    }
}