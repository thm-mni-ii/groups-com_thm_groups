<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * Class provides functions for modifying
 */
class THM_GroupsModelGroup extends JModelLegacy
{
    /**
     * Associates a role with a given group, ignoring existing associations with the given role
     *
     * @param   int   $roleID  the id of the role to be associated
     * @param   array $groupID the group with which the role ist to be associated
     *
     * @return bool true on success, otherwise false
     */
    private function associateRole($roleID, $groupID)
    {
        $existingQuery = $this->_db->getQuery(true);

        // First, we need to check if the profile is already assigned to a group
        $existingQuery->select('rolesID')
            ->from('#__thm_groups_role_associations')
            ->where("usergroupsID = '$groupID'")
            ->where("rolesID = '$roleID'");
        $this->_db->setQuery($existingQuery);

        try {
            $exists = $this->_db->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        if ($exists) {
            return true;
        }

        $insertQuery = $this->_db->getQuery(true);

        $insertQuery->insert('#__thm_groups_role_associations')->columns(['usergroupsID', 'rolesID']);
        $insertQuery->values("$groupID, $roleID");
        $this->_db->setQuery($insertQuery);

        try {
            $this->_db->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return true;
    }

    /**
     * Associates a template with a given group, overwriting any existing association
     *
     * @param   int   $templateID the id of the template to be associated
     * @param   array $groupID    the group with which the template ist to be associated
     *
     * @return bool true on success, otherwise false
     */
    private function associateTemplate($templateID, $groupID)
    {
        $existingQuery = $this->_db->getQuery(true);

        // First, we need to check if the profile is already assigned to a group
        $existingQuery->select('templateID')->from('#__thm_groups_template_associations')->where("usergroupsID = '$groupID'");
        $this->_db->setQuery($existingQuery);

        try {
            $existingTemplateID = $this->_db->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        // There are no previously saved group-profile mappings
        if (empty($existingTemplateID)) {
            $insertQuery = $this->_db->getQuery(true);

            $insertQuery->insert('#__thm_groups_template_associations')->columns(['usergroupsID', 'templateID']);
            $insertQuery->values("$groupID, $templateID");

            $this->_db->setQuery($insertQuery);

            try {
                $this->_db->execute();
            } catch (Exception $exception) {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

                return false;
            }
        } else {
            $updateQuery = $this->_db->getQuery(true);
            $updateQuery->update('#__thm_groups_template_associations')
                ->set("templateID = $templateID")
                ->where("usergroupsID = $groupID");

            $this->_db->setQuery($updateQuery);

            try {
                $this->_db->execute();
            } catch (Exception $exception) {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Method to perform batch operations on an item or a set of items.
     * TODO make generic function which handle all types of batch operations
     *
     * @return  boolean  Returns true on success, false on failure.
     */
    public function batch()
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $validActions = ['addRole', 'addTemplate', 'removeRole', 'removeTemplate'];
        $action       = $app->input->getCmd('batch_action', '');

        if (empty($action) or !in_array($action, $validActions)) {
            return false;
        }

        $isRoleAction = strpos($action, 'Role') !== false;

        // Role or Template IDs depending upon the batch used
        $batchSelected = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('batch', [], 'array'));

        if (empty($batchSelected)) {
            if ($isRoleAction) {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ROLE_SELECTED'), 'error');
            } else {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_TEMPLATE_SELECTED'), 'error');
            }

            return false;
        }

        $groupIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));

        // Should not be able to occur because of the checks before the batch is opened
        if (empty($groupIDs)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_GROUP_SELECTED'), 'error');

            return false;
        }

        $success = false;

        foreach ($batchSelected as $selectedID) {
            foreach ($groupIDs as $groupID) {
                switch ($action) {
                    case 'addRole':

                        $success = $this->associateRole($selectedID, $groupID);
                        break;

                    case 'addTemplate':
                        $success = $this->associateTemplate($selectedID, $groupID);
                        break;

                    case 'removeRole':

                        $success = $this->removeRole($selectedID, $groupID);
                        break;

                    case 'removeTemplate':

                        $success = $this->removeTemplate($selectedID, $groupID);
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
     * @param  int $roleID  the id of the role to be removed
     * @param  int $groupID the id of the group to be removed
     *
     * @return bool true if the association was successfully removed, otherwise false
     */
    public function removeRole($roleID = 0, $groupID = 0)
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
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
        $query->delete('#__thm_groups_role_associations')->where("rolesID = '$roleID'")->where("usergroupsID = '$groupID'");
        $this->_db->setQuery($query);

        try {
            $success = $this->_db->execute();
        } catch (Exception $exc) {
            $app->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return empty($success) ? false : true;
    }

    /**
     * Removes the association of a template to a group. Triggered by the trash icon next to the name of the template in the list.
     *
     * @param  int $templateID the id of the template to be removed
     * @param  int $groupID    the id of the group to be removed
     *
     * @return bool true if the association was successfully removed, otherwise false
     */
    public function removeTemplate($templateID = 0, $groupID = 0)
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $input          = JFactory::getApplication()->input;
        $templateID     = empty($templateID) ? $input->getInt('templateID', 0) : $templateID;
        $groupID        = empty($groupID) ? $input->getInt('groupID', 0) : $groupID;
        $invalidRequest = (empty($templateID) or empty($groupID));

        if ($invalidRequest) {
            return false;
        }

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_template_associations')
            ->where("templateID = '$templateID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $exc) {
            $app->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return true;
    }
}
