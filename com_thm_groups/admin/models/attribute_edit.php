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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = [], $loadData = true)
    {
        if (empty($this->form)) {
            $this->form = $this->loadForm('com_thm_groups.attribute_edit', 'attribute_edit',
                ['control' => 'jform', 'load_data' => $loadData]);
        }

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
     */
    protected function loadFormData()
    {
        $input         = JFactory::getApplication()->input;
        $name          = $this->get('name');
        $resource      = str_replace('_edit', '', $name);
        $task          = $input->getCmd('task', "$resource.add");
        $resourceArray = $input->get('cid', [], 'array');
        $resourceID    = empty($resourceArray) ? $input->getInt('id', 0) : $resourceArray[0];

        $add = (($task != "$resource.edit") and empty($resourceID));
        if ($add) {
            return $this->getItem(0);
        }

        $item    = $this->getItem($resourceID);
        $options = json_decode($item->options);
        if (!empty($options)) {
            $item->validate = empty($options->required) ? 0 : 1;
            if (!empty($options->icon)) {
                $item->iconpicker = $options->icon;
            }
        }

        return $item;
    }
}
