<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

/**
 * Class provides methods for building a model of the roles in JSON format
 */
class THM_GroupsModelRole_Ajax extends JModelLegacy
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
        $query  = $this->_db->getQuery(true);
        $jinput = JFactory::getApplication()->input;

        $groupID = $jinput->get->get('batch-groups', '', 'int');

        $nestedQuery = $this->_db->getQuery(true);
        $nestedQuery
            ->select('id')
            ->from('#__thm_groups_profiles');


        $query
            ->select('r.id, r.name')
            ->from('#__thm_groups_roles AS r')
            ->innerJoin('#__thm_groups_role_associations AS a ON r.id = a.roleID')
            ->group('r.id')
            ->order('r.name ASC');

        if (!empty($groupID)) {
            $query->where("a.groupID = $groupID");
        }

        $this->_db->setQuery($query);
        $this->_db->execute();

        $roles = $this->_db->loadObjectList();

        $result = [];
        foreach ($roles as $role) {
            $result[(int)$role->id] = $role->name;
        }

        $result = json_encode($result);

        return $result;
    }
}
