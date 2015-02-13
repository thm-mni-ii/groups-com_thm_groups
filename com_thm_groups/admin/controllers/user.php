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

        //Formvalidation is done in View via js
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=user_edit&id=' . $success, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            //todo: fails:
            $this->setRedirect('index.php?option=com_thm_groups&view=user_edit&id=0', $msg);
        }
    }

    public function save()
    {
        $model = $this->getModel('user_edit');

        //Formvalidation is done in View via js
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=user_manager', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            //todo: fails:
            $this->setRedirect('index.php?option=com_thm_groups&view=user_manager', $msg);
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
}