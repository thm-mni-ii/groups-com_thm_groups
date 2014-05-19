<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerDynamic_Types_Manager
 * @description THMGroupsControllerDynamic_Types_Manager class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');
jimport('joomla.log.log');
jimport('joomla.log.entry');


/**
 * THMGroupsControllerDynamic_Types_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerDynamic_Type_Manager extends JControllerLegacy
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
     * display task
     *
     * @return void
     */
    function display($cachable = false, $urlparams = false)
    {
        // Call parent behavior
        parent::display($cachable);
    }

    public function add()
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect("index.php?option=com_thm_groups&view=dynamic_type_edit&id=0");
    }

    public function edit()
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->input->set('view', 'dynamic_type_edit');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

    public function remove()
    {
        $model = $this->getModel('dynamic_type_manager');

        if ($model->remove())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_DELETED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_DELETED');
        }
        $this->setRedirect('index.php?option=com_thm_groups&view=dynamic_type_manager', $msg);
    }
}
