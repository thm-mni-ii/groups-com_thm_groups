<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerRole
 * @description THM_GroupsControllerRole class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');


/**
 * THM_GroupsControllerRole class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerRole extends JControllerLegacy
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
        $this->setRedirect("index.php?option=com_thm_groups&view=role_edit");
    }

    /**
     * Apply - Save button
     *
     * @return void
     */
    public function apply()
    {
        $model = $this->getModel('role');
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=role_edit&id=' . $success, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=role_edit', $msg);
        }
    }

    /**
     * Trash icon for group
     *
     * @return void
     */
    public function deleteGroup()
    {
        $model = $this->getModel('role');
        $success = $model->deleteGroup();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_ROLE_MANAGER_GROUP_DELETED');
            $type = 'message';
        }
        else
        {
            $this->setMessage(JText::sprintf('COM_THM_GROUPS_SAVE_ERROR', $model->getError()), 'warning');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=role_manager", $msg, $type);
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
        $model = $this->getModel('Role', '', array());

        // Preset the redirect
        $this->setRedirect('index.php?option=com_thm_groups&view=role_manager');

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

        $this->setRedirect('index.php?option=com_thm_groups&view=role_manager');
    }

    /**
     * Deletes the selected category and redirects to the category manager
     *
     * @return void
     */
    public function delete()
    {
        $model = $this->getModel('role');

        if ($model->delete())
        {
            $msg = JText::_('COM_THM_GROUPS_DELETE_SUCCESS');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DELETE_ERROR');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=role_manager", $msg);
    }

    /**
     * Redirects to the category_edit view for the editing of existing categories
     *
     * @return void
     */
    public function editRole()
    {
        $cid = $this->input->post->get('cid', array(), 'array');

        // Only edit the first id in the list
        if (count($cid) > 0)
        {
            $this->setRedirect(JRoute::_("index.php?option=com_thm_groups&view=role_edit&id=$cid[0]", false));
        }
        else
        {
            $this->setRedirect("index.php?option=com_thm_groups&view=role_edit");
        }
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
        $model = $this->getModel('role');
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=role_manager', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=role_manager' . $success, $msg);
        }
    }

    /**
     * Save2new
     *
     * @return void
     */
    public function save2new()
    {
        $model = $this->getModel('role');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect(Jroute::_('index.php?option=com_thm_groups&view=role_edit', false), $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=role_edit', $msg);
        }
    }
}
