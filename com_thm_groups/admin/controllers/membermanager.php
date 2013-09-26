<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllermembermanager
 * @description THMGroupsControllermembermanager class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

/**
 * THMGroupsControllermembermanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllermembermanager extends JController
{

    /**
      * constructor (registers additional tasks to methods)
      *
      */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit');
        $this->registerTask('setGroupsAndRoles', '');
        $this->registerTask('delGroupsAndRoles', '');
        $this->registerTask('publish', '');
        $this->registerTask('unpublish', '');
        $this->registerTask('deleteList', '');
        $this->registerTask('uploadPic', '');
        $this->registerTask('delPic', '');
        $this->registerTask('delGrouproleByUser', '');
        $this->registerTask('delAllGrouprolesByUser', '');
    }

    /**
       * Edit
       *
      * @return void
      */
    public function edit()
    {
        $user = JFactory::getUser();

        // Get user ids
        $uids = JRequest::getVar('cid', array(), 'post', 'array');

        foreach ($uids as $uid)
        {
            if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid == $user->get('id'))))
             && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid, 'core.admin'))))
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_EDIT_USER', true);
                $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
                return;
            }
        }
        JRequest::setVar('view', 'edit');
        JRequest::setVar('layout', 'forms');
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
        $model = $this->getModel('edit');
        $id = JRequest::getVar('userid');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            if ($model->getErrorMessage() != '')
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR') . '<br>' . $model->getErrorMessage();
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
            }
        }

        $this->setRedirect('index.php?option=com_thm_groups&task=membermanager.edit&cid[]=' . $id, $msg);
    }

    /**
       * Save
       *
      * @return void
      */
    public function save()
    {
        $model = $this->getModel('edit');

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
            $link = 'index.php?option=com_thm_groups&view=membermanager';
            $this->setRedirect($link, $msg);
        }
        else
        {
        }
    }

    /**
       * Cancel
       *
      * @return void
      */
    public function cancel()
    {
        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager');
    }

    /**
     * Edit
     *
     * @return void
     */
    public function delPic()
    {
        $model = $this->getModel('edit');
        $id = JRequest::getVar('userid');

        if ($model->delPic())
        {
            $msg = JText::_('COM_THM_GROUPS_PICTURE_REMOVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_REMOVE_PICTURE_ERROR');
        }
        $this->apply();
        $this->setRedirect('index.php?option=com_thm_groups&task=membermanager.edit&cid[]=' . $id, $msg);
    }

    /**
     * addTableRow
     *
     * @return void
     */
    public function addTableRow()
    {
        $model = $this->getModel('edit');

        // $id = JRequest::getVar('userid');

        if ($model->addTableRow())
        {
            $msg = JText::_('COM_THM_GROUPS_ROW_TO_TABLE');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_ROW_TO_TABLE_ERROR');
        }
        $this->apply();
    }

    /**
     * delTableRow
     *
     * @return void
     */
    public function delTableRow()
    {
        $model = $this->getModel('edit');

        // $id = JRequest::getVar('userid');

        if ($model->delTableRow())
        {
            $msg = JText::_('COM_THM_GROUPS_DEL_TABLE_ROW');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DEL_TABLE_ROW_ERROR');
        }
        $this->apply();
    }

    /**
     * editTableRow
     *
     * @return void
     */
    public function editTableRow()
    {
        $model = $this->getModel('edit');

        // $id = JRequest::getVar('userid');

        if ($model->editTableRow())
        {
            $msg = JText::_('COM_THM_GROUPS_EDIT_TABLE_ROW');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_EDIT_TABLE_ROW_ERROR');
        }

        $this->apply();
    }

    /**
     * Sets group and role parameters.
     *
     * This function gets group and role parameters from HTML-request/-view and calls
     * the corresponding model function to set group and role parameters in the database.
     *
     * @access public
     * @return void
     */
    public function setGroupsAndRoles()
    {
        // Get group-id
        $gid = JRequest::getVar('groups');

        // Get role-id
        $rids = JRequest::getVar('roles');
        $user = JFactory::getUser();

        // Get user ids
        $uids = JRequest::getVar('cid', array(), 'post', 'array');

        foreach ($uids as $uid)
        {
            if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid == $user->get('id'))))
             && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid, 'core.admin'))))
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_FOR_ADD_GROUP_AND_ROLES', true);
                $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
                return;
            }
        }
        foreach ($rids as $rid)
        {
            // Add group and role relations and display result
            $model = $this->getModel('edit');
            if ($model->setGroupsAndRoles($uids, $gid, $rid))
            {
                $msg = JText::_('COM_THM_GROUPS_USER_SUCCESSFULLY_ADDED_TO_GROUP');
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_USER_ADDED_TO_GROUP_ERROR');
            }
        }

        $model = $this->getModel('membermanager');
        $model->addGroupToUser($uids, $gid);

        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
    }

    /**
     * Deletes group and role parameters.
     *
     * This function gets group and role parameters from HTML-request/-view and calls
     * the corresponding model function to delete group and role parameters in the database.
     *
     * @access public
     * @return void
     */
    public function delGroupsAndRoles()
    {
        // Get group-id
        $gid = JRequest::getVar('groups');
        $rids = JRequest::getVar('roles');
        $user = JFactory::getUser();

        // Get user ids
        $uids = JRequest::getVar('cid', array(), 'post', 'array');
        foreach ($uids as $uid)
        {
            if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid == $user->get('id'))))
             && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid, 'core.admin'))))
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_FOR_DELETE_GROUP_AND_ROLES', true);
                $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
                return;
            }
        }
        foreach ($rids as $rid)
        {
            // Delete group and role relations and display result
            if (1 == $gid)
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_USER_FALSE', true);
            }
            else
            {
                $model = $this->getModel('membermanager');
                if ($model->delGroupsAndRoles($uids, $gid, $rid))
                {
                    $model->delGroupsToUser($uids, $gid);
                    $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_USER_TRUE', true);
                }
                else
                {
                    $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_BY_USER_FALSE', true);
                }
            }
        }
        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
    }

    /**
      * set user published (and redirect to view=membermanager)
      *
      * @return void
      */
    public function publish()
    {
        $user = JFactory::getUser();

        // Get user ids
        $uids = JRequest::getVar('cid', array(), 'post', 'array');
        foreach ($uids as $uid)
        {
            if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid == $user->get('id'))))
             && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid, 'core.admin'))))
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_CHANGE_STATE', true);
                $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
                return;
            }
        }

        $model = $this->getModel('membermanager');
        $result = $model->publish();
        if ($result)
        {
            $msg = JText::_('COM_THM_GROUPS_PUBLISHED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_PUBLISH_ERROR');
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);

    }

    /**
      * set user unpublished (and redirect to view=membermanager)
      *
      * @return void
      */
    public function unpublish()
    {
        $user = JFactory::getUser();

        // Get user ids
        $uids = JRequest::getVar('cid', array(), 'post', 'array');
        foreach ($uids as $uid)
        {
            if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid == $user->get('id'))))
             && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid, 'core.admin'))))
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_CHANGE_STATE', true);
                $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
                return;
            }
        }

        $model = $this->getModel('membermanager');
        $result = $model->unpublish();
        if ($result)
        {
            $msg = JText::_('COM_THM_GROUPS_UNPUBLISHED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_UNPUBLISH_ERROR');
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
    }

    /**
     * Delete
     *
     * @return void
     */
    public function delete()
    {
        $user = JFactory::getUser();

        // Get user ids
        $uids = JRequest::getVar('cid', array(), 'post', 'array');
        foreach ($uids as $uid)
        {
            if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid == $user->get('id'))))
             && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid, 'core.admin'))))
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_DELETE', true);
                $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
                return;
            }
        }

        $db = JFactory::getDBO();
        $cid = JRequest::getVar('cid', array(), 'post', 'array');
        JArrayHelper::toInteger($cid);

        // $cids = implode(',', $cid);

        foreach ($cid as $id)
        {
            /*
            $query = 'SELECT injoomla FROM #__thm_groups_additional_userdata'
                . ' WHERE userid= ' . $id;
            */
            $query = $db->getQuery(true);
            $query->select('injoomla');
            $query->from("#__thm_groups_additional_userdata");
            $query->where("userid = " . $id);

            $db->setQuery($query);
            $erg = $db->loadObjectList();

            if ($erg[0]->injoomla == '0')
            {
                /*
                $query = 'DELETE FROM #__thm_groups_date'
                    . ' WHERE userid = ' . $id . ';';
                    */
                $query = $db->getQuery(true);
                $query->from("#__thm_groups_date");
                $query->delete();
                $query->where("userid = " . $id);
                $db->setQuery($query);
                $db->query();

                /*
                $query = 'DELETE FROM #__thm_groups_number'
                    . ' WHERE userid = ' . $id . ';';
                    */
                $query = $db->getQuery(true);
                $query->from("#__thm_groups_number");
                $query->delete();
                $query->where("userid = " . $id);
                $db->setQuery($query);
                $db->query();

                /*
                $query = 'DELETE FROM #__thm_groups_picture'
                    . ' WHERE userid = ' . $id . ';';
                */
                $query = $db->getQuery(true);
                $query->from("#__thm_groups_picture");
                $query->delete();
                $query->where("userid = " . $id);
                $db->setQuery($query);
                $db->query();

                /*
                $query = 'DELETE FROM #__thm_groups_table'
                    . ' WHERE userid = ' . $id . ';';
                */
                $query = $db->getQuery(true);
                $query->from("#__thm_groups_table");
                $query->delete();
                $query->where("userid = " . $id);
                $db->setQuery($query);
                $db->query();

                /*
                $query = 'DELETE FROM #__thm_groups_text'
                    . ' WHERE userid = ' . $id . ';';
                */
                $query = $db->getQuery(true);
                $query->from("#__thm_groups_text");
                $query->delete();
                $query->where("userid = " . $id);
                $db->setQuery($query);
                $db->query();

                /*
                $query = 'DELETE FROM #__thm_groups_textfield'
                    . ' WHERE userid = ' . $id . ';';
                    */
                $query = $db->getQuery(true);
                $query->from("#__thm_groups_textfield");
                $query->delete();
                $query->where("userid = " . $id);
                $db->setQuery($query);
                $db->query();

                /*
                $query = 'DELETE FROM #__thm_groups_groups_map'
                    . ' WHERE uid = ' . $id . ';';
                    */
                $query = $db->getQuery(true);
                $query->from("#__thm_groups_groups_map");
                $query->delete();
                $query->where("userid = " . $id);

                $db->setQuery($query);
                $db->query();

                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_USER_DELETE_TRUE');
            }

            if ($erg[0]->injoomla == '1')
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_USER_DELETE_FALSE');
            }
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
    }

    /**
     * Delete All GroupRoles
     *
     * @return void
     */
    public function delAllGrouprolesByUser()
    {
        $gid = JRequest::getVar('g_id');
        $uid = array();
        $uid[0] = JRequest::getVar('u_id');

        $user = JFactory::getUser();

        if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid[0] == $user->get('id'))))
         && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid[0], 'core.admin'))))
        {
            $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_DEL_GROUPROLES', true);
            $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
            return;
        }
        $model = $this->getModel('membermanager');

        $rids = $model->getGroupRolesByUser($uid[0], $gid);

        if (1 == $gid)
        {
            $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_USER_FALSE', true);
        }
        else
        {
            $model->delGroupToUser($uid[0], $gid);

            foreach ($rids as $rid)
            {
                if ($model->delGroupsAndRoles($uid, $gid, $rid->rid))
                {
                    $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_USER_TRUE', true);
                }
                else
                {
                    $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_BY_USER_FALSE', true);
                }
            }
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
    }

    /**
     * Delete GroupRole
     *
     * @return void
     */
    public function delGrouproleByUser()
    {
        $gid = JRequest::getVar('g_id');
        $uid = array();
        $uid[0] = JRequest::getVar('u_id');
        $rid = JRequest::getVar('r_id');
        $user = JFactory::getUser();

        if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid[0] == $user->get('id'))))
         && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid[0], 'core.admin'))))
        {
            $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_DEL_GROUPROLE', true);
            $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
            return;
        }

        $model = $this->getModel('membermanager');

        if (1 == $gid)
        {
            $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_BY_USER_FALSE', true);
        }
        else
        {
            if ($model->delGroupsAndRoles($uid, $gid, $rid))
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_BY_USER_TRUE', true);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_DELETE_BY_USER_FALSE', true);
            }
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=membermanager', $msg);
    }
}
