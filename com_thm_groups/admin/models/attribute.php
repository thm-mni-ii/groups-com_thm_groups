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

require_once HELPERS . 'attribute_types.php';
require_once HELPERS . 'fields.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelAttribute extends JModelLegacy
{
    const IMAGE_PATH = JPATH_ROOT . IMAGE_PATH;

    /**
     * Generates a row for this attribute's value for all existing user profiles
     *
     * @param   int $attributeID An id of a new created attribute
     *
     * @return bool
     *
     * @throws Exception
     */
    private function addProfileAttribute($attributeID)
    {
        $allProfileIDs        = $this->getProfileIDs();
        $associatedProfileIDs = $this->getAssocProfileIDs($attributeID);
        $unAssocProfileIDs    = array_diff($allProfileIDs, $associatedProfileIDs);

        // All profiles already have the attribute
        if (empty($unAssocProfileIDs)) {
            return true;
        }

        /*
         * Create database entry for created attribute with empty value for all users
         * It will be used in profile_edit view
         * If you find a better solution, you replace it
         */
        $query = $this->_db->getQuery(true);
        $query->insert('#__thm_groups_profile_attributes')->columns('profileID, attributeID, published');

        foreach ($unAssocProfileIDs as $profileID) {
            $query->values("'$profileID','$attributeID', '1'");
        }

        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return true;
    }

    /**
     * Deletes selected attributes from the db. Protected attributes are automatically removed from the selection.
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

        $doNotDelete = [FORENAME, SURNAME, EMAIL_ATTRIBUTE, TITLE, POSTTITLE];
        $selected    = $app->input->get('cid', [], 'array');
        Joomla\Utilities\ArrayHelper::toInteger($selected);
        $attributeIDs = array_diff($selected, $doNotDelete);

        $selectQuery = $this->_db->getQuery(true);
        $selectQuery->select('*')->from('#__thm_groups_attributes');

        $deleteQuery = $this->_db->getQuery(true);
        $deleteQuery->delete('#__thm_groups_attributes');

        foreach ($attributeIDs as $attributeID) {
            $selectQuery->clear('where');
            $selectQuery->where("id = '$attributeID'");

            $this->_db->setQuery($selectQuery);

            try {
                $attribute = $this->_db->loadAssoc();
            } catch (Exception $exception) {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
                continue;
            }

            if ($this->deletePictures($attribute)) {
                $deleteQuery->clear('where');
                $deleteQuery->where("id = '$attributeID'");
                $this->_db->setQuery($deleteQuery);

                try {
                    $success = $this->_db->execute();
                } catch (Exception $exception) {
                    JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

                    return false;
                }

                if (!$success) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes pictures associated with an attribute
     *
     * @param   array $attribute Object of attribute
     *
     * @return  boolean true on success, otherwise false
     * @throws Exception
     */
    private function deletePictures($attribute)
    {
        $query = $this->_db->getQuery(true);
        $query->select('id, value, attributeID')
            ->from('#__thm_groups_profile_attributes')
            ->where("attributeID = '{$attribute['id']}'");
        $this->_db->setQuery($query);

        try {
            $pictures = $this->_db->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        foreach (scandir(self::IMAGE_PATH) as $file) {
            foreach ($pictures as $picture) {
                if ($file == $picture['value']) {
                    unlink(self::IMAGE_PATH . "/$file");
                }
            }
        }

        return true;
    }

    /**
     * Returns all profile IDs which are associated with the given attribute ID
     *
     * @param   int $attributeID An attribute id
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    private function getAssocProfileIDs($attributeID)
    {
        $query = $this->_db->getQuery(true);
        $query->select('profileID')->from('#__thm_groups_profile_attributes')->where("attributeID = $attributeID");
        $this->_db->setQuery($query);

        try {
            $result = $this->_db->loadColumn();
        } catch (Exception $exception) {
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
    private function getProfileIDs()
    {
        $query = $this->_db->getQuery(true);
        $query->select('id')->from('#__thm_groups_profiles');
        $this->_db->setQuery($query);

        try {
            $profileIDs = $this->_db->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return $profileIDs;
    }

    /**
     * Saves the attribute
     *
     * @return mixed int attribute id on success, otherwise bool false
     * @throws Exception
     */
    public function save()
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $data                = $app->input->get('jform', [], 'array');
        $data['options']     = json_encode(THM_GroupsHelperAttribute_Types::getOptions($data['typeID'], $data));
        $data['description'] = empty($data['description']) ? "" : $this->_db->escape($data['description']);

        $this->_db->transactionStart();

        $attribute      = $this->getTable('Attributes', 'THM_GroupsTable');
        $attributeSaved = $attribute->save($data);

        if (!$attributeSaved) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
            $this->_db->transactionRollback();

            return false;
        }

        $attributeAdded = $this->addProfileAttribute($attribute->id);

        if (!$attributeAdded) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
            $this->_db->transactionRollback();

            return false;
        }

        $this->_db->transactionCommit();

        return $attribute->id;
    }

    /**
     * Toggles binary attribute attribute values.
     *
     * @return  boolean  true on success, otherwise false
     * @throws Exception
     */
    public function toggle()
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $input = JFactory::getApplication()->input;

        // Get array of ids if divers users selected
        $cid = $input->post->get('cid', [], 'array');

        // If array is empty, the toggle button was clicked
        if (empty($cid)) {
            $id = $input->getInt('id', 0);
        } else {
            Joomla\Utilities\ArrayHelper::toInteger($cid);
            $id = implode(',', $cid);
        }

        if (empty($id)) {
            return false;
        }

        $attribute       = $input->getString('attribute');
        $validAttributes = ['published', 'showLabel', 'showIcon'];
        if (empty($attribute) or !in_array($attribute, $validAttributes)) {
            return false;
        }

        $value = $input->getInt('value', 1) ? 0 : 1;

        $query = $this->_db->getQuery(true);

        $query->update('#__thm_groups_attributes')
            ->set("$attribute = '$value'")
            ->where("id IN ( $id )");

        $this->_db->setQuery($query);

        try {
            return (bool)$this->_db->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array $pks   An array of primary key ids.
     * @param   array $order the ordering values corresponding to the table keys
     *
     * @return bool true on success, otherwise false
     * @throws Exception
     */
    public function saveorder($pks = null, $order = null)
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        if (empty($pks)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEMS_SELECTED'), 'error');

            return false;
        }

        $table = $this->getTable('Attributes', 'THM_GroupsTable');

        // Update ordering values
        foreach ($pks as $i => $pk) {
            $table->load((int)$pk);

            if ($table->ordering != $order[$i]) {
                $table->ordering = $order[$i];

                if (!$table->store()) {
                    return false;
                }
            }
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }
}
