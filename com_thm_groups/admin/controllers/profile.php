<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerProfile
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/controllers/profile_edit_controller.php';

/**
 * THM_GroupsControllerProfile class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsControllerProfile extends THM_GroupsControllerProfile_Edit_Controller
{
    /**
     * Activates quickpages for multiple users
     *
     * @return void
     *
     * @throws Exception
     */
    public function activateQPForUser()
    {
        // TODO: Access checks here

        $model = $this->getModel('profile');
        JFactory::getApplication()->input->set('attribute', 'qpPublished');
        $success = $model->toggle('publish');
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_FAIL');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

    /**
     * Saves the entry and redirects to the edit interface
     *
     * @return  void
     */
    public function apply()
    {
        $canSave = $this->canSave();
        if (!$canSave)
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $profileID = $this->getModel('profile_edit')->save();

        if ($profileID)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_edit&userID=' . $profileID, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_edit&userID=0', $msg, 'error');
        }
    }

    /**
     * Method to run batch operations.
     *
     * @param   object $model The model.
     *
     * @return  boolean  True on success, false on failure
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $success = $this->getModel('profile')->batch();
        if ($success)
        {
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
        }
        else
        {
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
        }

        // Batch messages are added to the application in the model.
        $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=profile_manager', false));
    }

    /**
     * Redirects to the profile manager after cancelling creation/editing
     *
     * @param null $key
     *
     * @todo  the formal parameter seems not just to be unused, but also unnecessary
     *
     * @return  void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        $this->setRedirect('index.php?option=com_thm_groups&view=profile_manager');
    }

    /**
     * Checks whether the user has access to save
     *
     * @todo  Standardize this across the backend/frontend with manage and item id checks
     *
     * @return  bool true if user has access, otherwise false
     */
    private function canSave()
    {
        $canCreate = JFactory::getUser()->authorise('core.create', 'com_thm_groups');
        $canEdit   = JFactory::getUser()->authorise('core.edit', 'com_thm_groups');

        return ($canCreate OR $canEdit);
    }

    /**
     * Redirects to the profile_edit view for the editing of existing user
     *
     * @return void
     */
    public function edit()
    {
        $cid = $this->input->post->get('cid', array(), 'array');

        // Only edit the first id in the list
        if (count($cid) > 0)
        {
            $this->setRedirect(JRoute::_("index.php?option=com_thm_groups&view=profile_edit&userID=$cid[0]", false));
        }
        else
        {
            $this->setRedirect("index.php?option=com_thm_groups&view=profile_edit");
        }
    }

    /**
     * Sets or unsets the published tag for a profile
     *
     * @return  void
     */
    public function publish()
    {
        $model = $this->getModel('profile');
        JFactory::getApplication()->input->set('attribute', 1);
        $success = $model->toggle('publish');
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

    /**
     * Unpublish user
     */
    public function unpublish()
    {
        $model   = $this->getModel('profile');
        $success = $model->toggle('unpublish');
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

    /**
     * Deactivates quickpages for multiple users
     *
     * @return void
     *
     * @throws Exception
     */
    public function deactivateQPForUser()
    {
        $model = $this->getModel('profile');
        JFactory::getApplication()->input->set('attribute', 'qpPublished');
        $success = $model->toggle('unpublish');
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

    /**
     * Toggles category behaviour properties
     *
     * @return void
     */
    public function toggle()
    {
        $model   = $this->getModel('profile');
        $success = $model->toggle();
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

    /**
     * Deletes a role of a user by user id
     *
     * @return void
     */
    public function deleteRoleInGroupByUser()
    {
        $model   = $this->getModel('profile');
        $success = $model->deleteRoleInGroupByUser();
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

    /**
     * Deletes all roles in a group by user ID
     *
     * @return void
     */
    public function deleteAllRolesInGroupByUser()
    {
        $model   = $this->getModel('profile');
        $success = $model->deleteAllRolesInGroupByUser();
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
    }

    /**
     * Saves the entry and redirects to the manager interface
     *
     * @return  void
     */
    public function save()
    {
        $model = $this->getModel('profile_edit');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_manager', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            //todo: fails:
            $this->setRedirect('index.php?option=com_thm_groups&view=profile_manager', $msg);
        }
    }
}
