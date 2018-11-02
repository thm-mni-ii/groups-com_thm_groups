<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';


/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelTemplate_Edit extends THM_GroupsModelEdit
{
    /**
     * Returns all attributes with parameters of a template
     *
     * @return array the attribute information, empty if nothing could be found or an error occurred
     * @throws Exception
     */
    public static function getAttributes()
    {
        $app        = JFactory::getApplication();
        $templateID = $app->input->getInt('id', 0);
        $dbo        = JFactory::getDbo();

        $attributeQuery = $dbo->getQuery(true);

        $attributeQuery->select('a.id, a.label, a.published, a.showIcon, a.showLabel, at.id AS typeID');
        if (empty($templateID)) {
            $attributeQuery->select('a.ordering');
        }
        $attributeQuery->from('#__thm_groups_attributes AS a')
            ->innerJoin('#__thm_groups_attribute_types AS at ON at.id = a.typeID')
            ->order('a.ordering');
        $dbo->setQuery($attributeQuery);

        try {
            $rawAttributes = $dbo->loadAssocList('id');
        } catch (Exception $exc) {
            $app->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        if (empty($templateID)) {
            return empty($rawAttributes) ? [] : $rawAttributes;
        }

        $templateAttributes  = [];
        $maxOrdering         = 0;
        $missingAttributeIDs = [];

        $taQuery = $dbo->getQuery(true);
        $taQuery->select('ta.ordering AS ordering, ta.published AS published, ta.showIcon AS showIcon, ta.showLabel AS showLabel')
            ->from('#__thm_groups_template_attributes AS ta')
            ->innerJoin('#__thm_groups_attributes AS a ON a.id = ta.attributeID');

        foreach ($rawAttributes as $attribute) {
            $taQuery->clear('where')->where("a.id = '{$attribute['id']}'")->where("ta.templateID = '$templateID'");
            $dbo->setQuery($taQuery);

            try {
                $templateAttribute = $dbo->loadAssoc();
            } catch (Exception $exc) {
                $app->enqueueMessage($exc->getMessage(), 'error');

                return [];
            }

            if (empty($templateAttribute)) {
                $missingAttributeIDs[] = $attribute['id'];
            } else {
                $templateAttribute['id']     = $attribute['id'];
                $templateAttribute['label']  = $attribute['label'];
                $templateAttribute['typeID'] = $attribute['typeID'];

                $templateAttributes[$templateAttribute['ordering']] = $templateAttribute;

                $maxOrdering = $maxOrdering > $templateAttribute['ordering'] ? $maxOrdering : $templateAttribute['ordering'];
            }
        }

        foreach ($missingAttributeIDs as $missingAttributeID) {
            $maxOrdering++;
            $rawAttribute                     = $rawAttributes[$missingAttributeID];
            $rawAttribute['ordering']         = $maxOrdering;
            $rawAttribute['published']        = false;
            $templateAttributes[$maxOrdering] = $rawAttribute;
        }

        ksort($templateAttributes);

        return empty($templateAttributes) ? [] : $templateAttributes;
    }
}
