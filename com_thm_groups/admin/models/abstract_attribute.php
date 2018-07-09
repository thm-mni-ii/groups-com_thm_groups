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

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/field_types.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelAbstract_Attribute extends JModelLegacy
{
    /**
     * Deletes selected abstract attributes from the db
     *
     * @return  mixed  true on success, otherwise false
     */
    public function delete()
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $doNotDelete = [TEXT, TEXTFIELD, LINK, PICTURE, MULTISELECT, TABLE, NUMBER, DATE, TEMPLATE];
        $selected    = $app->input->get('cid', [], 'array');
        $dtIDs       = array_diff($selected, $doNotDelete);

        $query = $this->_db->getQuery(true);
        $query->delete('#__thm_groups_abstract_attributes')->where('id IN (' . implode(',', $dtIDs) . ')');
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
     * Save abstract attributes
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $data       = $app->input->get('jform', [], 'array');
        $fieldType  = $data['field_typeID'];
        $defOptions = THM_GroupsHelperField_Types::getOption($fieldType);
        $options    = new stdClass();

        switch ($fieldType) {
            case TEXT:

                $options->length = empty($data['length']) ? $defOptions->length : (int)$data['length'];
                break;

            case TEXTFIELD:

                $options->length = empty($data['length']) ? $defOptions->length : (int)$data['length'];
                break;

            case PICTURE:
                $options->path = $defOptions->path;
                break;

            case LINK:
            case MULTISELECT:
            case TABLE:
            case TEMPLATE:

                $options = $defOptions;
                break;
        }

        $data['options']     = json_encode($options);
        $data['description'] = $this->_db->escape($data['description']);


        $abstractAttributes = $this->getTable('Abstract_Attributes', 'THM_GroupsTable');

        $success = $abstractAttributes->save($data);

        return empty($success) ? false : $abstractAttributes->id;
    }
}
