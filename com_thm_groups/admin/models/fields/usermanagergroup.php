<?php
defined('_JEXEC') or die('Restricted access');
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldUsermanagergroup extends JFormFieldList
{

    protected $type = 'usermanagergroup';

    /**
     *
     * @var    array
     * @since  3.2
     */
    protected static $options = array();

    /**
     * returns a list of moderators
     *
     * @return  Array
     */
    public function getGroupsFromDB()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $nestedQuery = $db->getQuery(true);
        $nestedQuery
            ->select('id')
            ->from('#__thm_groups_users');


        $query
            ->select('g.id, g.title')
            ->from('#__usergroups AS g')
            ->innerJoin('#__thm_groups_usergroups_roles AS a ON g.id = a.usergroupsID')
            ->innerJoin('#__thm_groups_users_usergroups_roles AS b ON a.id = b.usergroups_rolesID')
            ->where('b.usersID IN (' . $nestedQuery .')')
            ->where('g.id NOT IN  (1,2)')
            ->group('g.id')
            ->order('g.title ASC');

        $db->setQuery($query);
        $db->execute();

        return $db->loadAssocList();
    }

    /**
     * Method to get the options to populate to populate list
     *
     * @return  array  The field option objects.
     *
     * @since   3.2
     */
    protected function getOptions()
    {
        // Accepted modifiers
        $hash = md5($this->element);

        if (!isset(static::$options[$hash]))
        {
            static::$options[$hash] = parent::getOptions();
            $options = array();

            $arrayOfGroups = $this->getGroupsFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('COM_THM_GROUPS_USER_MANAGER_SHOW_ALL_GROUPS'));
            foreach ($arrayOfGroups as $key => $value) :
                $options[] = JHTML::_('select.option', $value['id'], $value['title']);
            endforeach;

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}