<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerDynamic_Type
 * @description THMGroupsControllerDynamic_Type class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THMGroupsControllerDynamic_Type_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerDynamic_Type extends JControllerLegacy
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
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $this->setRedirect("index.php?option=com_thm_groups&view=dynamic_type_edit&id=0");
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

        $model = $this->getModel('dynamic_type');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_edit&id=' . $success, $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_edit&id=0', $msg);
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
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_manager');
    }

    /**
     * Deletes the selected category and redirects to the category manager
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

        $model = $this->getModel('dynamic_type');

        if ($model->delete())
        {
            $msg = JText::_('COM_THM_GROUPS_DELETE_SUCCESS');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DELETE_ERROR');
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=dynamic_type_manager", $msg);
    }

    /**
     * Redirects to the category_edit view for the editing of existing categories
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
        $this->input->set('view', 'dynamic_type_edit');
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
        $canEdit = THM_GroupsHelperComponent::canEdit();
        if (!$canEdit)
        {
            return;
        }

        $model = $this->getModel('dynamic_type');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_manager', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_manager' . $success, $msg);
        }
    }

    /**
     * Save2new
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

        $model = $this->getModel('dynamic_type');

        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_edit&id=0', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_edit&id=0', $msg);
        }
    }
}
