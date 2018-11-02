<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      Lavinia Popa-Rössel, <lavinia.popa-roessel@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once HELPERS . 'attributes.php';
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelAttribute_Edit extends THM_GroupsModelEdit
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
            $this->form = $this->loadForm('com_thm_groups.attribute_edit', 'attribute_edit',
                ['control' => 'jform', 'load_data' => $loadData]);
        }

        $attributeID = JFactory::getApplication()->input->getInt('id', 0);
        THM_GroupsHelperAttributes::configureForm($attributeID, $this->form);

        return $this->form;
    }

    /**
     * Returns table object
     *
     * @param   string $type   type
     * @param   string $prefix prefix
     * @param   array  $config config
     *
     * @return  JTable|mixed
     */
    public function getTable($type = 'Attributes', $prefix = 'THM_GroupsTable', $config = [])
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     * @throws exception
     */
    protected function loadFormData()
    {
        $input  = JFactory::getApplication()->input;
        $attributeID = $input->getInt('id', 0);

        // This avoids opening the edit view when the new button was pushed regardless of item selection
        $task = $input->getCmd('task', "attribute.add");
        $edit = (($task == "attribute.edit") or $attributeID > 0);

        if ($edit and empty($attributeID)) {
            $selected = $input->get('cid', [], 'array');
            if (!empty($selected)) {
                $attributeID = $selected[0];
            }
        }

        return $this->getItem($attributeID);
    }
}
