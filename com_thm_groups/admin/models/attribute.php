<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        attribute model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
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
class THM_GroupsModelAttribute extends JModelLegacy
{

    /**
     * Creates empty database entries for all users for the new created attribute
     *
     * @param   int  $attributeID  An id of a new created attribute
     *
     * @return bool
     *
     * @throws Exception
     */
    public function createEmptyRowsForAllUsers($attributeID)
    {
        $dbo = JFactory::getDbo();
        $ids = $this->getUserIDs();
        $usersWithAttribute = $this->getUserIDsByAttributeID($attributeID);
        $ids = $this->filterIDs($ids, $usersWithAttribute);

        /*
         * Create database entry for created attribute with empty value for all users
         * It will be used in profile_edit view
         * If you find a better solution, you replace it
         */
        foreach ($ids as $id)
        {
            $query = $dbo->getQuery(true);
            $columns = array('usersID', 'attributeID', 'published');

            $values = array($id, $attributeID, 0);
            $query
                ->insert($dbo->qn('#__thm_groups_users_attribute'))
                ->columns($dbo->qn($columns))
                ->values(implode(',', $values));
            $dbo->setQuery($query);

            try
            {
                $dbo->execute();
            }
            catch (Exception $exception)
            {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Filters user IDs and exclude IDs, which already have
     * an attribute
     *
     * @param   array  $ids     An array with all user IDs
     * @param   array  $badIDs  An array with user IDs, which have an attribute
     *
     * @return array
     */
    public function filterIDs($ids, $badIDs)
    {
        $idsToSave = array();
        $idsNotToSave = array();

        // Prepare ids for search
        foreach ($ids as $id)
        {
            array_push($idsToSave, $id->id);
        }

        // Prepare ids for search
        foreach ($badIDs as $id)
        {
            array_push($idsNotToSave, $id->usersID);
        }

        // Search ids and if founded then delete
        foreach ($idsToSave as $key => $id)
        {
            if (array_search($id, $idsNotToSave) !== false)
            {
                unset($idsToSave[$key]);
            }
        }

        return $idsToSave;
    }

    /**
     * Returns all user IDs which have an attribute with
     * the $attributeID
     *
     * @param   int  $attributeID  An attribute id
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function getUserIDsByAttributeID($attributeID)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('usersID')
            ->from('#__thm_groups_users_attribute')
            ->where("attributeID = $attributeID");
        $dbo->setQuery($query);

        try
        {
            $result = $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        return $result;
    }

    /**
     * Returns user IDs from THM Groups component
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function getUserIDs()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('id')
            ->from($dbo->qn('#__thm_groups_users'));

        $dbo->setQuery($query);

        try
        {
            $ids = $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        return $ids;
    }

    /**
     * Saves the attribute
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
        $staticTypeID = $this->getStaticTypeIDByDynTypeID($data['dynamic_typeID']);
        $options = THM_GroupsHelperStatic_Type::getOption($staticTypeID);
        $dbo = JFactory::getDbo();

        switch ($staticTypeID)
        {
            case TEXT:
            case TEXTFIELD:
                $options->length = empty($data['length'])? $options->length : (int) $data['length'];
            default:
                $options->required = isset($data['validate'])? (bool)$data['validate'] : false;
                if (!empty($data['iconpicker']))
                {
                    $options->icon = $data['iconpicker'];
                }
                $data['options'] = json_encode($options);
                $data['description'] = empty($data['description'])? " " : $dbo->escape($data['description']);
        }

        $dbo->transactionStart();

        $attribute = $this->getTable();

        $success = $attribute->save($data);

        if (!$success)
        {
            $dbo->transactionRollback();
            return false;
        }
        $dbo->transactionCommit();
        return $attribute->id;
    }

    /**
     * Returns a static type ID of a dynamic type by its ID
     *
     * @param   Int  $dynTypeID  dynamic type ID
     *
     * @return Int On success, else false
     *
     * @throws Exception
     */
    public function getStaticTypeIDByDynTypeID($dynTypeID)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('static_typeID')
            ->from('#__thm_groups_dynamic_type')
            ->where('id = ' . (int) $dynTypeID);
        $dbo->setQuery($query);

        try
        {
            $result = $dbo->loadResult();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        return $result;
    }

    /**
     * Deletes selected attributes from the db
     *
     * @param   array  $idsToDelete  IDs of items which should be deleted
     *
     * @return  mixed  true on success, otherwise false
     */
    public function delete($idsToDelete)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->delete('#__thm_groups_attribute');
        $query->where('id IN (' . implode(',', $idsToDelete) . ')');
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

    /**
     * Toggles the attribute
     *
     * @param   String  $action  publish/unpublish
     *
     * @return  boolean  true on success, otherwise false
     */
    public function toggle($action = null)
    {
        $input = JFactory::getApplication()->input;

        // Get array of ids if divers users selected
        $cid = $input->post->get('cid', array(), 'array');

        // A string with type of column in table
        $attribute = $input->get('attribute', '', 'string');

        // If array is empty, the toggle button was clicked
        if (empty($cid))
        {
            $id = $input->getInt('id', 0);
        }
        else
        {
            JArrayHelper::toInteger($cid);
            $id = implode(',', $cid);
        }

        if (empty($id))
        {
            return false;
        }

        // Will used if buttons (Publish/Unpublish user) in toolbar clicked
        switch ($action)
        {
            case 'publish':
                $value = 1;
                break;
            case 'unpublish':
                $value = 0;
                break;
            default:
                $value = $input->getInt('value', 1)? 0 : 1;
                break;
        }

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query
            ->update('#__thm_groups_attribute')
            ->where("id IN ( $id )");

        switch ($attribute)
        {
            case 'published':
            default:
                $query->set("published = '$value'");
                break;
        }

        $dbo->setQuery((string) $query);

        try
        {
            return (bool) $dbo->execute();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array    $pks    An array of primary key ids.
     * @param   integer  $order  +1 or -1
     *
     * @return  mixed
     *
     */
    public function saveorder($pks = null, $order = null)
    {
        JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_thm_groups/tables/');
        $table = $this->getTable('Attribute', 'Table');

        $conditions = array();

        if (empty($pks))
        {
            return JError::raiseWarning(500, JText::_('COM_THM_GROUPS_NO_ITEMS_SELECTED'));
        }

        // Update ordering values
        foreach ($pks as $i => $pk)
        {
            $table->load((int) $pk);

            // Access checks.
            if (!$this->canEditState($table))
            {
                // Prune items that you can't change.
                unset($pks[$i]);
                JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
            }
            elseif ($table->ordering != $order[$i])
            {
                $table->ordering = $order[$i];

                if (!$table->store())
                {
                    $this->setError($table->getError());
                    return false;
                }

                // Remember to reorder within position and client_id
                $condition = $this->getReorderConditions($table);
                $found = false;

                foreach ($conditions as $cond)
                {
                    if ($cond[1] == $condition)
                    {
                        $found = true;
                        break;
                    }
                }

                if (!$found)
                {
                    $key = $table->getKeyName();
                    $conditions[] = array($table->$key, $condition);
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond)
        {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   JTable  $table  A JTable object.
     *
     * @return  array  An array of conditions to add to ordering queries.
     *
     */
    protected function getReorderConditions($table)
    {
        return array();
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
     *
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        return $user->authorise('core.edit.state', 'com_content');
    }
}
