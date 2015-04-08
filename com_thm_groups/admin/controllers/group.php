<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerGroup
 * @description THM_GroupsControllerGroup class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * THM_GroupsControllerGroup class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsControllerGroup extends JControllerForm
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
     * Redirects to the group_edit view for the creation of new element
     *
     * @return object
     */
    public function add()
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect("index.php?option=com_users&view=group&layout=edit");

        parent::display();
    }

    /**
     * Add/Delete moderator button
     *
     * @return void
     */
    public function editModerator()
    {
        $model = $this->getModel('group');
        $success = $model->editModerator();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $type = 'message';
        }
        else
        {
            $this->setMessage(JText::sprintf('COM_THM_GROUPS_SAVE_ERROR', $model->getError()), 'warning');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=user_select&tmpl=component", $msg, $type);
    }

    /**
     * Trash icon for moderator
     *
     * @return void
     */
    public function deleteModerator()
    {
        $model = $this->getModel('group');
        $success = $model->deleteModerator();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_GROUP_MANAGER_MODERATOR_DELETED');
            $type = 'message';
        }
        else
        {
            $this->setMessage(JText::sprintf('COM_THM_GROUPS_SAVE_ERROR', $model->getError()), 'warning');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=group_manager", $msg, $type);
    }

    /**
     * Trash icon for role
     *
     * @return void
     */
    public function deleteRole()
    {
        $model = $this->getModel('group');
        $success = $model->deleteRole();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_GROUP_MANAGER_ROLE_DELETED');
            $type = 'message';
        }
        else
        {
            $this->setMessage(JText::sprintf('COM_THM_GROUPS_SAVE_ERROR', $model->getError()), 'warning');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=group_manager", $msg, $type);
    }

    /**
     * Apply - Save button
     *
     * @return void
     */
    public function apply()
    {
        $model = $this->getModel('group');
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=group_edit&id=' . $success, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=group_edit&id=0', $msg);
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
        $model = $this->getModel('group', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=group_manager' . $this->getRedirectToListAppend(), false));

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
     * Method to run batch operations.
     *
     * @param   object  $model  The model.
     *
     * @return  boolean  True on success, false on failure
     *
     * @since   2.5
     */
    public function batchProfile($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model = $this->getModel('group', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=group_manager' . $this->getRedirectToListAppend(), false));

        if ($model->batchProfile())
        {
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
        }
        else
        {
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
        }
    }

    /**
     * Redirects to the group manager view without making any persistent changes
     *
     * @param   Integer  $key  contains the key
     *
     * @return  void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=group_manager');
    }

    /**
     * Deletes the selected group and redirects to the group manager
     *
     * @return void
     */
    public function delete()
    {
        $model = $this->getModel('group');

        if ($model->delete())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_DELETED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_DELETED');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=group_manager", $msg);

    }

    /**
     * Redirects to the group_edit view for the editing of existing groups
     *
     * @return void
     */
    public function editGroup()
    {

        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $app = JFactory::getApplication();
        $ids = $app->input->get('cid', array(), 'array');

        JArrayHelper::toInteger($ids);

        // Input->get because id is in url
        $id = (empty($ids)) ? $app->input->get->getInt('id') : $ids[0];

        if (!empty($id))
        {
            $url = JRoute::_("index.php?option=com_users&view=group&layout=edit&id=$id", false);
            $this->setRedirect($url);
        }

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
        $model = $this->getModel('group');
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=group_manager', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=group_manager' . $success, $msg);
        }
    }

    /**
     * Save2new
     *
     * @return void
     */
    public function save2new()
    {
        $model = $this->getModel('group');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=group_edit&id=0', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=group_edit&id=0', $msg);
        }
    }
}
