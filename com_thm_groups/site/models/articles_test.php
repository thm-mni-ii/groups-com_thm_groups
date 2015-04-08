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
class THM_GroupsModelArticles_Test extends THM_CoreModelList
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
        print_r($query->__toString());
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
        foreach ($items as $key => $item)
        {
            $canChange = $this->hasUserRightTo('EditState', $item);
            $url = JRoute::_('index.php?option=com_content&task=article.edit&a_id=' . $item->id);
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $this->renderTitle($item);
            $return[$index][2] = JHtml::_('jgrid.published', $item->state, $key, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down);
            $return[$index][3] = $item->ordering;
            $return[$index][4] = JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC4'));
            $return[$index][5] = (int) $item->hits;
            $return[$index][6] = $this->renderCheckInAndEditIcons($key, $item);
            $return[$index][7] = $this->renderTrashIcon($key, $item);
            $return[$index][8] = $this->getToggle($item->id, $item->published, 'articles_test', '', 'published');
            $return[$index][9] = $this->getToggle($item->id, $item->featured, 'articles_test', '', 'featured');
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
            $editURL = JRoute::_('index.php?option=com_content&task=article.edit&a_id=' . $item->id);
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
            echo '<pre>';
            print_r("YES");
            echo '</pre>';
            $editURL = JRoute::_('index.php?option=com_content&view=form&layout=edit&catid='
                . $currCategoryID
            );

            return JHTML::_('link', $editURL, '<i class="icon-new"></i> ' . JText::_('COM_THM_GROUPS_QUICKPAGES_CREATE_NEW_ARTICLE'), 'title="'
                . JText::_('COM_THM_QUICKPAGES_HTML_CREATE')
                . '" class="btn btn-success btn-lg"'
            );

        }
        else
        {
            echo '<pre>';
            print_r("NO");
            echo '</pre>';
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
        $headers['checkbox'] = '';
        $headers['title'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_TITLE'), 'a.title', $direction, $ordering);
        $headers['stateid'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_PUBLISHED'), 'published', $direction, $ordering);
        $headers['ordering'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_ORDERING'), 'ordering', $direction, $ordering);
        $headers['data'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_DATA'), 'created', $direction, $ordering);
        $headers['hits'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_HITS'), 'hits', $direction, $ordering);
        $headers['edit'] = JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_EDIT');
        $headers['delete'] = JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_DELETE');
        $headers['published'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_SHOW_LIST'), 'd.published', $direction, $ordering);
        $headers['featured'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QUICKPAGES_ARTICLES_SHOW_CONTENT'), 'd.featured', $direction, $ordering);
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
}
