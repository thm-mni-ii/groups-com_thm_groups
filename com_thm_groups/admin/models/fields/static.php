<?php
defined('_JEXEC') or die('Restricted access');
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldStatic extends JFormFieldList
{

    protected $type = 'Static';

    /**
     * Cached array of the category items.
     *
     * @var    array
     * @since  3.2
     */
    protected static $options = array();

    /**
     * returns a list of static types
     *
     * @return  Array
     */
    public function getStaticTypesFromDB()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id, name')
            ->from('#__thm_groups_static_type');
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

            $arrayOfStaticTypes = $this->getStaticTypesFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '*', JText::_('JALL'));
            foreach ($arrayOfStaticTypes as $key => $value) :
                $options[] = JHTML::_('select.option', $value['id'], $value['name']);
            endforeach;

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}