<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldUsermanagergroup
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldProfilemanagerGroup extends JFormFieldList
{

    protected $type = 'profilemanagergroup';

    /**
     *
     * @var    array
     */
    protected static $options = array();

    /**
     * Retrieves a list of non-empty user groups associated with roles. (Public and registered are ignored.)
     *
     * @return  Array
     */
    public function getGroupsFromDB()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $nestedQuery = $dbo->getQuery(true);
        $nestedQuery
            ->select('id')
            ->from('#__thm_groups_profiles');


        $query
            ->select('gr.id, gr.title')
            ->from('#__usergroups AS gr')
            ->innerJoin('#__thm_groups_role_associations AS roleAssoc ON gr.id = roleAssoc.usergroupsID')
            ->innerJoin('#__thm_groups_associations AS assoc ON roleAssoc.id = assoc.role_assocID')
            ->where('assoc.profileID IN (' . $nestedQuery . ')')
            ->where('gr.id NOT IN  (1,2)')
            ->group('gr.id')
            ->order('gr.title ASC');

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

            $arrayOfGroups = $this->getGroupsFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('COM_THM_GROUPS_FILTER_BY_GROUP'));
            foreach ($arrayOfGroups as $key => $value) :
                $options[] = JHTML::_('select.option', $value['id'], $value['title']);
            endforeach;

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}
