<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once HELPERS . 'groups.php';

/**
 * Class provides functions for modifying
 */
class THM_GroupsModelGroup extends JModelLegacy
{
    /**
     * Method to perform batch operations on an item or a set of items.
     *
     * @return  boolean  Returns true on success, false on failure.
     * @throws Exception
     */
    public function batch()
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $validActions = ['addRole', 'removeRole'];
        $action       = $app->input->getCmd('batch_action', '');

        if (empty($action) or !in_array($action, $validActions)) {
            return false;
        }

        if (!$roleIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('batch', [], 'array'))) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ROLE_SELECTED'), 'error');

            return false;
        }

        if (!$groupIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'))) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_GROUP_SELECTED'), 'error');

            return false;
        }

        $success = false;

        foreach ($roleIDs as $roleID) {
            foreach ($groupIDs as $groupID) {
                switch ($action) {
                    case 'addRole':

                        $success = THM_GroupsHelperGroups::associateRole($roleID, $groupID);
                        break;

                    case 'deleteRoleAssociation':

                        $success = $this->deleteRoleAssociation($roleID, $groupID);
                        break;
                }

                if (empty($success)) {
                    return false;
                }
            }
        }

        $this->cleanCache();

        return $success;
    }

    /**
     * Removes the association of a role to a group. Triggered by the trash icon next to the name of the role in the list.
     *
     * @param int $roleID  the id of the role to be removed
     * @param int $groupID the id of the group to be removed
     *
     * @return bool true if the association was successfully removed, otherwise false
     * @throws Exception
     */
    public function deleteRoleAssociation($roleID = 0, $groupID = 0)
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $input          = JFactory::getApplication()->input;
        $roleID         = empty($roleID) ? $input->getInt('roleID', 0) : $roleID;
        $groupID        = empty($groupID) ? $input->getInt('groupID', 0) : $groupID;
        $invalidRequest = (empty($roleID) or empty($groupID));

        if ($invalidRequest) {
            return false;
        }

        $query = $this->_db->getQuery(true);
        $query->delete('#__thm_groups_role_associations')->where("roleID = '$roleID'")->where("groupID = '$groupID'");
        $this->_db->setQuery($query);

        try {
            $success = $this->_db->execute();
        } catch (Exception $exc) {
            $app->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return empty($success) ? false : true;
    }
}
