<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once HELPERS . 'fields.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelAttribute_Type extends JModelLegacy
{
    /**
     * Deletes selected attribute types from the db
     *
     * @return  mixed  true on success, otherwise false
     * @throws Exception
     */
    public function delete()
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        // Simple Text, Complex Text / HTML, URL, Picture, Date, Email, Telephone, Name
        $predefinedTypes = [1, 2, 3, 4, 5, 6, 7, 8];

        $selected     = $app->input->get('cid', [], 'array');
        $deletableIDs = array_diff($selected, $predefinedTypes);

        if (empty($deletableIDs)) {
            return false;
        }

        $query = $this->_db->getQuery(true);
        $query->delete('#__thm_groups_attribute_types')->where('id IN (' . implode(',', $deletableIDs) . ')');
        $this->_db->setQuery($query);

        try {
            $success = $this->_db->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($success) ? false : true;
    }

    /**
     * Save attribute types
     *
     * @return bool true on success, otherwise false
     * @throws Exception
     */
    public function save()
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $data            = $app->input->get('jform', [], 'array');
        $data['options'] = json_encode(THM_GroupsHelperFields::getOptions($data['fieldID'], $data));

        $abstractAttributes = $this->getTable('Attribute_Types', 'THM_GroupsTable');

        $success = $abstractAttributes->save($data);

        return empty($success) ? false : $abstractAttributes->id;
    }
}
