<?php
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldModerator extends JFormFieldList
{

    protected $type = 'moderator';

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
    public function getModeratorsFromDB()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('b.id, b.name')
            ->from('#__thm_groups_users_usergroups_moderator AS a')
            ->innerJoin('#__users AS b ON a.usersID = b.id')
            ->group('b.id');

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
            $options                = array();

            $arrayOfModerators = $this->getModeratorsFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('JALL'));
            foreach ($arrayOfModerators as $key => $value) :
                $options[] = JHTML::_('select.option', $value['id'], $value['name']);
            endforeach;

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}