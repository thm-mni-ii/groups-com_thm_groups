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
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profiles.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelProfile_Edit extends THM_GroupsModelEdit
{
    /**
     * Returns all user attributes for the user edit form
     *
     * @param   int $profileID the user id
     *
     * @return  array  array of arrays containing profile information
     */
    public function getAttributes($profileID = 0)
    {
        $input     = JFactory::getApplication()->input;
        $profileID = empty($profileID) ? $input->getInt('id', 0) : $profileID;
        if (empty($profileID)) {
            return [];
        }

        return THM_GroupsHelperProfiles::getProfile($profileID);
    }

    /**
     * Method to load the form data
     *
     * @return  mixed  Object on success, false on failure.
     */
    protected function loadFormData()
    {
        $input       = JFactory::getApplication()->input;
        $selectedIDs = $input->get('cid', [], 'array');
        $id          = (empty($selectedIDs)) ? $input->getInt('id', 0) : $selectedIDs[0];

        return $this->getItem($id);
    }
}
