<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/helpers/quickpage.php';

/**
 * THM_GroupsModelQuickpage class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelQuickpage extends JModelLegacy
{
    /**
     * Sets published or featured attributes of THM Groups articles to 1
     *
     * @param   array  $articleID Array with THM Groups article IDs
     * @param   string $attribute Attribute to save, 'published' or 'featured'
     *
     * @return  mixed  integer on success, false otherwise
     */
    public function activate($articleID, $attribute)
    {
        $app = JFactory::getApplication();

        // Should never occur
        if (empty($articleID))
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'notice');

            return false;
        }

        $successCount = 0;
        foreach ($articleID as $id)
        {
            $success = $this->updateQuickpageState($id, $attribute, 1);
            if ($success)
            {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Sets published or featured attribute of groups articles to 0
     *
     * @param   array  $cid       Array with article IDs
     * @param   string $attribute Attribute to save, 'published' or 'featured'
     *
     * @return  mixed  integer on success, false otherwise
     */
    public function deactivate($cid, $attribute)
    {
        $app = JFactory::getApplication();

        // Should never occur
        if (empty($cid))
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'notice');

            return false;
        }

        $successCount = 0;

        foreach ($cid as $id)
        {
            $success = $this->updateQuickpageState($id, $attribute, 0);

            if ($success)
            {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Returns author's ID for the given article
     *
     * @param   int $articleID the id of the article being checked
     *
     * @return  mixed int on success, otherwise null
     */
    private function getAuthorID($articleID)
    {
        $article = JTable::getInstance("content");
        $article->load($articleID);

        if (empty($article))
        {
            return JFactory::getUser()->id;
        }

        return $article->get('created_by');
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   object $table A record object.
     *
     * @return  array  An array of conditions to add to add to ordering queries.
     *
     * @since   1.6
     */
    protected function getReorderConditions($table)
    {
        $condition   = array();
        $condition[] = 'catid = ' . (int) $table->catid;

        return $condition;
    }

    /**
     * Method to change the core published state of THM Groups articles.
     *
     * @return  boolean  true on success, otherwise false
     */
    public function publish()
    {
        $app        = JFactory::getApplication();
        $articleIDs = $app->input->get('cid', array(), 'array');
        Joomla\Utilities\ArrayHelper::toInteger($articleIDs);

        // Should never occur
        if (empty($articleIDs))
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'notice');

            return false;
        }

        $articleID = $articleIDs[0];

        if (!THM_GroupsHelperQuickpage::canEditState($articleID))
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NOT_ALLOWED'), 'error');

            return false;
        }

        $task = $app->input->getCmd('task');
        JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
        $table = $this->getTable('Content', 'JTable');
        $value = constant(strtoupper(str_replace('quickpage.', '', $task)));

        // Attempt to change the state of the records.
        $success = $table->publish($articleID, $value, JFactory::getUser()->id);

        if (!$success)
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_STATE_FAIL'), 'error');

            return false;
        }

        return true;
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array   $pks   An array of primary key ids.
     * @param   integer $order +1 or -1
     *
     * @return  mixed
     *
     */
    public function saveorder($pks = null, $order = null)
    {
        if (empty($pks))
        {
            return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
        }

        JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
        $table      = $this->getTable('Content', 'JTable');
        $conditions = array();
        $user       = JFactory::getUser();

        // Update ordering values
        foreach ($pks as $i => $pk)
        {
            $table->load((int) $pk);

            $canEditState = $user->authorise('core.edit.state', 'com_content.article.' . $pk);

            // Access checks.
            if (!$canEditState)
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
                $found     = false;

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
                    $key          = $table->getKeyName();
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
     * Toggles THM Groups article attributes like 'published' and 'featured'
     *
     * @param  array $cid Array with THM Groups article IDs
     *
     * @return  mixed  integer on success, otherwise false
     */
    public function toggle($cid)
    {
        $app   = JFactory::getApplication();
        $input = $app->input;

        // If array is empty, the toggle button was clicked
        if (empty($cid))
        {
            $articleID = $input->getInt('id', 0);

            if (empty($articleID))
            {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'warning');

                return false;
            }

            $cid = array($articleID);
        }
        else
        {
            Joomla\Utilities\ArrayHelper::toInteger($cid);
        }

        $attribute         = $input->getString('attribute', '');
        $allowedAttributes = array('featured', 'published');
        $invalidAttribute  = (empty($attribute) OR !in_array($attribute, $allowedAttributes));

        // Should only occur by url manipulation, general error
        if ($invalidAttribute)
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');

            return false;
        }

        // Invert value according to the implementation
        $value = $input->getInt('value', 1) ? 0 : 1;

        // Process multiple ids
        $successCount = 0;

        foreach ($cid as $id)
        {
            $success = $this->updateQuickpageState($id, $attribute, $value);

            if ($success)
            {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Checks if a THM Groups article exists and executes a corresponding query
     *
     * @param   int    $articleID ID of the THM Groups article
     * @param   string $attribute Attribute to change, published or featured
     * @param   int    $value     Value to save, 0 or 1
     *
     * @return  mixed
     */
    private function updateQuickpageState($articleID, $attribute, $value)
    {
        $dbo       = JFactory::getDbo();
        $query     = $dbo->getQuery(true);
        $tableName = '#__thm_groups_users_content';

        $articleExists = THM_GroupsHelperQuickpage::quickpageExists($articleID);

        if ($articleExists)
        {
            $query->update($tableName)->where("contentID = '$articleID'");

            switch ($attribute)
            {
                case 'featured':
                    $query->set("featured = '$value'");
                    break;
                case 'published':
                    $query->set("published = '$value'");
                    break;
            }
        }

        // TODO: There is no synch plugin or event. This block is necessary to synch group attributes with content
        else
        {
            $query->insert('#__thm_groups_users_content')->columns(array('usersID', 'contentID', 'featured', 'published'));

            // Use create_by of the content
            $values = array($this->getAuthorID($articleID), $articleID);
            Joomla\Utilities\ArrayHelper::toInteger($values);

            switch ($attribute)
            {
                case 'featured':
                    $values[] = $value;
                    $values[] = 0;
                    break;
                case 'published':
                    $values[] = 0;
                    $values[] = $value;
                    break;
            }

            $query->values(implode(',', $values));
        }

        $dbo->setQuery((string) $query);

        try
        {
            $success = $dbo->execute();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return $success;
    }
}
