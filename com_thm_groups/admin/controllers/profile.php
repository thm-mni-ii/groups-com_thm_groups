<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerProfile
 * @description THM_GroupsControllerProfile class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');


/**
 * THM_GroupsControllerProfile class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsControllerProfile extends JControllerLegacy
{
    /**
     * constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Redirects to the dynamic_type_edit view for the creation of new element
     *
     * @return object
     */
    public function add()
    {
        if (!JFactory::getUser()->authorise('core.create', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $input = JFactory::getApplication()->input;
        $input->set('view', 'profile_edit');
        $input->set('id', '0');
        parent::display();
    }

    /**
     * Apply - Save button
     *
     * @return void
     */
    public function apply()
    {
        $model = $this->getModel('profile');
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_edit&id=' . $success, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_edit&id=0', $msg);
        }
    }

    /**
     * Redirects to the category manager view without making any persistent changes
     *
     * @param   Integer  $key  contains the key
     *
     * @return  void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=profile_manager');
    }

    /**
     * Deletes the selected category and redirects to the category manager
     *
     * @return void
     */
    public function delete()
    {
        $model = $this->getModel('profile');

        if ($model->delete())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_DELETED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_DELETED');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg);
    }

    /**
     * Redirects to the category_edit view for the editing of existing categories
     *
     * @return void
     */
    public function edit()
    {
        if (!JFactory::getUser()->authorise('core.edit', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->input->set('view', 'profile_edit');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Save&Close button
     *
     * @param   Integer  $key     contain key
     * @param   String   $urlVar  contain url
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function save($key = null, $urlVar = null)
    {
        $model = $this->getModel('profile');
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_manager', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_manager' . $success, $msg);
        }
    }

    /**
     * Save2new
     *
     * @return void
     */
    public function save2new()
    {
        $model = $this->getModel('profile');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_edit&id=0', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_edit&id=0', $msg);
        }
    }

    /**
     * Method to run batch operations.
     *
     * @param   object  $model  The model.
     *
     * @return  boolean  True on success, false on failure
     *
     * @since   2.5
     */
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model = $this->getModel('profile', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=profile_manager', false));

        if ($model->batch())
        {
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
        }
        else
        {
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
        }
    }

    /**
     * Trash icon for group
     *
     * @return void
     */
    public function deleteGroup()
    {
        $model = $this->getModel('profile');
        $success = $model->deleteGroup();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_PROFILE_MANAGER_GROUP_DELETED');
            $type = 'message';
        }
        else
        {
            $this->setMessage(JText::sprintf('COM_THM_GROUPS_SAVE_ERROR', $model->getError()), 'warning');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

}
