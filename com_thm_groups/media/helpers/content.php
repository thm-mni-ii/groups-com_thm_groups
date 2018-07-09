<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

define('PUBLISH', 1);
define('UNPUBLISH', 0);
define('ARCHIVE', 2);
define('TRASH', -2);

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperContent
{
    /**
     * Method which checks user edit state permissions for content.
     *
     * @param   int $contentID the id of the content
     *
     * @return  boolean  True if allowed to change the state of the record.
     *          Defaults to the permission for the component.
     *
     */
    public static function canEditState($contentID)
    {
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if ($isAdmin or $isComponentManager) {
            return true;
        }

        return JFactory::getUser()->authorise('core.edit.state', "com_content.article.$contentID");
    }

    /**
     * Checks whether the user has permission to edit the content associated with the ids provided.
     *
     * @param array $contentIDs the content ids submitted by the form
     *
     * @return bool true if the user can edit the state all referenced content, otherwise false
     */
    private static function canReorder($contentIDs)
    {
        foreach ($contentIDs as $contentID) {
            $canReorder = THM_GroupsHelperContent::canEditState($contentID);

            if (empty($canReorder)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Changes created_user_id (author) attribute for a given category
     *
     * @param   int $profileID user ID
     * @param   int $catID     category ID
     *
     * @return bool true on success, otherwise false
     */
    private static function changeCategoryCreator($profileID, $catID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->update('#__categories')->set("created_user_id = '$profileID'")->where("id = $catID");
        $dbo->setQuery($query);

        try {
            $success = $dbo->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($success) ? false : true;
    }

    /**
     * Checks if an article were previously featured or published for modules
     *
     * @param   int $contentID the id of the content
     *
     * @return  bool  true if the content already exists, otherwise false
     */
    public static function contentExists($contentID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('*')->from('#__thm_groups_content')->where("contentID = '$contentID'");
        $dbo->setQuery($query);

        try {
            $result = $dbo->loadObject();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($result) ? false : true;
    }

    /**
     * Creates a content category for the user's personal content
     *
     * @param   string $title     Category Title
     * @param   string $alias     Category Alias
     * @param   int    $parentID  Parent ID of this Category entry
     * @param   int    $profileID Id of user
     *
     * @return  mixed int the id of the created category on success, otherwise false
     */
    private static function createCategory($title, $alias, $parentID, $profileID)
    {
        $dbo = JFactory::getDBO();

        // Get the path of the root category
        $query = $dbo->getQuery(true);
        $query->select("path")->from("#__categories")->where("id = '$parentID'");
        $dbo->setQuery($query);

        try {
            $path = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        $properties                    = [];
        $properties['title']           = $title;
        $properties['alias']           = $alias;
        $properties['path']            = "$path/$alias";
        $properties['extension']       = 'com_content';
        $properties['published']       = 1;
        $properties['access']          = 1;
        $properties['params']          = '{"target":"","image":""}';
        $properties['metadata']        = '{"page_title":"","author":"","robots":""}';
        $properties['created_user_id'] = $profileID;
        $properties['language']        = '*';

        $table = JTable::getInstance('Category', 'JTable', []);

        // Append category to parent as last child
        $table->setLocation($parentID, 'last-child');

        // Bind properties, check and save the category
        $success = $table->save($properties);

        return empty($success) ? false : $table->id;
    }

    /**
     * Creates a content category for the profile
     *
     * @param   int $profileID the id of the user for whom the category is to be created
     *
     * @return void
     */
    public static function createProfileCategory($profileID)
    {
        $user = JFactory::getUser($profileID);

        // Remove overhead from name, although honestly they should not be making personal content from non-personal accounts
        $overhead   = ["(", ")", "Admin", "Webmaster"];
        $namePieces = explode(" ", str_replace($overhead, '', $user->name));
        $surname    = array_pop($namePieces);

        // Surname, Forename(s);
        $title = trim($surname) . ", " . trim(implode(" ", $namePieces));

        $rawAlias = trim($surname) . "-" . trim(implode("-", $namePieces)) . "-" . $profileID;
        $alias    = JFilterOutput::stringURLSafe($rawAlias);

        $parentID = self::getRootCategory();

        if ($parentID > 0) {
            // Create category and get its ID
            $categoryID = self::createCategory($title, $alias, $parentID, $profileID);

            // Change created_user_id attribute in db, because of bug
            self::changeCategoryCreator($profileID, $categoryID);

            // Map category to profile
            self::mapUserCategory($profileID, $categoryID);
        }
    }

    /**
     * Gets the profile's category id
     *
     * @param   int $profileID the user id
     *
     * @return  mixed  int on successful query, null if the query failed, 0 on exception or if user is empty
     */
    public static function getCategoryID($profileID)
    {
        if (empty($profileID)) {
            return 0;
        }

        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('id');
        $query->from('#__thm_groups_categories');
        $query->where("profileID = '$profileID'");
        $dbo->setQuery($query);

        try {
            $categoryID = $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return 0;
        }

        return (empty($categoryID)) ? 0 : $categoryID;
    }

    /**
     * Returns the root category for profile content
     *
     * @return mixed
     */
    public static function getRootCategory()
    {
        $params = JComponentHelper::getParams('com_thm_groups');

        return $params->get('rootCategory');
    }

    /**
     * Returns dropdown for changing content status
     *
     * @param   int    $index the current row index
     * @param   object $item  the content item being iterated
     *
     * @return  string the HTML for the status selection dialog
     */
    public static function getStatusDropdown($index, $item)
    {
        $status    = '';
        $canChange = THM_GroupsHelperContent::canEditState($item->id);

        $task = 'content.publish';

        $status .= '<div class="btn-group">';
        $status .= JHtml::_('jgrid.published', $item->state, $index, "$task.", $canChange, 'cb', $item->publish_up,
            $item->publish_down);

        $archive = $item->state == 2 ? 'unarchive' : 'archive';
        $status  .= JHtml::_('actionsdropdown.' . $archive, 'cb' . $index, $task);

        $trash  = $item->state == -2 ? 'untrash' : 'trash';
        $status .= JHtml::_('actionsdropdown.' . $trash, 'cb' . $index, $task);

        $status .= JHtml::_('actionsdropdown.render', JFactory::getDbo()->escape($item->title));
        $status .= "</div>";

        return $status;
    }

    /**
     * Inserts a new data row into the content mapping table.
     *
     * @param   int $profileID  the profile ID
     * @param   int $categoryID the category ID to be associated with the profile
     *
     * @return  void
     */
    private static function mapUserCategory($profileID, $categoryID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->insert('#__thm_groups_categories')->set("profileID = '$profileID'")->set("categoryID = '$categoryID'");

        $dbo->setQuery($query);
        $dbo->execute();
    }

    /**
     * Determines whether a category entry exists for a user or group.
     *
     * @param   int $profileID the user id to check against groups categories
     *
     * @return  boolean  true, if a category exists, otherwise false
     */
    public static function profileCategoriesExist($profileID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);
        $query->select('COUNT(ID)')->from('#__thm_groups_categories')->where("profileID = '$profileID'");
        $dbo->setQuery($query);

        try {
            $result = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return ($result > 0);
    }

    /**
     * Method to change the core published state of THM Groups articles.
     *
     * @return  boolean  true on success, otherwise false
     */
    public static function publish()
    {
        $app   = JFactory::getApplication();
        $input = $app->input;

        $contentIDs = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', [], 'array'));

        if (empty($contentIDs) or empty($contentIDs[0])) {
            return false;
        }

        $contentID = $contentIDs[0];

        if (!THM_GroupsHelperContent::canEditState($contentID)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $taskParts     = explode('.', $app->input->getString('task'));
        $status        = count($taskParts) == 3 ? $taskParts[2] : 'unpublish';
        $validStatuses = ['publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3];

        // Unarchive and untrash equate to unpublish.
        $statusValue = Joomla\Utilities\ArrayHelper::getValue($validStatuses, $status, 0, 'int');

        JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
        $table = JTable::getInstance('Content', 'JTable');

        // Attempt to change the state of the records.
        $success = $table->publish($contentID, $statusValue, JFactory::getUser()->id);

        if (!$success) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_STATE_FAIL'), 'error');

            return false;
        }

        return true;
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array $contentIDs an array of primary content ids
     * @param   array $order      the order for the content items
     *
     * @return  mixed
     *
     */
    public static function saveorder($contentIDs = null, $order = null)
    {
        if (empty($contentIDs)) {
            return false;
        }

        $canReorder = self::canReorder($contentIDs);

        JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
        $table      = JTable::getInstance('Content', 'JTable');
        $conditions = [];

        // Update ordering values
        foreach ($contentIDs as $index => $contentID) {
            $table->load((int)$contentID);

            if ($table->ordering != $order[$index]) {
                $table->ordering = $order[$index];

                if (!$table->store()) {
                    return false;
                }

                // Remember to reorder within position and client_id
                $condition   = [];
                $condition[] = 'catid = ' . (int)$table->catid;

                $found = false;

                foreach ($conditions as $cond) {
                    if ($cond[1] == $condition) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $key          = $table->getKeyName();
                    $conditions[] = [$table->$key, $condition];
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond) {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        // Clear the component's cache
        THM_GroupsHelperComponent::cleanCache();

        return true;
    }

    /**
     * Toggles the binary attribute 'featured'
     *
     * @return  mixed  integer on success, otherwise false
     */
    public static function toggle()
    {
        $app   = JFactory::getApplication();
        $input = $app->input;

        $selectedContent = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', [], 'array'));
        $toggleID        = $input->getInt('id', 0);
        $value           = $input->getBool('value', false);

        // Should never occur without request manipulation
        if (empty($selectedContent) and empty($toggleID)) {
            return false;
        } // The inline toggle was used.
        elseif (empty($selectedContent)) {
            $selectedContent = [$toggleID];

            // Toggled values reflect the current value not the desired value
            $value = !$value;
        }

        $attribute        = $input->getString('attribute', '');
        $invalidAttribute = (empty($attribute) or $attribute != 'featured');

        // Should only occur by url manipulation, general error
        if ($invalidAttribute) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');

            return false;
        }

        foreach ($selectedContent as $contentID) {
            $canEditState = JFactory::getUser()->authorise('core.edit.state', "com_content.article.$contentID");

            if (!$canEditState) {
                $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

                return false;
            }

            $success = self::updateState($contentID, $value);

            if (!$success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a THM Groups article exists and executes a corresponding query
     *
     * @param   int $contentID ID of the THM Groups article
     * @param   int $value     Value to save, 0 or 1
     *
     * @return  mixed
     */
    private static function updateState($contentID, $value)
    {
        $dbo       = JFactory::getDbo();
        $query     = $dbo->getQuery(true);
        $tableName = '#__thm_groups_content';

        $contentExists = THM_GroupsHelperContent::contentExists($contentID);

        if ($contentExists) {
            $query->update($tableName)->set("featured = '$value'")->where("contentID = '$contentID'");
        } // TODO: There is no synchronization plugin or event. This block is necessary to synchronize group attributes with content
        else {
            $profileID = JFactory::getUser()->id;
            $query->insert('#__thm_groups_content')
                ->columns(['profileID', 'contentID', 'featured'])
                ->values("'$profileID','$contentID','$value'");
        }

        $dbo->setQuery($query);

        try {
            $success = $dbo->execute();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return empty($success) ? false : true;
    }
}
