<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerDB_Data_Manager
 * @description THM_GroupsControllerDB_Data_Manager class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');


/**
 * THM_GroupsControllerDB_Data_Manager is responsible for data migration for THM Groups component
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsControllerDB_Data_Manager extends JControllerLegacy
{
    /**
     * constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model = $this->getModel('DB_Data_Manager', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=thm_groups'));

        if ($model->execute())
        {
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
        }
        else
        {
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
        }
    }
}
