<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerUser
 * @description THM_GroupsControllerUser class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

// For delete operation
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models', 'UsersModel');

/**
 * THM_GroupsControllerUser class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerUser extends JControllerLegacy
{
    /**
     * @TODO: This function is being called instead of save during frontend editing...
     *
     * @throws Exception
     */
    public function apply()
    {
        $model = $this->getModel('profile_edit');
        $app = JFactory::getApplication()->input;
        $data = $app->get('jform', array(), 'array');

        $userID = $data['userID'];
        $groupID = $data['groupID'];
        $menuID = $data['menuID'];
        $canEdit = THM_GroupsHelperComponent::canEditProfile($userID, $groupID);

        $baseURL = 'index.php?option=com_thm_groups';
        $view = '&view=profile';
        $query = "&groupID=$groupID&userID=$userID&Itemid=$menuID";
        if(!$canEdit)
        {
            $url = JRoute::_($baseURL . $view . $query);
            $msg = JText::_('COM_THM_GROUPS_NOT_ALLOWED');
            $this->setRedirect($url, $msg, 'error');
        }

        $success = $model->save();
        if ($success)
        {
            $url = JRoute::_($baseURL . $view . $query);
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect($url, $msg);
        }
        else
        {
            $view = '&view=profile_edit';
            $url = JRoute::_($baseURL . $view . $query);
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            //todo: fails:
            $this->setRedirect($url, $msg);
        }
    }

    /**
     * Method to run batch operations.
     *
     * @param   object  $model  The model.
     *
     * @return  boolean  True on success, false on failure
     *
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

    public function save()
    {
        $model = $this->getModel('profile_edit');
        $app = JFactory::getApplication();
        $formData = $app->input->post->get('jform', array(), 'array');;
        $userid = $formData['userID'];

        //Formvalidation is done in View via js
        // TODO: Check here anyways... WTF?!
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_edit&layout=default&tmpl=component&userID=' . $userid, $msg);
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

        $this->input->set('view', 'profile_edit');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

}