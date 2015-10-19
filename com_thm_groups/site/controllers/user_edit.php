<?php
/**
 * @version     v3.2.7
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsControllerEdit
 * @description THMGroups component site edit controller
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

/**
 * Site edit controller class for component com_thm_groups
 *
 * Edit controller for the site section of the component
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsControllerUser_Edit extends JControllerLegacy
{
    public $uid = null;

    public $uname = null;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('delPic', '');
        $this->registerTask('backToRefUrl', '');
        $this->registerTask('apply', '');
        $this->registerTask('addTableRow', '');
        $this->registerTask('save', '');
    }

    /**
     *  Method to get the link, where the redirect has to go
     *
     * @since    Method available since Release 2.0
     *
     * @return   String  link
     */
    public function getLink()
    {
        $model = $this->getModel('user_edit');

        return $model->getLink();
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
        $app      = JFactory::getApplication();
        $model    = $this->getModel('user_edit');
        $file     = $app->input->files->get('data');
        $attrID   = $app->input->get('attrID');
        $filename = $app->input->get('filename');
        $userID   = $app->input->get('id');

        $success = $model->saveCropped($attrID, $file, $filename, $userID);

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
        $app    = JFactory::getApplication();
        $model  = $this->getModel('user_edit');
        $userID = $app->input->get('id');
        $attrID = $app->input->get('attrID');

        $success = $model->deletePicture($attrID, $userID);

        if ($success != 'false')
        {
            echo $success;
            $app->close();
        }
    }
}
