<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperBatch
{
    /**
     * Return all existing roles as select field
     *
     * @return  array  an array of options for drop-down list
     * @throws Exception
     */
    public static function getRoles()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true)
            ->select('id AS value, name AS text')
            ->from('#__thm_groups_roles')
            ->order('id');
        $dbo->setQuery($query);

        try {
            $options = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        for ($i = 0, $n = count($options); $i < $n; $i++) {
            $roles[] = JHtml::_('select.option', $options[$i]->value, $options[$i]->text);
        }

        return $roles;
    }

    /**
     * Returns groups as a select field
     * It shows only groups with users in it, because this select field
     * will be used only for filtering in backend-user-manager
     *
     * @return array
     * @throws Exception
     */
    public static function getGroupOptions()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        // TODO: Explain the logic behind this
        $select = 'ug.id, ug.title, COUNT(DISTINCT ugTemp.id) AS level';
        $query->select($select);
        $query->from('#__usergroups AS ug');
        $query->leftJoin('#__usergroups AS ugTemp ON ug.lft > ugTemp.lft AND ug.rgt < ugTemp.rgt');
        $query->group('ug.id, ug.title, ug.lft, ug.rgt');
        $query->order('ug.lft ASC');

        $dbo->setQuery($query);

        try {
            $groups = $dbo->loadAssocList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        $standardGroupIDS = [1, 2, 3, 4, 5, 6, 7, 8];
        $options          = [];
        foreach ($groups as $group) {
            $label          = str_repeat('- ', $group['level']) . $group['title'];
            $attribs        = in_array($group['id'], $standardGroupIDS) ? ['disable' => true] : [];
            $options[]      = JHtml::_('select.option', $group['id'], $label, $attribs);
        }

        return $options;
    }
}
