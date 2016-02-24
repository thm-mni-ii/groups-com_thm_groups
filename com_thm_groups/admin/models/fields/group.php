<?php
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldGroup extends JFormFieldList
{

    protected $type = 'Group';

    /**
     * Cached array of the category items.
     *
     * @var    array
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

        $query
            ->select('a.id, a.title')
            ->from('#__usergroups AS a')
            ->innerJoin('#__thm_groups_usergroups_roles AS b ON a.id = b.usergroupsID')
            ->group('a.title');

        $db->setQuery($query);
        $db->execute();

        return $db->loadAssocList();
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

        if (!isset(static::$options[$hash]))
        {
            static::$options[$hash] = parent::getOptions();
            $options = array();

            $arrayOfGroups = $this->getGroupsFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('JALL'));
            foreach ($arrayOfGroups as $key => $value) :
                $options[] = JHTML::_('select.option', $value['id'], $value['title']);
            endforeach;

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}