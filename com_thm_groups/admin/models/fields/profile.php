<?php
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldProfile extends JFormFieldList
{

    protected $type = 'profile';

    /**
     * Cached array of the category items.
     *
     * @var    array
     */
    protected static $options = array();

    /**
     * returns a list of profiles
     *
     * @return  Array
     */
    public function getProfilesFromDB()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id, a.name')
            ->from('#__thm_groups_profile AS a')
            ->innerJoin('#__thm_groups_profile_usergroups AS b ON b.profileID = a.id')
            ->group('a.name');

        $db->setQuery($query);
        try
        {
            $return = $db->loadObjectList();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        return $return;
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

            $arrayOfProfiles = $this->getProfilesFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('JALL'));
            foreach ($arrayOfProfiles as $key => $value)
            {
                $options[] = JHTML::_('select.option', $value->id, $value->name);
            }

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}