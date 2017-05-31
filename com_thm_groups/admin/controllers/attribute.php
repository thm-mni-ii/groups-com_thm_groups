<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerAttribute
 * @description THMGroupsControllerAttribute class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

define('VORNAME', 1);
define('NACHNAME', 2);
define('EMAIL', 4);
define('TITEL', 5);
define('POSTTITEL', 7);

jimport('joomla.application.component.controller');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';


/**
 * THMGroupsControllerAttribute class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerAttribute extends JControllerLegacy
{
    /**
     * Constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display task
     *
     * @param   boolean $cachable  ?
     * @param   boolean $urlparams the urlparams
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
        // Call parent behavior
        parent::display($cachable);
    }

    /**
     * Adding
     *
     * @return mixed
     */
    public function add()
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $this->setRedirect("index.php?option=com_thm_groups&view=attribute_edit&id=0");
    }

    /**
     * Apply - Save button
     *
     * @return void
     */
    public function apply()
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $model       = $this->getModel('attribute');
        $success     = $model->save();
        $rowsCreated = $model->createEmptyRowsForAllUsers($success);

        if ($rowsCreated)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&id=' . $success, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&id=0', $msg);

        }
    }

    /**
     * Edit
     *
     * @return void
     */
    public function edit()
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $this->input->set('view', 'attribute_edit');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Cancel
     *
     * @param   Integer $key contains the key
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager');
    }

    /**
     * Save&Close button
     *
     * @param   Integer $key    contain key
     * @param   String  $urlVar contain url
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function save($key = null, $urlVar = null)
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $model   = $this->getModel('attribute');
        $success = $model->save();

        if ($success)
        {
            $rowsCreated = $model->createEmptyRowsForAllUsers($success);

            if ($rowsCreated)
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager', $msg);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ATTRIBUTE_EMPTY_ROWS_ERROR');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager&id=' . $success, $msg);
            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager$id=' . $success, $msg);
        }
    }

    /**
     * Saves the selected attribute and redirects to a new page
     * to create a new attribute
     *
     * @return void
     */
    public function save2new()
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $model = $this->getModel('attribute');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&id=0', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&id=0', $msg);
        }
    }

    /**
     * Deletes selected attributes
     *
     * @return void
     */
    public function delete()
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $doNotDelete = array(VORNAME, NACHNAME, EMAIL, TITEL, POSTTITEL);
        $ids         = JFactory::getApplication()->input->get('cid', array(), 'array');
        $idsToDelete = array_diff($ids, $doNotDelete);

        $redirectURL = 'index.php?option=com_thm_groups&view=attribute_manager';
        if (empty($idsToDelete))
        {
            $msg  = JText::_("COM_THM_GROUPS_CANT_DELETE_ERROR");
            $type = 'warning';
            $this->setRedirect($redirectURL, $msg, $type);

            return;
        }

        $success = $this->getModel('attribute')->delete($idsToDelete);
        if ($success)
        {
            $msg  = JText::_('COM_THM_GROUPS_DELETE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg  = JText::_('COM_THM_GROUPS_DELETE_ERROR');
            $type = 'error';
        }
        $this->setRedirect($redirectURL, $msg, $type);
    }

    /**
     * Toggles category behaviour properties
     *
     * @return void
     */
    public function toggle()
    {
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $model   = $this->getModel('attribute');
        $success = $model->toggle();
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
        $this->setRedirect("index.php?option=com_thm_groups&view=attribute_manager", $msg, $type);
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     */
    public function saveOrderAjax()
    {
        // Get the input
        $pks   = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        Joomla\Utilities\ArrayHelper::toInteger($pks);
        Joomla\Utilities\ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel('attribute');

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }
}
