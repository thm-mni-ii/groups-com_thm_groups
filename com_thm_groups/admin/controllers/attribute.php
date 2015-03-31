<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerAttribute
 * @description THMGroupsControllerAttribute class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * THMGroupsControllerAttribute class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.5
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
     * @param   boolean  $cachable   ?
     * @param   boolean  $urlparams  the urlparams
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
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
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
        $model = $this->getModel('attribute');

        // $isValid = $model->validateForm();
        $isValid = true;

        if ($isValid)
        {
            $success = $model->save();

            if ($success)
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=' . $success, $msg);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');

               // $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg);

            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_VALIDATION_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg, 'warning');
        }
    }

    /**
     * Edit
     *
     * @return void
     */
    public function edit()
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->input->set('view', 'attribute_edit');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Cancel
     *
     * @param   Integer  $key  contains the key
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager');
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
        $model = $this->getModel('attribute');
        //$isValid = $model->validateForm();
        $isValid = true;

        if ($isValid)
        {
            $success = $model->save();
            if ($success)
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager', $msg);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager' . $success, $msg);
            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_VALIDATION_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager', $msg, 'warning');
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
        $model = $this->getModel('attribute');

        //$isValid = $model->validateForm();
        $isValid = true;

        if ($isValid)
        {
            $success = $model->save();
            if ($success)
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg);
            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_VALIDATION_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg, 'warning');
        }
    }


    /**
     * Deletes the selected attribute from the database
     *
     * @return void
     */
    public function delete()
    {
        $model = $this->getModel('attribute_manager');

        if ($model->delete())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_DELETED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_DELETED');
        }
        $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager', $msg);
    }

    /**
     * Gets additional fields of dynamic type
     * Loads values of additonal fields from the specific dynamic type
     * when options of selected attribute are not set in database.
     *
     * @return void
     */
    public function getFieldExtras()
    {
        try
        {
            $app = Jfactory::getApplication();
            $dynTypeId = $app->input->get('dynTypeId');
            $attrID = $app->input->get('cid');
            $attOpt = $app->input->getHtml('attOpt');

            $result = $this->getModel('attribute_edit')->getFieldExtras($dynTypeId, $attrID, $attOpt);
            echo $result;
        }
        catch (Exception $e)
        {
            echo $e;
        }
    }
}
