<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerUser
 * @description THM_GroupsControllerUser class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';
require_once JPATH_SITE . '/media/com_thm_groups/controllers/profile_edit_controller.php';


/**
 * THM_GroupsControllerUser class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerProfile extends THM_GroupsControllerProfile_Edit_Controller
{
    private $_baseURL = 'index.php?option=com_thm_groups';

    private $_userID;

    private $_groupID;

    private $_menuID;

    private $_name;

    private $_referrer;

    /**
     * Saves changes to the profile and returns to the edit view
     *
     * @return  void
     */
    public function apply()
    {
        $this->preProcess();
        $success = $this->getModel('profile_edit')->save();

        if ($success)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
        }
        else
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_ERROR'), 'error');
        }

        // This method of redirection allows the referrer to be input directly into the context
        $this->input->set('view', 'profile_edit');
        $this->input->set('groupID', $this->_groupID);
        $this->input->set('userID', $this->_userID);
        $this->input->set('Itemid', $this->_menuID);
        $this->input->set('name', $this->_name);
        $this->input->set('referrer', $this->_referrer);
        parent::display();
    }

    /**
     * Saves changes to the profile and redirects to the profile on success
     *
     * @return  void
     */
    public function save2Profile()
    {
        $this->preProcess();

        $success = $this->getModel('profile_edit')->save();

        $query = "&groupID=$this->_groupID&userID=$this->_userID&Itemid=$this->_menuID&name=$this->_name";
        if ($success)
        {
            $url = JRoute::_($this->_baseURL . '&view=profile' . $query);
            $msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
            $this->setRedirect($url, $msg);
        }
        else
        {
            $url = JRoute::_($this->_baseURL . '&view=profile_edit' . $query);
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            //todo: fails:
            $this->setRedirect($url, $msg);
        }
    }

    /**
     * Sets object variables and checks access rights. Redirects on insufficient access.
     *
     * @return  void
     */
    private function preProcess()
    {
        $app = JFactory::getApplication()->input;
        $data = $app->get('jform', array(), 'array');

        $this->_userID = $data['userID'];
        $this->_groupID = $data['groupID'];
        $this->_menuID = $data['menuID'];
        $this->_name = $data['name'];
        $this->_referrer = $data['referrer'];
        $canEdit = THM_GroupsHelperComponent::canEditProfile($this->_userID, $this->_groupID);

        $query = "&view=profile&groupID=$this->_groupID&userID=$this->_userID&Itemid=$this->_menuID&name=$this->_name";
        if(!$canEdit)
        {
            $url = JRoute::_($this->_baseURL . $query);
            $msg = JText::_('COM_THM_GROUPS_NOT_ALLOWED');
            $this->setRedirect($url, $msg, 'error');
        }
        return;
    }

    /**
     * Calls calls the saveCropped() function. Handles ajax call.
     *
     * @TODO  Output should be in a view.
     *
     * @return  void  the name of the saved file on success, otherwise empty
     */
    public function saveCropped()
    {
        parent::saveCropped();
    }

    public function createQuickpageForUser()
    {
        $model = $this->getModel('user');

        // TODO function need cid
        $model->createQuickpageCategoryForUser('');
    }
}