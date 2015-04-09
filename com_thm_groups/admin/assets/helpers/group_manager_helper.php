<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsHelperGroup_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

/**
 * Class providing helper functions for group manager
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperGroup_Manager
{
    /**
     * Return all existing roles as select field
     *
     * @return  array  An array of options for drop-down list
     */
    public static function getRoles()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id AS value, name AS text')
            ->from('#__thm_groups_roles')
            ->order('id');
        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }

        for ($i = 0, $n = count($options); $i < $n; $i++)
        {
            $roles[] = JHtml::_('select.option', $options[$i]->value, $options[$i]->text);
        }

        return $roles;
    }

    /**
     * Returns groups as select field
     * It shows only groups with users in it, because this select field
     * will be used only for filtering in backend-user-manager
     *
     * @return array
     */
    public static function getGroups()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $nestedQuery = $db->getQuery(true);
        $nestedQuery
            ->select('id')
            ->from('#__thm_groups_users');

        $query
            ->select('a.id, a.title')
            ->select('COUNT(DISTINCT b.id) AS level')
            ->from('#__usergroups as a')
            ->join('LEFT', '#__usergroups  AS b ON a.lft > b.lft AND a.rgt < b.rgt')
            ->innerJoin('#__thm_groups_usergroups_roles AS c ON a.id = c.usergroupsID')
            ->innerJoin('#__thm_groups_users_usergroups_roles AS d ON c.id = d.usergroups_rolesID')
            ->where('d.usersID IN (' . $nestedQuery . ')')
            ->where('a.id NOT IN  (1,2)')
            ->group('a.id, a.title, a.lft, a.rgt')
            ->order('a.lft ASC');

        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }

        for ($i = 0, $n = count($options); $i < $n; $i++)
        {
            $groups[] = JHtml::_('select.option', $options[$i]->id, str_repeat('- ', $options[$i]->level) . $options[$i]->title);
        }

        return $groups;
    }

    /**
     * Return all existing profiles as select field
     *
     * @return array
     */
    public static function getProfiles()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('id, name')
            ->from('#__thm_groups_profile');

        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }

        foreach ($options as $key => $option)
        {
            $profiles[] = JHtml::_('select.option', $option->id, $option->name);
        }

        return $profiles;
    }
}
