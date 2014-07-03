<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
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
        $ids = $app->input->get('cid', array(), 'array');

        // Input->get because id is in url
        $id = (empty($ids)) ? $app->input->get->get('id') : $ids[0];
        return $this->getItem($id);
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
     * returns a list of static types
     *
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

    /**
     * saves the dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function store()
    {
        $dbo = JFactory::getDbo();

        $app = JFactory::getApplication();
        $data = $app->input->post->get('jform', array(), 'array');
        $data['static_typeID'] = $app->input->post->get('staticType');

        // Cast to int, because the type in DB is int
        $data['static_typeID'] = (int) $data['static_typeID'];

        $dbo->transactionStart();

        $dynamicType = $this->getTable();

        $success = $dynamicType->save($data);

        if (!$success)
        {
            $dbo->transactionRollback();
            return false;
        }
        else
        {
            $dbo->transactionCommit();
            return $dynamicType->id;
        }
    }

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
}