<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_SITE . '/media/com_thm_groups/models/list.php';
require_once JPATH_SITE . '/media/com_thm_groups/helpers/content.php';

/**
 * THM_GroupsModelContent_Manager is a class which deals with the information preparation for the administrator view.
 */
class THM_GroupsModelContent_Manager extends THM_GroupsModelList
{

    protected $defaultOrdering = 'users.name';

    protected $defaultDirection = 'ASC';

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $query = $this->_db->getQuery(true);

        $rootCategory = THM_GroupsHelperContent::getRootCategory();

        if (empty($rootCategory)) {
            return $query;
        }

        $query->select('content.*')
            ->select('users.name AS author_name')
            ->select('pContent.featured as featured')
            ->from('#__content AS content')
            ->innerJoin('#__thm_groups_content AS pContent ON pContent.id = content.id')
            ->innerJoin('#__categories AS cCats ON cCats.id = content.catid')
            ->innerJoin('#__thm_groups_categories AS pCats ON pCats.id = cCats.id')
            ->innerJoin('#__users AS users ON users.id = pCats.profileID')
            ->where("cCats.parent_id= '$rootCategory' ");

        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $query->where("(content.title LIKE '%" . implode("%' OR content.title LIKE '%",
                    explode(' ', $search)) . "%')");
        }

        $authorID = $this->getState('filter.author');
        if (!empty($authorID)) {
            $query->where("users.id = '$authorID'");
        }

        $featured = $this->getState('filter.featured');
        if (isset($featured) and $featured == '0') {
            $query->where("(pContent.featured = '0' OR pContent.featured IS NULL)");
        } elseif ($featured == '1') {
            $query->where("pContent.featured = '1'");
        }

        $state = $this->getState('filter.status');
        if (is_numeric($state)) {
            $query->where('content.state = ' . (int)$state);
        }

        $this->setOrdering($query);

        return $query;
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        $rootCategory = THM_GroupsHelperContent::getRootCategory();

        $return = [];

        if (!empty($rootCategory)) {
            $items = parent::getItems();
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ROOT_CATEGORY_NOT_CONFIGURED'),
                'notice');

            return $return;
        }

        if (empty($items)) {
            return $return;
        }

        $generalOrder    = '<input type="text" style="display:none" name="order[]" size="5" ';
        $generalOrder    .= 'value="XX" class="width-20 text-area-order " />';
        $generalSortIcon = '<span class="sortable-handlerXXX"><i class="icon-menu"></i></span>';
        $canSort         = JFactory::getUser()->authorise('core.edit', 'com_thm_groups');
        $orderingActive  = $this->state->get('list.ordering') == 'content.ordering';
        $user            = JFactory::getUser();

        $index = 0;

        foreach ($items as $item) {
            $canEdit   = $user->authorise('core.edit', 'com_content.article.' . $item->id);
            $iconClass = '';

            if (!$canEdit) {
                $iconClass = ' inactive';
            } elseif (!$orderingActive) {
                $iconClass = ' inactive tip-top hasTooltip';
            }

            $specificOrder = ($canSort and $orderingActive) ? str_replace('XX', $item->ordering, $generalOrder) : '';

            $return[$index] = [];

            $return[$index]['attributes'] = ['class' => 'order nowrap center', 'id' => $item->id];

            $return[$index]['ordering']['attributes'] = ['class' => "order nowrap center", 'style' => "width: 40px;"];
            $return[$index]['ordering']['value']      = str_replace('XXX', $iconClass,
                    $generalSortIcon) . $specificOrder;

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);

            $canEdit = JFactory::getUser()->authorise('core.edit', 'com_content.article.' . $item->id);

            if ($canEdit) {
                $url               = JRoute::_('index.php?option=com_content&task=article.edit&id=' . $item->id);
                $return[$index][1] = JHtml::link($url, $item->title, ['target' => '_blank']);
            } else {
                $return[$index][1] = $item->title;
            }

            $return[$index][2] = $item->author_name;
            $return[$index][3] = $this->getToggle($item->id, $item->featured, 'content', '', 'featured');
            $return[$index][4] = THM_GroupsHelperContent::getStatusDropdown($index, $item);
            $return[$index][5] = $item->id;

            $index++;
        }

        return $return;
    }

    /**
     * Function to get table headers
     *
     * @return array including headers
     */
    public function getHeaders()
    {
        $ordering  = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers             = [];
        $headers['order']    = JHtml::_('searchtools.sort', '', 'content.ordering', $direction, $ordering, null, 'asc',
            'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox'] = '';
        $headers['title']    = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_TITLE', 'title', $direction, $ordering);
        $headers['author']   = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_AUTHOR', 'author_name', $direction,
            $ordering);
        $headers['featured'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_PROFILE_MENU', 'pContent.featured',
            $direction,
            $ordering);
        $headers['status']   = JHtml::_('searchtools.sort', 'JSTATUS', 'content.state', $direction, $ordering);
        $headers['id']       = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'content.id', $direction,
            $ordering);

        return $headers;
    }

    /**
     * Returns custom hidden fields for page
     *
     * @return array
     */
    public function getHiddenFields()
    {
        return [];
    }
}
