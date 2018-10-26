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
require_once HELPERS . 'language.php';

/**
 * Class loads a list of fields for selection
 */
class JFormFieldLocalizedList extends JFormFieldList
{
    /**
     * Type
     *
     * @var    String
     */
    public $type = 'localizedlist';

    /**
     * Method to get the field options for category
     * Use the extension attribute in a form to specify the.specific extension for
     * which categories should be displayed.
     * Use the show_root attribute to specify whether to show the global category root in the list.
     *
     * @return  array  The field option objects.
     * @throws Exception
     */
    protected function getOptions()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $tag         = THM_GroupsHelperLanguage::getShortTag();
        $valueColumn = $this->getAttribute('valueColumn') . "_$tag";
        $textColumn  = $this->getAttribute('textColumn') . "_$tag";

        $query->select("DISTINCT $valueColumn AS value, $textColumn AS text");
        $this->setFrom($query);
        $query->order("text ASC");
        $dbo->setQuery($query);

        try {
            $resources = $dbo->loadAssocList();
            $options   = [];
            foreach ($resources as $resource) {
                $options[] = JHtml::_('select.option', $resource['value'], $resource['text']);
            }

            return array_merge(parent::getOptions(), $options);
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return parent::getOptions();
        }
    }

    /**
     * Resolves the textColumns for concatenated values
     *
     * @param   object &$query the query object
     *
     * @return  void modifies the query object
     */
    private function setFrom(&$query)
    {
        $tableParameter = $this->getAttribute('table');
        $tables         = explode(',', $tableParameter);
        $count          = count($tables);
        if ($count === 1) {
            $query->from("#__$tableParameter");

            return;
        }

        $query->from("#__{$tables[0]}");
        for ($index = 1; $index < $count; $index++) {
            $query->innerjoin("#__{$tables[$index]}");
        }
    }
}
