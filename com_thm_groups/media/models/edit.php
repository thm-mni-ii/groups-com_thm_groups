<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelEdit extends JModelAdmin
{
    /**
     * Method to get the form
     *
     * @param   array   $data     Data         (default: Array)
     * @param   Boolean $loadData Load data  (default: true)
     *
     * @return  mixed  JForm object on success, False on error.
     */
    public function getForm($data = [], $loadData = true)
    {
        $name = $this->get('name');
        $form = $this->loadForm("com_thm_groups.$name", $name, ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer $pk The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @throws  exception  if the user is not authorized to access the view
     */
    public function getItem($pk = null)
    {
        if (!THM_GroupsHelperComponent::isManager()) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
        }

        return parent::getItem($pk);
    }

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string $name    The table name. Optional.
     * @param   string $prefix  The class prefix. Optional.
     * @param   array  $options Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     */
    public function getTable($name = '', $prefix = 'Table', $options = [])
    {
        /**
         * Joomla makes the mistake of handling front end and backend differently for include paths. Here we add the
         * possible frontend and media locations for logical consistency.
         */
        JTable::addIncludePath(JPATH_ROOT . "/media/com_thm_groups/tables");
        JTable::addIncludePath(JPATH_ROOT . "/components/com_thm_groups/tables");

        $type = str_replace('_edit', '', $this->get('name')) . 's';

        return JTable::getInstance($type, 'THM_GroupsTable', $options);
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     * @throws exception
     */
    protected function loadFormData()
    {
        $input      = JFactory::getApplication()->input;
        $name       = $this->get('name');
        $resource   = str_replace('_edit', '', $name);
        $task       = $input->getCmd('task', "$resource.add");
        $resourceID = $input->getInt('id', 0);

        // Edit can only be explicitly called from the list view or implicitly with an id over a URL
        $edit = (($task == "$resource.edit") or $resourceID > 0);
        if ($edit) {
            if (!empty($resourceID)) {
                return $this->getItem($resourceID);
            }

            $resourceIDs = $input->get('cid', null, 'array');

            return $this->getItem($resourceIDs[0]);
        }

        return $this->getItem(0);
    }
}
