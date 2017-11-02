<?php
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldUsermanagerrole extends JFormFieldList
{

    protected $type = 'usermanagerrole';

    /**
     * Cached array of the category items.
     *
     * @var    array
     */
    protected static $options = array();

    /**
     * Retrieves a list of roles, optionally for a selected group
     *
     * @return  Array
     */
    public function getRolesFromDB()
    {
        $input = JFactory::getApplication()->input;

        $list = $input->post->get('list', array(), 'array');
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
            ->select('r.id, r.name')
            ->from('#__thm_groups_roles AS r')
            ->innerJoin('#__thm_groups_role_associations AS a ON r.id = a.rolesID')
            ->innerJoin('#__thm_groups_associations AS b ON a.id = b.usergroups_rolesID')
            ->where('b.usersID IN (' . $nestedQuery . ')')
            ->group('r.id')
            ->order('r.name ASC');

        if (!empty($groupID)) {
            $query->where("a.usergroupsID = $groupID");
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
            $options                = array();

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
