<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once 'fields.php';

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperAttribute_Types
{
    /**
     * Configures the form for the relevant attribute type
     *
     * @param int    $typeID the id of the attribute type to be configured to
     * @param object &$form  the form being modified
     * @param bool   $inTypeForm whether or not the function was called from the type form context
     *
     * @return void configures the form for the relevant field
     * @throws Exception
     */
    public static function configureForm($typeID, &$form, $inTypeForm = false)
    {
        $fieldID = self::getFieldID($typeID);
        THM_GroupsHelperFields::configureForm($fieldID, $form);

        if ($inTypeForm) {
            $options = self::getOptions($typeID);
            foreach ($options as $option => $value) {
                $form->setValue($option, null, $value);
            }

            // Predefined types
            if (in_array($typeID, [TEXT, EDITOR, URL, IMAGE, DATE_EU, EMAIL, TELEPHONE, NAME, SUPPLEMENT])) {

                // The name
                $form->setFieldAttribute('type', 'readonly', 'true');
                foreach ($options as $option => $value) {
                    $form->setFieldAttribute($option, 'readonly', 'true');
                }
            }

            // Not editable once set
            if ($typeID) {
                $form->setFieldAttribute('fieldID', 'readonly', 'true');
            }
        }

        if ($typeID == IMAGE) {
            $form->setFieldAttribute('showIcon', 'readonly', 'true');
            $form->setFieldAttribute('showLabel', 'readonly', 'true');
            $form->setFieldAttribute('accept', 'readonly', 'true');
        }

    }

    /**
     * Retrieves the ID of the field type associated with the abstract attribute
     *
     * @param int $typeID the id of the abstract attribute
     *
     * @return int the id of the field type associated with the abstract attribute
     *
     * @throws Exception
     */
    public static function getFieldID($typeID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('fieldID')
            ->from('#__thm_groups_attribute_types')
            ->where('id = ' . (int)$typeID);
        $dbo->setQuery($query);

        try {
            $result = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($result) ? 0 : $result;
    }

    /**
     * Returns specific field type options mapped with attribute type data and optionally mapped with form data
     *
     * @param int   $typeID  the attribute type id
     * @param array $options the options to be mapped from user input
     *
     * @return  array the field options set with form values if available
     * @throws Exception
     */
    public static function getOptions($typeID, $options = null)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('options')->from('#__thm_groups_attribute_types')->where("id = $typeID");
        $dbo->setQuery($query);

        try {
            $atOptions = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        $atOptions = json_decode($atOptions, true);
        if (empty($atOptions)) {
            $atOptions = [];
        }

        // Accepts all data, later restricted by the field configuration
        if ($options) {
            foreach ($options as $property => $value) {
                if ($value !== '') {
                    $atOptions[$property] = $value;
                }
            }
        }

        return THM_GroupsHelperFields::getOptions(self::getFieldID($typeID), $atOptions);
    }
}
