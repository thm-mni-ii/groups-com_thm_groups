<?php
/**
 * @package     com_thm_groups
 * @subpackate com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

/**
 * Class loads a list of templates
 */
class JFormFieldTemplate extends JFormFieldList
{
    public $type = 'template';

    /**
     * Retrieves the saved profile templates and adds context dependent meta-options.
     *
     * @return  array  the template options
     */
    protected function getOptions()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select("DISTINCT template.id AS value, template.name AS text");
        $query->from('#__thm_groups_templates AS template');

        $associated = (bool)$this->getAttribute('associated', false);

        if ($associated) {
            $query->innerJoin('#__thm_groups_template_associations AS tempAssoc ON tempAssoc.templateID = template.id');
        }

        $query->order("text ASC");
        $dbo->setQuery($query);

        try {
            $templates = $dbo->loadAssocList('value');
        } catch (Exception $exc) {
            return parent::getOptions();
        }

        if (empty($templates)) {
            return [];
        }

        $plugin = (bool)$this->getAttribute('plugin', false);

        if ($associated) {
            if ($plugin) {
                $noTemplates = ['value' => '', 'text' => JText::_('COM_THM_GROUPS_MODULE_DEFAULT')];
                array_unshift($templates, $noTemplates);
            } else {
                $noTemplates = ['value' => -1, 'text' => JText::_('JNONE')];
                array_unshift($templates, $noTemplates);

                $allTemplates = ['value' => '', 'text' => JText::_('JALL')];
                array_unshift($templates, $allTemplates);
            }
        }

        return $templates;
    }
}
