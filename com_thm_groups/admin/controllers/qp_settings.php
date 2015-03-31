<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerQuickpage
 * @description THM_GroupsControllerQuickpage class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');


/**
 * THM_GroupsControllerQuickpage class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsControllerQp_Settings extends JControllerLegacy
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
     * Apply - Save button
     *
     * @return void
     */
    public function apply()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('jform', array(), 'array');

        $model = $this->getModel('qp_settings');
        $success = $model->save($data);
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=qp_settings&tmpl=component', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=qp_settings&tmpl=component', $msg);
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
        $model = $this->getModel('qp_settings');
        $success = $model->save();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $this->setRedirect('index.php?option=com_thm_groups&view=qp_settings', $msg);
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=qp_settings' . $success, $msg);
        }
    }

    // TODO change model name
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
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=role_manager');
    }
}
