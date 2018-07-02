<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';
/** @noinspection MissingSinceTagDocInspection */


/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelTemplate_Edit extends THM_GroupsModelEdit
{
    /**
     * Method to get a table object, load it if necessary. Can't be generalized because of irregular english plural
     * spelling. :(
     *
     * @param   string $name    The table name. Optional.
     * @param   string $prefix  The class prefix. Optional.
     * @param   array  $options Configuration array for model. Optional.
     *
     * @return  JTable object
     */
    public function getTable($name = 'Template', $prefix = 'THM_GroupsTable', $options = [])
    {
        return JTable::getInstance($name, $prefix, $options);
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        $ids = $app->input->get('cid', [], 'array');

        // Input->get because id is in url
        $id = (empty($ids)) ? $app->input->get->get('id') : $ids[0];

        return $this->getItem($id);
    }
}
