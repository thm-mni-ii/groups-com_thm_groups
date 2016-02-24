<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        attribute edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelAttribute_Edit extends JModelAdmin
{

    /**
     * Gets the static type from DB returns it as object
     *
     * @param   integer  $staticTid  ID of desired static type
     *
     * @return mixed
     */
    public function  getStaticType($staticTid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
              ->from('#__thm_groups_static_type')
              ->where('id = ' . (int) $staticTid);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * returns one attribute item or an dummy object
     *
     * @return mixed|stdClass
     */
    public function getStructureItem()
    {
        $app = JFactory::getApplication();
        $cid = $app->input->get('cid', array(), 'array');
        $id = (empty($cid)) ? $app->input->get->get('id') : $cid[0];

        // TODO Ilja, Delete this bullshit! Comment from Ilja :)
        // Gets all from attribute and the options from dynamic type
        if ($id != 0)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select(array('#__thm_groups_attribute.*', '#__thm_groups_dynamic_type.options AS dynoptions'));
            $query->from($db->qn(array('#__thm_groups_attribute','#__thm_groups_dynamic_type')));
            $query->where('#__thm_groups_attribute.id = '
                . (int) $id . ' AND #__thm_groups_attribute.dynamic_typeID = #__thm_groups_dynamic_type.id ');
            $db->setQuery($query);

            return $db->loadObject();
        }
        else
        {
            // Bullshit part
            $temp = new stdClass;
            $temp->id = 0;
            $temp->dynamic_typeID = 0;
            $temp->options = null;
            return $temp;
        }
    }

    /**
     * Method to get the form
     *
     * @param   Array    $data      Data         (default: Array)
     * @param   Boolean  $loadData  Load data  (default: true)
     *
     * @return  A Form object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_thm_groups.attribute_edit', 'attribute_edit',
            array('control' => 'jform', 'load_data' => $loadData)
        );
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Returns table object
     *
     * @param   string  $type    type
     * @param   string  $prefix  prefix
     * @param   array   $config  config
     *
     * @return  JTable|mixed
     */
    public function getTable($type = 'Attribute', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Returns a list of dynamic types
     *
     * @return  Array
     */
    public function getDynamicTypes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id, name')
            ->from('#__thm_groups_dynamic_type');
        $db->setQuery($query);
        $db->execute();

        return $db->loadAssocList();
    }

    /**
     * Returns a single dynamicType object
     *
     * @param   integer  $id  ID of dynamic type
     *
     * @return  Object
     */
    public function getDynamicType($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
              ->from('#__thm_groups_dynamic_type')
              ->where('id = ' . (int) $id);
        $db->setQuery($query);
        $db->execute();
        return $db->loadObject();
    }

    /**
     * @param $dynTypeID
     * @param $attrID
     * @param $attOpt
     * @return mixed
     */
    public function getFieldExtras($dynTypeID, $attrID, $attOpt)
    {
        $dynType = $this->getDynamicType($dynTypeID);
        $staticType = $this->getStaticType($dynType->static_typeID);
        $dynOptions = json_decode($dynType->options);
        $attOpt = json_decode($attOpt);

        JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

        $attributeFields = JFormHelper::loadFieldType('attributefield', false);
        $options = array(
            "staticType"  => $staticType,
            "attOpt"      => $attOpt,
            "dynOptions"  => $dynOptions,
            "attrID"      => $attrID
        );
        $attributeFields->__set('options', $options );
        return $attributeFields->getInput();
    }

    /**
     * Generate Select Field for static types
     *
     * @param   Int  $dynamic_typeID  dynamic type id, check static_TypeID for current dynamic type
     *
     * @return  select field
     */
    public function getDynamicTypesSelectField($dynamic_typeID)
    {
        $options = array();

        $selected = $dynamic_typeID;

        $arrayOfStaticTypes = $this->getDynamicTypes();

        // Convert array to options
        foreach ($arrayOfStaticTypes as $key => $value) :
            $options[] = JHTML::_('select.option', $value['id'], $value['name']);
        endforeach;

        // 'onchange' => js-function that's executed when selection on selectfield has made
        $settings = array(
            'id'            => 'staticTypesField',
            'option.key'    => 'value',
            'option.value'  => 'text',
            'onchange'      => 'jQf.fn.getFieldExtras()',
            'list.select'   => $selected
        );

        // Generates selectfields:
        $selectFieldStaticTypes = JHtmlSelect::genericlist(
            $options,
            'dynamicType', // Name of select field
            $settings,     // array of settings
            'value',       // Standard
            'text',        // variables
            $selected      // Selected Index //do not delete
        );

        return $selectFieldStaticTypes;
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        $ids = $app->input->get('cid', array(), 'array');

        // Input->get because id is in url
        $id = (empty($ids)) ? $app->input->get->get('id') : $ids[0];
        return $this->getItem($id);
    }

    /**
     * validates form
     *
     * @return bool
     */
    public function validateForm()
    {
        $app = JFactory::getApplication();
        $data = $app->input->post->get('jform', array(), 'array');

        // TODO make validation better
        if (empty($data['name']))
        {
            return false;
        }

        return true;
    }
}