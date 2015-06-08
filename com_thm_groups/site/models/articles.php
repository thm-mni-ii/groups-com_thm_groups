<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelArticles_Test
 * @description THM_GroupsModelArticles_Test file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('thm_core.list.model');
jimport('thm_groups.data.lib_thm_groups_quickpages');
require_once JPATH_COMPONENT . '/models/article.php';

/**
 * THM_GroupsModelUser_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsModelArticles extends THM_CoreModelList
{

    protected $defaultOrdering = "id";

    protected $defaultDirection = "ASC";

    protected $defaultLimit = "20";

    protected $defaultFilters = array();

    /**
     * Constructor
     *
     * @param   array  $config  config array
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(

            );
        }

        // Get user quickpages root category and show on start
        $this->defaultFilters = array('catid' => THMLibThmQuickpages::getCategoryByProfileData(array('Id' => JFactory::getUser()->id)));

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $uid = JFactory::getUser()->id;

        $query
            ->select('a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid, a.state, a.access, a.created, a.created_by, a.ordering, a.featured, a.language, a.hits, a.publish_up, a.publish_down,l.title AS language_title,uc.name AS editor,ag.title AS access_level,c.title AS category_title,ua.name AS author_name')
            ->select('d.featured, d.published')
            ->from('#__content AS a')
            ->leftJoin('#__languages AS l ON l.lang_code = a.language')
            ->leftJoin('#__users AS uc ON uc.id=a.checked_out')
            ->leftJoin('#__viewlevels AS ag ON ag.id = a.access')
            ->leftJoin('#__categories AS c ON c.id = a.catid')
            ->leftJoin('#__users AS ua ON ua.id = a.created_by')
            ->leftJoin('#__thm_groups_users_categories AS qc ON qc.categoriesID = a.catid')
            ->leftJoin('#__thm_groups_users_content AS d ON d.contentID = a.id');
            //->where('(a.created_by = ' . ((int) $uid) . ' OR qc.id = ' . ((int) $uid) . ')');


        $this->setSearchFilter($query, array('a.title', 'a.alias'));
        $this->setIDFilter($query, 'a.catid', array('filter.catid'));
        $this->setIDFilter($query, 'a.state', array('filter.stateid'));
        $this->setIDFilter($query, 'd.published', array('filter.published'));
        $this->setIDFilter($query, 'd.featured', array('filter.featured'));

        $this->setOrdering($query);

        echo '<pre>';
        //print_r($query->dump());
        echo '</pre>';

        return $query;
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        $items = parent::getItems();

        if (empty($items))
        {
            return false;
        }

        $index = 0;
        $return['attributes'] = array('class' => 'ui-sortable');
        foreach ($items as $key => $item)
        {
            $canChange = $this->hasUserRightTo('EditState', $item);
            $archived = $this->state->get('filter.published') == 2 ? true : false;
            $trashed = $this->state->get('filter.published') == -2 ? true : false;

            $listOrder = $this->state->get('list.ordering');
            $saveOrder = $listOrder == 'a.ordering';
            $iconClass = '';

            if (!$canChange)
            {
                $iconClass = ' inactive';
            }
            elseif (!$saveOrder)
            {
                $iconClass = ' inactive tip-top hasTooltip';
            }

            $action = $archived ? 'unarchive' : 'archive';
            JHtml::_('actionsdropdown.' . $action, 'cb' . $key, 'articles');
            $action = $trashed ? 'untrash' : 'trash';
            JHtml::_('actionsdropdown.' . $action, 'cb' . $key, 'articles');

            $url = JRoute::_('index.php?option=com_content&task=article.edit&a_id=' . $item->id);
            $return[$index] = array();

            $order = '';
            if ($canChange && $saveOrder)
            {
                $order = '<input type="text" style="display:none" name="order[]" size="5" value="'
                    . $item->ordering . '" class="width-20 text-area-order " />';
            }

            $publishedBtn = JHtml::_('jgrid.published', $item->state, $key, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down);
            $dropdownBtn = JHtml::_('actionsdropdown.render', $item->title);

            $return[$index]['attributes'] = array( 'class' => 'order nowrap center', 'id' => $item->id);
            $return[$index]['ordering']['attributes'] = array( 'class' => "order nowrap center", 'style' => "width: 40px;");
            $return[$index]['ordering']['value']
                = "<span class='sortable-handler$iconClass'><i class='icon-menu'></i></span>" . $order;
            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $this->renderTitle($item);
            $return[$index][2] = "<div class='btn-group'>$publishedBtn . $dropdownBtn</div>";
            $return[$index][4] = JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC4'));
            $return[$index][5] = (int) $item->hits;
            $return[$index][6] = $this->renderCheckInAndEditIcons($key, $item);
            $return[$index][7] = $this->renderTrashIcon($key, $item);
            $return[$index][8] = $this->getToggle($item->id, $item->published, 'articles', '', 'published');
            $return[$index][9] = $this->getToggle($item->id, $item->featured, 'articles', '', 'featured');
            $return[$index][10] = $item->category_title;

            $index++;
        }
        return $return;
    }

    /**
     * Returns a title of an article
     *
     * @param   object  &$item  An object item
     *
     * @return  string
     */
    public function renderTitle(&$item)
    {
        if ($item->state > 0)
        {
            $additionalURLParams = array('gsuid' => $item->created_by);
            return JHTML::_('link', THMLibThmQuickpages::getQuickpageRoute($item, '&' . http_build_query($additionalURLParams)), $item->title);
        }
        else
        {
            return $item->title;
        }
    }

    /**
     * Renders checkin and edit icons
     *
     * @param   int     $key    An index of an item
     *
     * @param   object  &$item  An object item
     *
     * @return  mixed|string
     */
    public function renderCheckInAndEditIcons($key, &$item)
    {
        $canEdit = $this->hasUserRightTo('Edit', $item);
        $canCheckin = $this->hasUserRightTo('Checkin', $item);
        $return = '';

        // Output checkin icon
        if ($item->checked_out)
        {
            return JHtml::_('jgrid.checkedout', $key, $item->editor, $item->checked_out_time, 'articles.', $canCheckin);
        }

        // Output edit icon
        if ($canEdit)
        {
            $itemId = JFactory::getApplication()->input->getInt('Itemid', 0);
            $returnURL = base64_encode("index.php?option=com_thm_groups&view=articles&Itemid=$itemId");
            $editURL = 'index.php/' . $item->alias . '?task=article.edit&a_id=' . $item->id . '&return=' . $returnURL;
            $imgSpanTag = '<span class="state edit" style=""><span class="text">Edit</span></span>';

            $return .= JHTML::_('link', $editURL, $imgSpanTag, 'title="'
                . JText::_('COM_THM_QUICKPAGES_HTML_EDIT_ITEM')
                . '" class="jgrid"'
            );
            $return .= "\n";
        }
        else
        {
            $return = '<span class="jgrid"><span class="state edit_disabled"><span class="text">Edit</span></span></span>';
        }

        return $return;
    }

    /**
     * Returns an output icon
     *
     * @param   int     $key    An index of an item
     *
     * @param   object  &$item  An item object
     *
     * @return mixed
     */
    public function renderTrashIcon($key, &$item)
    {
        $canDelete	= $this->hasUserRightTo('Delete', $item);
        if ($item->state >= 0)
        {
            // Define state changes needed by JHtmlJGrid.state(), see also JHtmlJGrid.published()
            $states	= array(
                0	=> array(),		// Dummy: Wird nicht gebraucht, erzeugt aber sonst Notice
                3	=> array(
                    'trash',
                    'JPUBLISHED',
                    'COM_THM_QUICKPAGES_HTML_TRASH_ITEM',
                    'JPUBLISHED',
                    false,
                    'trash',
                    'trash_disabled'
                ),
                -3	=> array(
                    'publish',
                    'JTRASHED',
                    'COM_THM_QUICKPAGES_HTML_UNTRASH_ITEM',
                    'JTRASHED',
                    false,
                    'untrash',
                    'untrash'
                ),
            );
            $button = JHtml::_('jgrid.state', $states, ($item->state < 0 ? -3 : 3), $key, 'articles.', $canDelete);
            $button = str_replace(
                "onclick=\"", "onclick=\"if (confirm('" . JText::_('COM_THM_GROUPS_REALLY_DELETE') . "')) ", $button
            );
            return $button;
        }
    }

    /**
     * Returns a button for creating of a new article
     *
     * @return mixed|string
     */
    public function getCreateNewArticleButton()
    {
        // Check for authorization to create article in current category
        $currCategoryID = THMLibThmQuickpages::getCategoryByProfileData(array('Id' => JFactory::getUser()->id));
        $canCreate = $this->hasUserRightToCreateArticle($currCategoryID);

        if ($canCreate AND $currCategoryID != 0)
        {
            $itemId = JFactory::getApplication()->input->getInt('Itemid', 0);
            $returnURL = base64_encode("index.php?option=com_thm_groups&view=articles&Itemid=$itemId");
            $addURL = JRoute::_('index.php?option=com_content&view=form&layout=edit&catid='
                . $currCategoryID . '&return=' . $returnURL
            );

            return JHTML::_('link', $addURL, '<i class="icon-new"></i> ' . JText::_('COM_THM_GROUPS_QUICKPAGES_CREATE_NEW_ARTICLE'), 'title="'
                . JText::_('COM_THM_QUICKPAGES_HTML_CREATE')
                . '" class="btn btn-success btn-lg"'
            );

        }
        else
        {
            return '<span class="qp_icon_big qp_create_icon_disabled"><span class="qp_invisible_text">' .
             JText::_('COM_THM_GROUPS_QUICKPAGES_CREATE_NEW_ARTICLE') . '</span></span>';
        }
    }

    /**
     * Method to test whether the session user
     * has the permission to create a new article.
     *
     * @param   int  $categoryID  The category id to create the article in.
     *
     * @return	boolean	True if permission granted.
     */
    protected function hasUserRightToCreateArticle($categoryID)
    {
        $articleModel = new THM_GroupsModelArticle;

        return $articleModel->canCreate($categoryID);
    }

    /**
     * Function to get table headers
     *
     * @return array including headers
     */
    public function getHeaders()
    {
        $ordering = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers = array();
        $headers['ordering'] =  JHtml::_('searchtools.sort', '', 'a.ordering', $direction, $ordering , null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox'] = '';
        $headers['title'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_TITLE'), 'a.title', $direction, $ordering);
        $headers['stateid'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_PUBLISHED'), 'published', $direction, $ordering);
        //$headers['ordering'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_ORDERING'), 'ordering', $direction, $ordering);
        $headers['data'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_DATA'), 'created', $direction, $ordering);
        $headers['hits'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_HITS'), 'hits', $direction, $ordering);
        $headers['edit'] = JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_EDIT');
        $headers['delete'] = JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_DELETE');
        $headers['published'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_SHOW_LIST'), 'd.published', $direction, $ordering);
        $headers['featured'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_SHOW_CONTENT'), 'd.featured', $direction, $ordering);
        //$headers['published'] = JHtml::_('searchtools.sort', JText::_('Show L.M.'), 'd.published', $direction, $ordering);
        //$headers['featured'] = JHtml::_('searchtools.sort', JText::_('Show C.M.'), 'd.featured', $direction, $ordering);
        $headers['catid'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_CATEGORY'), 'catid', $direction, $ordering);

        return $headers;
    }

    /**
     * Method to test whether the session user
     * has the permission to do something with an article.
     *
     * @param   Strig   $rightName    The right name
     * @param   object  $articleItem  A article record object.
     *
     * @return	boolean	True if permission granted.
     */
    protected function hasUserRightTo($rightName, $articleItem)
    {
        $methodName = 'can' . $rightName;

        $articleModel = new THM_GroupsModelArticle;

        if (method_exists($articleModel, $methodName))
        {
            return $articleModel->$methodName($articleItem);
        }

        return false;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
     *
     * @since   12.2
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        return $user->authorise('core.edit.state', 'com_content');
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array    $pks    An array of primary key ids.
     * @param   integer  $order  +1 or -1
     *
     * @return  mixed
     *
     * @since   12.2
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
            return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
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
     * @since   12.2
     */
    protected function getReorderConditions($table)
    {
        return array();
    }

    /**
     * Method to change the published state of one or more records.
     *
     * @param   array    &$pks   A list of the primary keys to change.
     * @param   integer  $value  The value of the published state.
     *
     * @return  boolean  True on success.
     *
     * @since   12.2
     */
    public function publish(&$pks, $value = 1)
    {
        $dispatcher = JEventDispatcher::getInstance();
        $user = JFactory::getUser();
        JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
        $table = $this->getTable('Content', 'JTable');
        $pks = (array) $pks;

        // Include the plugins for the change of state event.
        JPluginHelper::importPlugin($this->events_map['change_state']);

        // Access checks.
        foreach ($pks as $i => $pk)
        {
            $table->reset();

            if ($table->load($pk))
            {
                if (!$this->canEditState($table))
                {
                    // Prune items that you can't change.
                    unset($pks[$i]);
                    JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

                    return false;
                }
            }
        }

        // Attempt to change the state of the records.
        if (!$table->publish($pks, $value, $user->get('id')))
        {
            $this->setError($table->getError());

            return false;
        }

        $context = $this->option . '.' . $this->name;

        // Trigger the change state event.
        $result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

        if (in_array(false, $result, true))
        {
            $this->setError($table->getError());

            return false;
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

}
