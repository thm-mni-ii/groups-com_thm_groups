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
class JFormFieldDisjunctList extends JFormFieldList
{
    /**
     * Type
     *
     * @var    String
     */
    public $type = 'disjunctlist';

    /**
     * Method to get the field options for category
     * Use the extension attribute in a form to specify the.specific extension for
     * which categories should be displayed.
     * Use the show_root attribute to specify whether to show the global category root in the list.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $valueColumn = $this->getAttribute('valueColumn');
        $textColumn  = $this->resolveText($query);

        $query->select("DISTINCT $valueColumn AS value, $textColumn AS text");
        $this->setFrom($query);
        $this->setDisjuncture($query);
        $query->order("text ASC");
        $dbo->setQuery($query);

        try {
            $resources = $dbo->loadAssocList();
            $options   = [];
            foreach ($resources as $resource) {
                // Removes glue from the end of entries
                $glue = $this->getAttribute('glue', '');
                if (!empty($glue)) {
                    $glueSize = strlen($glue);
                    $textSize = strlen($resource['text']);
                    if (strpos($resource['text'], $glue) == $textSize - $glueSize) {
                        $resource['text'] = str_replace($glue, '', $resource['text']);
                    }
                }

                $options[] = JHtml::_('select.option', $resource['value'], $resource['text']);
            }

            return array_merge(parent::getOptions(), $options);
        } catch (Exception $exc) {
            return parent::getOptions();
        }
    }

    /**
     * Resolves the textColumns for concatenated values
     *
     * @param   object &$query the query object
     *
     * @return  string  the string to use for text selection
     */
    private function resolveText(&$query)
    {
        $textColumn = $this->getAttribute('textColumn');
        $glue       = $this->getAttribute('glue');

        $textColumns = explode(',', $textColumn);
        if (count($textColumns) === 1 or empty($glue)) {
            return $textColumn;
        }

        return '( ' . $query->concatenate($textColumns, $glue) . ' )';
    }

    /**
     * Resolves the textColumns for concatenated values
     *
     * @param   object &$query the query object
     *
     * @return  void  sets query object values
     */
    private function setFrom(&$query)
    {
        $tableParameter = $this->getAttribute('table');
        $aliasParameter = $this->getAttribute('alias');
        $tables         = explode(',', $tableParameter);
        $aliases        = explode(',', $aliasParameter);
        $count          = count($tables);
        if ($count === 1 or $count != count($aliases)) {
            $query->from("#__$tableParameter");

            return;
        }

        $query->from("#__{$tables[0]} AS {$aliases[0]}");
        for ($index = 1; $index < $count; $index++) {
            $query->innerjoin("#__{$tables[$index]} AS {$aliases[$index]}");
        }
    }

    /**
     * Sets the disjunct conditions for the query
     *
     * @param   object &$query the query object
     *
     * @return  void  sets query object values
     */
    private function setDisjuncture(&$query)
    {
        $notInColumn   = $this->getAttribute('notInColumn');
        $disjunctValue = $this->getAttribute('disjunctValue');
        $disjunctTable = $this->getAttribute('disjunctTable');

        if (empty($disjunctValue) or empty($disjunctTable)) {
            return;
        }

        $subQuery = JFactory::getDbo()->getQuery(true);
        $subQuery->select("$disjunctValue")->from("#__$disjunctTable");
        $query->where("$notInColumn NOT IN ( " . (string)$subQuery . " )");
    }
}
