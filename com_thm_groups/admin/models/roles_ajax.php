<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelRoles
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

/**
 * Class provides methods for building a model of the roles in JSON format
 *
 * @category    Joomla.Component.Site
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelRoles_Ajax extends JModelLegacy
{
    /**
     * Returns all roles of specified group
     *
     * @return json string
     *
     * @throws Exception
     */
    public function getRolesOfGroup()
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $jinput = JFactory::getApplication()->input;

        $groupID = $jinput->get->get('batch-groups', '', 'int');

        $nestedQuery = $db->getQuery(true);
        $nestedQuery
            ->select('id')
            ->from('#__thm_groups_users');


        $query
            ->select('r.id, r.name')
            ->from('#__thm_groups_roles AS r')
            ->innerJoin('#__thm_groups_usergroups_roles AS a ON r.id = a.rolesID')
            ->group('r.id')
            ->order('r.name ASC');

        if (!empty($groupID))
        {
            $query->where("a.usergroupsID = $groupID");
        }

        $db->setQuery($query);
        $db->execute();

        $roles = $db->loadObjectList();

        $result = array();
        foreach ($roles as $role)
        {
            $result[(int) $role->id] = $role->name;
        }

        $result = json_encode($result);

        return $result;
    }
}
