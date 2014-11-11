<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerStructuremanager
 * @description THMGroupsControllerStructuremanager class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerStructuremanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerStructuremanager extends JControllerForm
{
    /**
      * constructor (registers additional tasks to methods)
      *
      */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'add');
    }

    /**
       * Edit
       *
       * @param   Integer  $key     contain key
       * @param   String   $urlVar  contain url
       *
      * @return void
      *
      * @SuppressWarnings(PHPMD.UnusedFormalParameter)
      */
    public function edit($key = null, $urlVar = null)
    {

        $cid = JRequest::getVar('cid',   array(), 'post', 'array');
        for ($i = 1; $i < 5; $i++)
        {
            if (in_array($i, $cid))
            {
                $msg = JText::_('COM_THM_GROUPS_EDIT_ERROR');
                $this->setRedirect('index.php?option=com_thm_groups&view=structuremanager', $msg);
            }
        }

        JRequest::setVar('view', 'editstructure');
        JRequest::setVar('layout', 'default');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Add
     *
     * @return void
     */
    public function add()
    {
        JRequest::setVar('view', 'addstructure');
        JRequest::setVar('layout', 'default');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Cancel
     *
     *@param   Integer  $key  contains the key
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        $msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
        $this->setRedirect('index.php?option=com_thm_groups', $msg);
    }

    /**
     * Remove
     *
     * @return void
     */
    public function remove()
    {
        $model = $this->getModel('structuremanager');

        if ($model->remove())
        {
            $msg = JText::_('COM_THM_GROUPS_REMOVED_SUCCESSFUL');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_REMOVE_ERROR');
        }

        $cid = JRequest::getVar('cid', array(), 'post', 'array');

        for ($i = 1; $i < 5; $i++)
        {
            if (in_array($i, $cid))
            {
                echo "<br />";
                $msg .= JText::_('COM_THM_GROUPS_CAN_NOT_DELETE_ITEM');
                echo " " . $i;
            }
        }
        $this->setRedirect('index.php?option=com_thm_groups&view=structuremanager', $msg);
    }

    /**
     * Save order
     *
     * @return void
     */
    public function saveorder()
    {
        $model = $this->getModel('structuremanager');

        if ($model->reorder())
        {
            $msg = JText::_('COM_THM_GROUPS_ORDER_SUCCESSFUL');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_ORDER_ERROR');
        }
        $this->setRedirect('index.php?option=com_thm_groups&view=structuremanager', $msg);
    }

    /**
     * Order up
     *
     * @return void
     */
    public function orderup()
    {
        $model = $this->getModel('structuremanager');

        if ($model->reorder(-1))
        {
            $msg = JText::_('COM_THM_GROUPS_ORDER_SUCCESSFUL');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_ORDER_ERROR');
        }
        $this->setRedirect('index.php?option=com_thm_groups&view=structuremanager', $msg);
    }

    /**
     * Order down
     *
     * @return void
     */
    public function orderdown()
    {
        $model = $this->getModel('structuremanager');

        if ($model->reorder(1))
        {
            $msg = JText::_('COM_THM_GROUPS_ORDER_SUCCESSFUL');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_ORDER_ERROR');
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=structuremanager', $msg);
    }
}
