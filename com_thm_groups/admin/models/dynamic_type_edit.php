<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT . '/assets/helpers/static_type_options_helper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelDynamic_Type_Edit extends JModelAdmin
{

    /**
     * returns one dynamic item
     *
     * @return mixed|stdClass
     */
    public function getDynamicTypeItem()
    {
        $app = JFactory::getApplication();
        $cid = $app->input->get('cid', array(), 'array');
        $id = (empty($cid)) ? $app->input->get->get('id') : $cid[0];

        // TODO Ilja, Delete this bullshit! Comment from Ilja :)
        if ($id != 0)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            $query->select('*');
            $query->from($db->qn('#__thm_groups_dynamic_type'));
            $query->where('id = ' . (int) $id);
            $db->setQuery($query);

            return $db->loadObject();
        }
        else
        {
            // Bullshit part
            $temp = new stdClass;
            $temp->id = 0;
            $temp->static_typeID = 0;
            return $temp;
        }
    }

    /**
     * @param $selected
     * @param $isActType
     * @return mixed
     */
    public function getFieldOptions($selected, $isActType)
    {
        $dynAttribute = $this->getDynamicTypeItem();

        JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

        $dynFields = JFormHelper::loadFieldType('DynField', false);
        $options = array(
            "selected"  => $selected,
            "isActType" => $isActType,
            "dynAttribute" => $dynAttribute
        );
        $dynFields->__set('options', $options );
        return $dynFields->getInput();
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
        $form = $this->loadForm('com_thm_groups.dynamic_type_edit', 'dynamic_type_edit', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * returns table object
     *
     * @param   string  $type    type
     * @param   string  $prefix  prefix
     * @param   array   $config  config
     *
     * @return  JTable|mixed
     */
    public function getTable($type = 'Dynamic_Type', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * returns a list of static types, when $reduce is true reduced set of
     * options will be selected.
     *
     * @param   boolean  $reduce  Reduced options?
     *
     * @return  Array
     */
    public function getStaticTypes($reduce)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        if ($reduce == 'true')
        {
            $query->select('id, name')
                ->from('#__thm_groups_static_type')
                ->where('name= "TEXT" OR name= "TEXTFIELD"');
        }
        else
        {
            $query->select('id, name')
                ->from('#__thm_groups_static_type');
        }

        $db->setQuery($query);
        $db->execute();

        return $db->loadAssocList();
    }

    /**
     * Generates the regex if staticType is TEXT or LINK
     *
     * @param   String  $name  Name of staticType
     *
     * @return array|null
     */
    public function getRegex($name)
    {
        $regexOptions = array();

        if ($name == 'TEXT')
        {
            // The '\\\' -parts before an '\' are inserted because of string to regexobject conversion in javascript
            array_push(
                $regexOptions,
                JHtml::_('select.option', '', 'Other'),
                JHtml::_('select.option', '^([a-zA-ZäöüÄÖÜ])*[a-zA-ZäöüÄÖÜ]$', 'Only letters'),
                JHtml::_('select.option', '^[0-9]*$', 'Only numbers'),
                JHtml::_('select.option', '^[0-9a-zA-ZäöüÄÖÜ]+$', 'Letters and numbers'),
                JHtml::_('select.option', '^[0-9]{4,5}$', 'PLZ'),
                JHtml::_('select.option', '^(\\\\+49 )?(\\\\([0-9]{1,5}\\\\)|[0-9]{0,5}|\\\\(0\\\\)[0-9]{3,4})(\\\\/| )?([0-9]+\\\\ ?)*(\\\\ \\\\+[0-9]{1,3})?$', 'Phone'),
                JHtml::_('select.option', '^([0-9a-zA-Z\\\\.]+)@(([\\\\w]|\\\\.\\\\w)+)\\\\.(\\\\w+)$', 'E-Mail'),
                JHtml::_('select.option', '^[A-E]{1}([0-9]{2}\\\\.|\\\\.)[0-9]{1,2}\\\\.([0-9]{2}[a-z]{0,1})$', 'Room'),
                JHtml::_('select.option', '^(\\\\w+\\\\.?)+$', 'Title')
            );
            return $regexOptions;
        }
        elseif ($name == 'LINK')
        {
            array_push(
                $regexOptions,
                JHtml::_('select.option', 'Other', 'Other'),
                JHtml::_('select.option', '^(https?:\\\\/\\\\/)?([\\\\da-z\\\\.-]+)\\\\.([a-z\\\\.]{2,6})([\\\\/\\\\w \\\\.-]*)*\\\\/?$', 'URL'),
                JHtml::_('select.option', '(http|ftp|https:\\\\/\\\\/){0,1}[\\\\w\\\\-_]+(\\\\.[\\\\w\\\\-_]+)+([\\\\w\\\\-\\\\.,@?^=%&amp;:/~\\\\+#]*[\\\\w\\\\-\\\\@?^=%&amp;/~\\\\+#])?', 'Hyperlink with special chars')
                );
            return $regexOptions;
        }
        else
        {
            return null;
        }
    }

    /**
     * Generates the regex selection field
     *
     * @param   Integer  $static_typeID  ID of staticType
     *
     * @return null
     */
    public function getRegexOptions($static_typeID)
    {
        $selected = $static_typeID;

        $staticType = $this->getStaticType($selected);

        /** TODO: When user clicks on +new
         * in dynamic type manager an error
         * occurs because static type id is
         * "0", staticType == null is only
         * a workaround.
         * */
        if ($staticType == null)
        {
            $options = $this->getRegex('TEXT');
        }
        else
        {
            $options = $this->getRegex($staticType->name);
        }


        if ($options != null)
        {
            $settings = array(
                'id' => 'jform_regex_select',
                'option.key' => 'value',
                'option.value' => 'text',
                'onchange' => 'getRegex();'
            );

            $selectFieldRegex = JHtmlSelect::genericlist(
                $options,
                'jform_regex_select',  // Name of select field
                $settings,
                'value',       // Standard
                'text',        // variables
                $selected
            );
            return $selectFieldRegex;
        }
        else
        {
            return null;
        }

    }

    /**
     * Generate Select Field for static types
     *
     * @param   Int  $static_typeID  dynamic type id, check static_TypeID for current dynamic type
     *
     * @return  select field
     */
    public function getStaticTypesSelectField($static_typeID)
    {
        $options = array();

        $selected = $static_typeID;

        // Check if it is not a new type
        if ($selected != 0)
        {
            $type = $this->getStaticType($selected);

            // When type is TEXT or TEXTFIELD limited options are available
            if (($type->name == 'TEXT') || ($type->name == 'TEXTFIELD'))
            {
                $arrayOfStaticTypes = $this->getStaticTypes('true');
            }
            else
            {
                $arrayOfStaticTypes = $this->getStaticTypes('false');
            }
        }
        else
        {
            $arrayOfStaticTypes = $this->getStaticTypes('false');
        }

        // Convert array to options
        foreach($arrayOfStaticTypes as $key => $value) :
            $options[] = JHTML::_('select.option', $value['id'], $value['name']);
        endforeach;

        // Aditional HTML attributes for <select> tag
        $settings = array(
            'id' => 'staticTypesField',
            'option.key' => 'value',
            'option.value' => 'text',
            'onchange' => 'getTypeOptions();'
        );

        $selectField = JHtmlSelect::genericlist(
            $options,
            'staticType',  // Name of select field
            $settings,
            'value',       // Standard
            'text',        // variables
            $selected      // Selected
        );

        return $selectField;
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

    /**
     * Returns a static type based on $static_typeID
     *
     * @param   Integer  $static_typeID  ID
     *
     * @return mixed
     */
    public function  getStaticType($static_typeID)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('id, name')
            ->from('#__thm_groups_static_type')
            ->where('id = ' . (int) $static_typeID);
        $dbo->setQuery($query);
        $dbo->execute();

        $staticType = $dbo->loadObject();

        return $staticType;
    }
}