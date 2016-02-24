<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsControllerProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/controllers/profile_edit_controller.php';

/**
 * THM_GroupsControllerProfile_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component
 * @package   com_thm_groups
 */
class THM_GroupsControllerProfile_Edit extends THM_GroupsControllerProfile_Edit_Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // TODO: Assuming this removes deprecated funtion calls. Do these still need to be here?
        $this->registerTask('delPic', '');
        $this->registerTask('backToRefUrl', '');
        $this->registerTask('apply', '');
        $this->registerTask('addTableRow', '');
        $this->registerTask('save', '');
    }
    /**
     * Calls picture delete function. Handles ajax call.
     *
     * @TODO  Output should be in a view.
     *
     * @return  string  the name of the default file on success, otherwise empty
     */
    public function deletePicture()
    {
        echo parent::saveCropped('frontend');
    }

    /**
     * Method to get the link where the redirect should go.
     *
     * @TODO  I'm assuming this is used in an AJAX Call. Why are there no AJAX views?
     *
     * @return   String  link
     */
    public function getLink()
    {
        $model = $this->getModel('profile_edit');
        return $model->getLink();
    }

    /**
     * Calls calls the saveCropped() function. Handles ajax call.
     *
     * @TODO  Output should be in a view.
     *
     * @return  void  the name of the saved file on success, otherwise empty
     */
    public function saveCropped()
    {
        echo parent::saveCropped('frontend');
    }
}
