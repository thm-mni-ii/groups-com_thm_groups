<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

/**
 * Class loads a list of fields for selection
 */
class JFormFieldFieldList extends JFormFieldList
{
    /**
     * Type
     *
     * @var    String
     */
    public $type = 'fieldlist';

    /**
     * Method to get the field options for attribute types
     *
     * @return  array  The field option objects.
     * @throws Exception
     */
    protected function getOptions()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('DISTINCT id AS value, field AS text')->from('#__thm_groups_fields')->order("text ASC");

        // Suppress the inclusion of the file field type for new attribute types
        if (empty(JFactory::getApplication()->input->getInt('id'))) {
            $query->where('id != 4');
        }

        $dbo->setQuery($query);

        try {
            $fields = $dbo->loadAssocList();
        } catch (Exception $exc) {
            return parent::getOptions();
        }

        $options   = [];
        foreach ($fields as $field) {

            $options[$field['text']] = JHtml::_('select.option', $field['value'], $field['text']);
        }

        return array_merge(parent::getOptions(), $options);
    }
}
