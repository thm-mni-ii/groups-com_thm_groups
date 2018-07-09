<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/field_types.php';

/**
 * Class provides method for extra options of static types
 */
class THM_GroupsModelField_Type_Ajax extends JModelForm
{
    /**
     * Returns field type name of an abstract attribute by its id
     *
     * @return string on success, else false
     * @throws Exception
     */
    public function getNameByDynamicID()
    {
        $input      = JFactory::getApplication()->input;
        $abstractID = $input->getInt('abstractID', 0);

        if (empty($abstractID)) {
            return '';
        }

        $query = $this->_db->getQuery(true);

        $query
            ->select('ft.name')
            ->from('#__thm_groups_field_types AS ft')
            ->innerJoin('#__thm_groups_abstract_attributes AS aa ON aa.field_typeID = ft.id')
            ->where("aa.id = $abstractID");
        $this->_db->setQuery($query);

        try {
            return $this->_db->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception, 'error');

            return false;
        }
    }

    /**
     * Returns static type's name by its ID
     *
     * @return string on success, else false
     * @throws Exception
     */
    public function getNameByID()
    {
        $input       = JFactory::getApplication()->input;
        $fieldTypeID = $input->getInt('fieldTypeID', 0);

        if (empty($fieldTypeID)) {
            return '';
        }

        $query = $this->_db->getQuery(true);
        $query->select('name')->from('#__thm_groups_field_types')->where("id = '$fieldTypeID'");
        $this->_db->setQuery($query);

        try {
            return $this->_db->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception, 'error');

            return false;
        }
    }

    /**
     * Returns json options from #__thm_groups_attributes
     *
     * @param   int $attributeID attribute ID
     *
     * @return string in json format on success, else false
     * @throws Exception
     */
    public function getAttributeOptionsByID($attributeID)
    {
        $query = $this->_db->getQuery(true);

        $query
            ->select('options')
            ->from('#__thm_groups_attributes')
            ->where("id = '$attributeID'");
        $this->_db->setQuery($query);

        try {
            return $this->_db->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception, 'error');

            return false;
        }
    }

    /**
     * Returns json options from #__thm_groups_abstract_attributes
     *
     * @param   int $abstractID the abstract attribute ID
     *
     * @return string in json format on success, else false
     * @throws Exception
     */
    public function getDynTypeOptionsByID($abstractID)
    {
        $query = $this->_db->getQuery(true);

        $query
            ->select('options')
            ->from('#__thm_groups_abstract_attributes')
            ->where("id = $abstractID");
        $this->_db->setQuery($query);

        try {
            return $this->_db->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception, 'error');

            return false;
        }
    }

    /**
     * Abstract method for getting the form from the model.
     *
     * @param   array   $data     Data for the form.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   12.2
     */
    public function getForm($data = [], $loadData = true)
    {
        $option = $this->get('option');
        $name   = $this->get('name');

        return $this->loadForm("$option.$name", $name, ['control' => 'jform', 'load_data' => $loadData]);
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  array    The default data is an empty array.
     */
    public function loadFormData()
    {
        $input          = JFactory::getApplication()->input;
        $dynTypeOptions = $this->getDynTypeOptionsByID($input->getInt('abstractID', 0));

        if (!empty(json_decode($dynTypeOptions))) {
            $dynTypeOptions = json_decode($dynTypeOptions);
        } else {
            $fieldTypeID = $input->getInt('fieldTypeID', 0);

            // New abstract attribute -> get default field type parameters
            $dynTypeOptions = THM_GroupsHelperField_Types::getOption($fieldTypeID);
        }

        $data        = [];
        $attrOptions = json_decode($this->getAttributeOptionsByID($input->getInt('attributeID', 0)));

        // Attribute
        if (!empty($attrOptions)) {
            foreach ($attrOptions as $key => $value) {
                if ($key !== 'required') {
                    $data[$key] = !empty($value) ? $value : $dynTypeOptions->$key;
                }
            }

            return $data;
        }

        // Dynamic type
        foreach ($dynTypeOptions as $key => $value) {
            if ($key !== 'required') {
                $data[$key] = !empty($value) ? $value : '';
            }
        }

        return $data;
    }
}
