<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        structure item model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
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
class THMGroupsModelStructure_Item_Edit extends JModelAdmin
{

    /**
     * returns one dynamic item
     *
     * @return mixed|stdClass
     */
    public function getStructureItem()
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
            $query->from($db->qn('#__thm_groups_structure_item'));
            $query->where('id = ' . (int) $id);
            $db->setQuery($query);

            return $db->loadObject();
        }
        else
        {
            // Bullshit part
            $temp = new stdClass;
            $temp->id = 0;
            $temp->dynamic_typeID = 0;
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
        $form = $this->loadForm('com_thm_groups.structure_item_edit', 'structure_item_edit',
            array('control' => 'jform', 'load_data' => $loadData));
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
    public function getTable($type = 'Structure_Item', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * returns a list of static types
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
     * Generate Select Field for static types
     *
     * @param   Int  static_typeID  dynamic type id, check static_TypeID for current dynamic type
     *
     * @return  select field
     */
    public function getDynamicTypesSelectField($dynamic_typeID)
    {
        $options = array();

        $selected = $dynamic_typeID;

        $arrayOfStaticTypes = $this->getDynamicTypes();

        // Convert array to options
        foreach($arrayOfStaticTypes as $key => $value) :
            $options[] = JHTML::_('select.option', $value['id'], $value['name']);
        endforeach;

        $settings = array(
            'id' => 'staticTypesField',
            'option.key' => 'value',
            'option.value' => 'text'
        );

        $selectFieldStaticTypes = JHtmlSelect::genericlist(
            $options,
            'dynamicType',  // Name of select field
            $settings,
            'value',       // Standard
            'text',        // variables
            $selected              // Selected
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