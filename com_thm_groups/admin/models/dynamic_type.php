<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/static_type.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelDynamic_Type extends JModelLegacy
{
    /**
     * Save element of dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
        $staticTypeID = $data['static_typeID'];
        $defOptions = THM_GroupsHelperStatic_Type::getOption($staticTypeID);

        $options = new stdClass();
        switch ($staticTypeID)
        {
            case TEXT:
                $options->length = empty($data['length']) ? $defOptions->length : (int) $data['length'];
                break;
            case TEXTFIELD:
                $options->length = empty($data['length']) ? $defOptions->length : (int) $data['length'];
                break;
            case PICTURE:
                // Save always default values, because pictures are placed now only in /images/com_thm_groups/profile
                $options->path = $defOptions->path;
                $options->filename = $defOptions->filename;
                break;
            case LINK:
            case MULTISELECT:
            case TABLE:
            case TEMPLATE:
                $options = $defOptions;
                break;
        }

        $data['options'] = json_encode($options);
        $dbo = JFactory::getDbo();
        $data['description'] = $dbo->escape($data['description']);

        $dbo->transactionStart();

        $dynamicType = $this->getTable();

        $success = $dynamicType->save($data);


        if (!$success)
        {
            $dbo->transactionRollback();
            return false;
        }
        else
        {
            $dbo->transactionCommit();
            return $dynamicType->id;
        }
    }

    /**
     * Deletes selected dynamic types from the db
     *
     * @param   array  $idsToDelete  IDs of items which should be deleted
     *
     * @return  mixed  true on success, otherwise false
     */
    public function delete($idsToDelete)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->delete('#__thm_groups_dynamic_type');
        $query->where('id IN (' . join(',', $idsToDelete) . ')');
        $dbo->setQuery($query);

        try
        {
            return $dbo->execute();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }
    }
}