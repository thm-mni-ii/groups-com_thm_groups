<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.admin
 */
class THMGroupsModelDynamic_Type_Edit extends JModelAdmin
{
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
        $form = $this->loadForm('com_thm_groups.dynamic_type_edit', 'dynamic_type_edit',
                                array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        $ids = $app->input->post->get('cid', array(), 'array');

        // input->get because id is in url
        $id = (empty($ids)) ? $app->input->get->get('id') : $ids[0];
        return $this->getItem($id);
    }

    /**
     * Method to get the table
     *
     * @return  JTable object
     */
    public function getTable($type = 'Dynamic_Type', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * @return  Array
     */
    public function getStaticTypes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id, name')
            ->from('#__thm_groups_static_type');
        $db->setQuery($query);
        $db->execute();

        return $db->loadAssocList();
    }

    public function store()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $jform = $app->input->post->get('jform', array(), 'array');
        $id = $jform['id'];
        $name = $jform['name'];
        $regex = $jform['regex'];
        $staticTypeID = $app->input->post->get('staticType');

        $query = $db->getQuery(true);

        // INSERT
        // Insert columns.
        $columns = array('static_typeID', 'name', 'regex');

        // Insert values.
        $values = array($staticTypeID, $db->quote($name), $db->quote($regex));

        // Prepare the insert query.
        $query
            ->insert($db->quoteName('#__thm_groups_dynamic_type'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);
        try
        {
            $db->execute();
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}