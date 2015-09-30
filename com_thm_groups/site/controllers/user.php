<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerUser
 * @description THM_GroupsControllerUser class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');

// For delete operation
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models', 'UsersModel');

/**
 * THM_GroupsControllerUser class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsControllerUser extends JControllerLegacy
{

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
        $model = $this->getModel('user', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=user_manager', false));

        if ($model->batch())
        {
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
        }
        else
        {
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
        }
    }

    public function publish()
    {
        $model = $this->getModel('user');
        $success = $model->toggle('publish');
        if ($success) {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_SUCCESS');
            $type = 'message';
        } else {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_FAIL');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=user_manager", $msg, $type);
    }

    public function unpublish()
    {
        $model = $this->getModel('user');
        $success = $model->toggle('unpublish');
        if ($success) {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_SUCCESS');
            $type = 'message';
        } else {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_FAIL');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=user_manager", $msg, $type);
    }

    /**
     * Toggles category behaviour properties
     *
     * @return void
     */
    public function toggle()
    {
        $model = $this->getModel('user');
        $success = $model->toggle();
        if ($success) {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_SUCCESS');
            $type = 'message';
        } else {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_FAIL');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=user_manager", $msg, $type);
    }

    public function createQuickpageForUser()
    {
        $model = $this->getModel('user');

        // TODO function need cid
        $model->createQuickpageCategoryForUser('');
    }

    public function deleteRoleInGroupByUser()
    {
        $model = $this->getModel('user');
        $success = $model->deleteRoleInGroupByUser();
        if ($success) {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_SUCCESS');
            $type = 'message';
        } else {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_FAIL');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=user_manager", $msg, $type);
    }

    public function deleteAllRolesInGroupByUser()
    {
        $model = $this->getModel('user');
        $success = $model->deleteAllRolesInGroupByUser();
        if ($success) {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_SUCCESS');
            $type = 'message';
        } else {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_FAIL');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=user_manager", $msg, $type);
    }

    public function apply()
    {
        $model = $this->getModel('user_edit');
        $app = JFactory::getApplication()->input;
        $data = $app->get('jform', array(), 'array');

        $userid = $data['gsuid'];
        $gsgid = $data['gsgid'];

        // Formvalidation is done in View via js
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');

            $this->setRedirect('index.php?option=com_thm_groups&view=user_edit&layout=default&tmpl=component&gsgid=' . $gsgid
                . '&gsuid=' . $userid, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            //todo: fails:
            $this->setRedirect('index.php?option=com_thm_groups&view=profile&layout=default&tmpl=component&gsgid=' . $gsgid
                . '&gsuid=' . $userid, $msg);
        }
    }

    public function save()
    {
        $model = $this->getModel('user_edit');
        $app = JFactory::getApplication();
        $formData = $app->input->post->get('jform', array(), 'array');;
        $userid = $formData['userID'];
        //var_dump($formData);
        //die();
        //Formvalidation is done in View via js
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=user_edit&layout=default&tmpl=component&gsuid=' . $userid, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            //todo: fails:
            $this->setRedirect('index.php?', $msg);
        }
    }

    public function cancel($key = null)
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=user_manager');
    }

    /**
     * Redirects to the category_edit view for the editing of existing categories
     *
     * @return void
     */
    public function edit()
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->input->set('view', 'user_edit');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

}