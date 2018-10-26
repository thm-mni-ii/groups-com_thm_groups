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
class JFormFieldGenericList extends JFormFieldList
{
    /**
     * Type
     *
     * @var    String
     */
    public $type = 'genericlist';

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

                $options[$resource['text']] = JHtml::_('select.option', $resource['value'], $resource['text']);
            }
            $this->setValueParameters($options);

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
        $textColumn  = $this->getAttribute('textColumn');
        $textColumns = explode(',', $textColumn);

        $localized = $this->getAttribute('localized', false);
        if ($localized) {
            require_once HELPERS . 'language.php';
            $tag = THM_GroupsHelperLanguage::getShortTag();
            foreach ($textColumns as $key => $value) {
                $textColumns[$key] = $value . '_' . $tag;
            }
        }
        $glue = $this->getAttribute('glue');

        if (count($textColumns) === 1 or empty($glue)) {
            return $textColumns[0];
        }

        return '( ' . $query->concatenate($textColumns, $glue) . ' )';
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
        $tableParameters = $this->getAttribute('table');
        $tables          = explode(',', $tableParameters);

        $query->from("#__{$tables[0]}");
        $count = count($tables);
        if ($count === 1) {
            return;
        }

        for ($index = 1; $index < $count; $index++) {
            $query->innerjoin("#__{$tables[$index]}");
        }
    }

    /**
     * Sets value oriented parameters from component settings
     *
     * @param   array &$options the input options
     *
     * @return  void  sets option values
     * @throws Exception
     */
    private function setValueParameters(&$options)
    {
        $valueParameter = $this->getAttribute('valueParameter', '');
        if ($valueParameter === '') {
            return;
        }
        $valueParameters     = explode(',', $valueParameter);
        $componentParameters = JComponentHelper::getParams(JFactory::getApplication()->input->get('option'));
        foreach ($valueParameters as $parameter) {
            $componentParameter = $componentParameters->get($parameter);
            if (empty($componentParameter)) {
                continue;
            }
            $options[$componentParameter] = JHtml::_('select.option', $componentParameter, $componentParameter);
        }
        ksort($options);
    }
}
