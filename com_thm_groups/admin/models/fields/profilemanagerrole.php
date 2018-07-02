<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldProfilemanagerRole extends JFormFieldList
{

    protected $type = 'profilemanagerrole';

    /**
     * Cached array of the category items.
     *
     * @var    array
     */
    protected static $options = [];

    /**
     * Retrieves a list of roles, optionally for a selected group
     *
     * @return  Array
     */
    public function getRolesFromDB()
    {
        $input = JFactory::getApplication()->input;

        $list = $input->post->get('list', [], 'array');
        if (!empty($list['groupID'])) {
            $groupID = $list['groupID'];
        }

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $nestedQuery = $dbo->getQuery(true);
        $nestedQuery
            ->select('id')
            ->from('#__thm_groups_profiles');

        $query
            ->select('role.id, role.name')
            ->from('#__thm_groups_roles AS role')
            ->innerJoin('#__thm_groups_role_associations AS roleAssoc ON role.id = roleAssoc.rolesID')
            ->innerJoin('#__thm_groups_associations AS assoc ON roleAssoc.id = assoc.role_assocID')
            ->where('assoc.profileID IN (' . $nestedQuery . ')')
            ->group('role.id')
            ->order('role.name ASC');

        if (!empty($groupID)) {
            $query->where("roleAssoc.usergroupsID = $groupID");
        }

        $dbo->setQuery($query);
        $dbo->execute();

        return $dbo->loadAssocList();
    }

    /**
     * Method to get the options to populate to populate list
     *
     * @return  array  The field option objects.
     *
     */
    protected function getOptions()
    {
        // Accepted modifiers
        $hash = md5($this->element);

        if (!isset(static::$options[$hash])) {
            static::$options[$hash] = parent::getOptions();
            $options                = [];

            $arrayOfRoles = $this->getRolesFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('COM_THM_GROUPS_FILTER_BY_ROLE'));
            foreach ($arrayOfRoles as $key => $value) {
                $options[] = JHTML::_('select.option', $value['id'], $value['name']);
            }
            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}
