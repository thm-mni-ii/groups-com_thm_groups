<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerEditRole
 * @description THMGroupsControllerEditRole class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerEditRole class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerEditRole extends JControllerForm
{

    /**
      * constructor (registers additional tasks to methods)
      *
      */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('apply', 'apply');
        $this->registerTask('save2new', 'save2new');
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
        JRequest::setVar('view', 'editrole');
        JRequest::setVar('layout', 'default');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Apply
     *
     * @return void
     */
    public function apply()
    {
        $model = $this->getModel('editrole');
        $id = JRequest::getVar('rid');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }
        $this->setRedirect('index.php?option=com_thm_groups&task=editrole.edit&cid[]=' . $id, $msg);
    }

    /**
       * Save
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
        $model = $this->getModel('editrole');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=rolemanager', $msg);
    }

    /**
     * Save2New
     *
     * @return void
     */
    public function save2new()
    {
        $model = $this->getModel('editrole');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=addrole', $msg);
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
        $this->setRedirect('index.php?option=com_thm_groups&view=rolemanager');
    }
}
