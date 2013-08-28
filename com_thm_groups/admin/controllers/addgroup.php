<?php
/**
 * @version     v3.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerAddGroup
 * @description THMGroupsControllerAddGroup class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser, <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof, <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla, <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May, <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerAddGroup class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerAddGroup extends JControllerForm
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
      */
    public function edit($key = null, $urlVar = null)
    {
        JRequest::setVar('view', 'editgroup');
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
        $model = $this->getModel('addgroup');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }

        $id = JRequest::getVar('cid[]');

        $this->setRedirect('index.php?option=com_thm_groups&task=addgroup.edit&cid[]=' . $id, $msg);
    }

    /**
       * Save
       *
       * @param   Integer  $key     contain key
       * @param   String   $urlVar  contain url
       *
      * @return void
      */
    public function save($key = null, $urlVar = null)
    {
        $model = $this->getModel('addgroup');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=groupmanager', $msg);
    }

    /**
     * Save data
     *
     * @return void
     */
    public function save2new()
    {
        $model = $this->getModel('addgroup');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=addgroup', $msg);
    }

    /**
     * Cancel
     *
     * @param   Integer  $key  contains the key
     *
     * @return void
     */
    public function cancel($key = null)
    {
        $msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
        $this->setRedirect('index.php?option=com_thm_groups&view=groupmanager', $msg);
    }
}
