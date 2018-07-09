<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for template editing
 */
class THM_GroupsHelperTemplate
{
    /**
     * Returns array with attributes and assigned parameters
     *
     * @param   array $allAttributes      All attributes in THM Groups
     * @param   array $templateAttributes Attributes of a certain template
     *
     * @return  mixed  array on success, false otherwise
     */
    public static function assignParametersToAttributes($allAttributes, $templateAttributes)
    {
        $app = JFactory::getApplication();
        if (empty($allAttributes)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ATTRIBUTE_ERROR_NO_ATTRIBUTES'), 'error');

            return false;
        }

        if (empty($templateAttributes)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NO_ATTRIBUTES'), 'error');

            return false;
        }

        foreach ($allAttributes as &$attribute) {
            foreach ($templateAttributes as $templateAttribute) {
                if ($attribute['id'] == $templateAttribute['attributeID']) {
                    $attribute['published'] = $templateAttribute['published'];
                    $attribute['ordering']  = $templateAttribute['ordering'];
                    $attribute['params']    = $templateAttribute['params'];
                }
            }
        }

        return $allAttributes;
    }

    /**
     * Retrieves the name of the template with a given ID
     *
     * @param   int $templateID the id of the template
     *
     * @return  string the name of the template
     */
    public static function getName($templateID)
    {
        if (empty($templateID)) {
            return '';
        }

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('template.name');
        $query->from('#__thm_groups_templates AS template');
        $query->where("id = '$templateID'");
        $dbo->setQuery($query);

        try {
            $templateName = $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return '';
        }

        return empty($templateName) ? '' : $templateName;
    }

    /**
     * Returns all attributes with parameters of a template by its ID
     *
     * @param   int $templateID Template ID
     *
     * @return  mixed  array on success, false otherwise
     */
    public static function getTemplateAttributes($templateID)
    {
        $app = JFactory::getApplication();

        if (empty($templateID)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NULL'), 'error');

            return false;
        }

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select("*")->from('#__thm_groups_template_attributes')->where("templateID = $templateID");
        $dbo->setQuery($query);

        try {
            $attributes = $dbo->loadAssocList();
        } catch (Exception $exception) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_MANAGER_ERROR_GET_TEMPLATE_ATTRIBUTES'), 'error');

            return false;
        }

        return empty($attributes) ? [] : $attributes;
    }
}
