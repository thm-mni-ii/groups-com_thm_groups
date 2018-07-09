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
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelAbstract_Attribute_Edit extends THM_GroupsModelEdit
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
            $this->form = $this->loadForm('com_thm_groups.abstract_attribute_edit', 'abstract_attribute_edit',
                ['control' => 'jform', 'load_data' => $loadData]);
        }

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
    public function getTable($type = 'Abstract_Attributes', $prefix = 'THM_GroupsTable', $config = [])
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
        $input      = JFactory::getApplication()->input;
        $task       = $input->getCmd('task', "abstract_attribute.add");
        $abstractID = $input->getInt('id', 0);

        // Edit can only be explicitly called from the list view or implicitly with an id over a URL
        $edit = (($task == "abstract_attribute.edit") or $abstractID > 0);

        if ($edit) {
            if (empty($abstractID)) {
                $selected = $input->get('cid', [], 'array');

                if (empty($selected)) {
                    return $this->getItem(0);
                }

                $abstractID = $selected[0];
            }

            $item    = $this->getItem($abstractID);
            $options = empty($item->options) ? '' : json_decode($item->options);

            if (!empty($options)) {
                if (isset($options->required)) {
                    $item->validate = $options->required === false ? 0 : 1;
                }
            }

            return $item;
        }

        return $this->getItem(0);
    }
}
