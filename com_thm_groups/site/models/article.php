<?php

/**
 * @version     v3.4.4
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once JPATH_COMPONENT . '/helper/content.php';

/**
 * Item Model for an Article.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THM_GroupsModelArticle extends JModelAdmin
{
    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_THM_QUICKPAGES';


    /**
     * Method to test whether a new record can be created in a category.
     *
     * @param   int  $currCategoryID  The category id to create the article in.
     *
     * @return	boolean	True if allowed to create a new article.
     */
    public function canCreate($currCategoryID)
    {
        $user = JFactory::getUser();

        return $user->authorise('core.create', 'com_content.category.' . $currCategoryID);
    }

    /**
     * Method to test whether a record can be checked in.
     *
     * @param   object  $record  A article record object.
     *
     * @return	boolean	True if allowed to checkin the article.
     */
    public function canCheckin($record)
    {
        $user = JFactory::getUser();

        return $user->authorise('core.manage',	'com_checkin') || $record->checked_out == $user->id || $record->checked_out == 0;
    }

    /**
     * Method to test whether a record can be edited.
     *
     * @param   object  $record  A article record object.
     *
     * @return	boolean	True if allowed to edit the article.
     */
    public function canEdit($record)
    {
        $user = JFactory::getUser();

        $canEdit    = $user->authorise('core.edit',		'com_content.article.' . $record->id);
        $canEditOwn = $user->authorise('core.edit.own',	'com_content.article.' . $record->id) && $record->created_by == $user->id;

        return ($canEdit || $canEditOwn);
    }

    /**
     * Method to test whether a article's state can be changed.
     * Important: Must exist to use library functions to change the state of an item.
     *
     * @param   object  $record  A article record object.
     *
     * @return	boolean	True if allowed to change the state of the article.
     */
    public function canEditState($record)
    {
        $user = JFactory::getUser();

        // Check for existing article.
        if (!empty($record->id))
        {
            return $user->authorise('core.edit.state', 'com_content.article.' . (int) $record->id);
        }
        // New article, so check against the category.
        elseif (!empty($record->catid))
        {
            return $user->authorise('core.edit.state', 'com_content.category.' . (int) $record->catid);
        }
        // Default to component settings if neither article nor category known.
        else
        {
            return parent::canEditState($record);
        }
    }

    /**
     * Method to test whether a article can be deleted.
     * Important: Must exist to use library functions to delete an item.
     *
     * @param   object  $record  A article record object.
     *
     * @return	boolean		True if allowed to delete the article.
     */
    public function canDelete($record)
    {
        $user = JFactory::getUser();

        return $user->authorise('core.delete', 'com_content.article.' . (int) $record->id);
    }


    /**
     * Returns a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return	JTable	A database object
    */
    public function getTable($type = 'Content', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            // Convert the params field to an array.
            $registry = new JRegistry;
            $registry->loadJSON($item->attribs);
            $item->attribs = $registry->toArray();

            // Convert the params field to an array.
            $registry = new JRegistry;
            $registry->loadJSON($item->metadata);
            $item->metadata = $registry->toArray();

            $item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />"
                . $item->fulltext : $item->introtext;
        }

        return $item;
    }

    /**
     * A protected method to get a set of ordering conditions.
     * Articles can be ordered independently within one category.
     *
     * @param   object  $table  A record object.
     *
     * @return	array	An array of conditions to add to add to ordering queries.
     */
    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'catid = ' . (int) $table->catid;
        return $condition;
    }

    /**
     * Dummy method: Functionally not needed, but method in super class is abstract
     *
     * @param   array    $data      Dummy
     * @param   boolean  $loadData  Dummy
     *
     * @return ERROR
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        return 'Fatal Error: No form supported for Quickpage articles_old';
    }

    /**
     * Toggles the article
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

        switch ($action)
        {
            default:
                $value = $input->getInt('value', 1)? 0 : 1;
                break;
        }

        if (!$this->isEntryExistsInDatabase($id))
        {
            return $this->createDatabaseEntry($id, $attribute, $value);
        }
        else
        {
            return $this->updateDatabaseEntry($id, $attribute, $value);
        }
    }

    /**
     * Creates a database entry, which will be used for quickpages modules
     *
     * @param   int     $id         An article id
     * @param   string  $attribute  An attribute to save
     * @param   int     $value      A value to save for attribute
     *
     * @return bool
     *
     * @throws Exception
     */
    public function createDatabaseEntry($id, $attribute, $value)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $uid = JFactory::getUser()->id;
        $values = array($uid, $id, $this->getValue($type = 'featured', $attribute, $value),
            $this->getValue($type = 'published', $attribute, $value));

        $query
            ->insert('#__thm_groups_users_content')
            ->columns(array('usersID', 'contentID', 'featured', 'published'))
            ->values(implode(',', $values));

        $db->setQuery($query);

        try
        {
            return (bool) $db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Returns either the value or default value 0 for attribute
     *
     * @param   string  $type       A type of an attribute to save
     * @param   string  $attribute  An attribute name
     * @param   int     $value      A value to return
     *
     * @return int
     */
    public function getValue($type, $attribute, $value)
    {
        if ($type === $attribute)
        {
            return $value;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Updates the values of an article
     *
     * @param   int     $id         An article id
     * @param   string  $attribute  An attribute to update
     * @param   int     $value      A new value to update
     *
     * @return bool
     *
     * @throws Exception
     */
    public function updateDatabaseEntry($id, $attribute, $value)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->update('#__thm_groups_users_content')
            ->where("contentID IN ( $id )");

        switch ($attribute)
        {
            case 'featured':
                $query->set("featured = '$value'");
                break;
            case 'published':
                $query->set("published = '$value'");
                break;
        }

        $db->setQuery((string) $query);

        try
        {
            return (bool) $db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Checks if an article were previously featured or published for modules
     *
     * @param   int  $id  An id of an article
     *
     * @return bool true|false
     */
    public function isEntryExistsInDatabase($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__thm_groups_users_content')
            ->where('contentID = ' . (int) $id);
        $db->setQuery($query);

        $result = $db->loadObject();

        if (empty($result) || $result == null)
        {
            return false;
        }

        return true;
    }

    /**
     * Unfeatures article in DB
     *
     * @param   Int  $a_id  article id
     *
     * @return void
     */
    public static function deleteArticleId($a_id)
    {
        $db = JFactory::getDbo();
        $deleteQuery = $db->getQuery(true);


        $deleteQuery
            ->delete('#__thm_groups_users_content')
            ->where('contentID = ' . (int) $a_id);

        $db->setQuery((string) $deleteQuery);

        try
        {
            $db->execute();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }
    }
}
