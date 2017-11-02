<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

use MongoDB\BSON\Type;

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/static_type.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelDynamic_Type extends JModelLegacy
{
    /**
     * Deletes selected dynamic types from the db
     *
     * @return  mixed  true on success, otherwise false
     */
    public function delete()
    {
        $app = JFactory::getApplication();

        if (!JFactory::getUser()->authorise('core.admin', 'com_thm_groups')) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $doNotDelete = array(TEXT, TEXTFIELD, LINK, PICTURE, MULTISELECT, TABLE, NUMBER, DATE, TEMPLATE);
        $selected    = $app->input->get('cid', array(), 'array');
        $dtIDs       = array_diff($selected, $doNotDelete);

        $query = $this->_db->getQuery(true);
        $query->delete('#__thm_groups_dynamic_type')->where('id IN (' . implode(',', $dtIDs) . ')');
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
     * Save element of dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $app = JFactory::getApplication();

        if (!JFactory::getUser()->authorise('core.admin', 'com_thm_groups')) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $data         = $app->input->get('jform', array(), 'array');
        $staticTypeID = $data['static_typeID'];
        $defOptions   = THM_GroupsHelperStatic_Type::getOption($staticTypeID);
        $options      = new stdClass();

        switch ($staticTypeID) {
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


        $dynamicType = $this->getTable('Dynamic_Type', 'THM_GroupsTable');

        $success = $dynamicType->save($data);

        return empty($success) ? false : $dynamicType->id;
    }
}
