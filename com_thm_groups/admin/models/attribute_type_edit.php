<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once HELPERS . 'attribute_types.php';
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelAttribute_Type_Edit extends THM_GroupsModelEdit
{
    protected $form = false;

    /**
     * Method to get the form
     *
     * @param   array   $data     Data         (default: Array)
     * @param   Boolean $loadData Load data  (default: true)
     *
     * @return  object the form
     * @throws Exception
     */
    public function getForm($data = [], $loadData = true)
    {
        if (empty($this->form)) {
            $this->form = $this->loadForm('com_thm_groups.attribute_type_edit', 'attribute_type_edit',
                ['control' => 'jform', 'load_data' => $loadData]);
        }

        $typeID = JFactory::getApplication()->input->getInt('id', 0);
        THM_GroupsHelperAttribute_Types::configureForm($typeID, $this->form, true);

        return $this->form;
    }

    /**
     * returns table object
     *
     * @param   string $type   type
     * @param   string $prefix prefix
     * @param   array  $config config
     *
     * @return  JTable|mixed
     */
    public function getTable($type = 'Attribute_Types', $prefix = 'THM_GroupsTable', $config = [])
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     * @throws exception
     * @throws Exception
     */
    protected function loadFormData()
    {
        $input  = JFactory::getApplication()->input;
        $typeID = $input->getInt('id', 0);

        // This avoids opening the edit view when the new button was pushed regardless of item selection
        $task = $input->getCmd('task', "attribute_type.add");
        $edit = (($task == "attribute_type.edit") or $typeID > 0);

        if ($edit and empty($typeID)) {
            $selected = $input->get('cid', [], 'array');
            if (!empty($selected)) {
                $typeID = $selected[0];
            }
        }

        return $this->getItem($typeID);
    }
}
