<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

define('PUBLISH', 1);
define('UNPUBLISH', 0);
define('ARCHIVE', 2);
define('TRASH', -2);

/**
 * THM_GroupsModelQuickpage class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelQuickpage extends JModelLegacy
{

    /**
     * The event to trigger after changing the published state of the data.
     *
     * @var    string
     * @since  12.2
     */
    protected $event_change_state = null;

    /**
     * Maps events to plugin groups.
     *
     * @var array
     */
    protected $events_map = null;

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JModelLegacy
     * @since   12.2
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        if (isset($config['event_change_state']))
        {
            $this->event_change_state = $config['event_change_state'];
        }
        elseif (empty($this->event_change_state))
        {
            $this->event_change_state = 'onContentChangeState';
        }
    }

    /**
     * Method which checks user edit state permissions for the quickpage.
     *
     * @param   int  $qpID  the id of the quickpage
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
     *
     */
    protected function canEditState($qpID)
    {
        // Check admin rights before descending into the mud
        $user = JFactory::getUser();
        $isAdmin = ($user->authorise('core.admin', 'com_content') OR $user->authorise('core.admin', 'com_thm_groups'));
        if ($isAdmin)
        {
            return true;
        }

        // TODO: Would it be possible for a person of the same group to edit the state of 'my' article?
        return JFactory::getUser()->authorise('core.edit.state', "com_content.article.$qpID");
    }

    /**
     * Method to change the published state of one or more records.
     *
     * @param   array    &$pks   A list of the primary keys to change.
     * @param   integer  $value  The value of the published state.
     *
     * @return  boolean  True on success.
     *
     */
    public function publish()
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $qpIDs = Joomla\Utilities\ArrayHelper::toInteger($input->get('cid', array(), 'array'));

        // Should never occur
        if (empty($qpIDs))
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'notice');
            return;
        }

        $qpID = $qpIDs[0];
        if (!$this->canEditState($qpID))
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NOT_ALLOWED'), 'error');
            return;
        }

        $task = $input->getCmd('task');
        $value = constant(strtoupper(str_replace('quickpage.', '', $task)));

        JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
        $table = $this->getTable('Content', 'JTable');

        // Attempt to change the state of the records.
        $success = $table->publish($qpID, $value, JFactory::getUser()->id);
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
     * @param   array    $pks    An array of primary key ids.
     * @param   integer  $order  +1 or -1
     *
     * @return  mixed
     *
     */
    public function saveorder($pks = null, $order = null)
    {
        JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
        $table = $this->getTable('Content', 'JTable');
        $tableClassName = get_class($table);
        $contentType = new JUcmType;
        $type = $contentType->getTypeByTable($tableClassName);
        $tagsObserver = $table->getObserverOfClass('JTableObserverTags');
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
     * Toggles quickpage attributes
     *
     * @param   String  $action  publish/unpublish
     *
     * @return  boolean  true on success, otherwise false
     */
    public function toggle()
    {
        $app = JFactory::getApplication();
        $input = $app->input;

        $qpID = $input->getInt('id', 0);

        // Should only occur by url manipulation, but has validity
        if (empty($qpID))
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'warning');
            return;
        }

        $attribute = $input->getString('attribute', '');
        $allowedAttributes = array('featured', 'published');
        $invalidAttribute = (empty($attribute) OR !in_array($attribute, $allowedAttributes));

        // Should only occur by url manipulation, general error
        if ($invalidAttribute)
        {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
            return;
        }

        $value = $input->getBool('value', false);

        // TODO: Create a table class to take care of this
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $tableName = '#__thm_groups_users_content';

        $qpExists = $this->quickpageExists($qpID);
        if ($qpExists)
        {
            $query->update($tableName)->where("contentID = '$qpID'");

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

            $values = array(JFactory::getUser()->id, $qpID);
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
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }

        return empty($success)? false : true;
    }


    /**
     * Checks if an article were previously featured or published for modules
     *
     * @param   int  $qpID  the id of the quickpage
     *
     * @return  bool  true if the quickpage already exists, otherwise false
     */
    public function quickpageExists($qpID)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__thm_groups_users_content')
            ->where('contentID = ' . (int) $qpID);
        $db->setQuery($query);

        $result = $db->loadObject();

        if (empty($result) || $result == null)
        {
            return false;
        }

        return true;
    }
}
