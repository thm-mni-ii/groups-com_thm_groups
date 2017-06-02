<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

jimport('thm_core.list.model');
jimport('thm_groups.data.lib_thm_groups_quickpages');
require_once JPATH_SITE . '/media/com_thm_groups/helpers/quickpage.php';

/**
 * THM_GroupsModelQuickpage_Manager is a class which deals with the information
 * preparation for the administrator view. This view represents the user specific content/articles
 * named Quickpages.
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsModelQuickpage_Manager extends THM_CoreModelList
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
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$rootCategory = THMLibThmQuickpages::getQuickpagesRootCategory();

		if (empty($rootCategory))
		{
			return $query;
		}

		$contentSelect = 'content.id, content.title, content.alias, content.checked_out, content.checked_out_time, ';
		$contentSelect .= 'content.catid, content.state, content.access, content.created, content.featured, ';
		$contentSelect .= 'content.created_by, content.ordering, content.language, content.hits, content.publish_up, ';
		$contentSelect .= 'content.publish_down';
		$query->select($contentSelect);
		$query->select('language.title AS language_title');
		$query->select('ag.title AS access_level');
		$query->select('cats.title AS category_title');
		$query->select('users.name AS author_name');

		// TODO: Apparently these are VERY poorly named module parameters. RENAME THESE!
		$query->select('qps.featured as qp_featured, qps.published as qp_published');
		$query->from('#__content AS content');
		$query->leftJoin('#__languages AS language ON language.lang_code = content.language');
		$query->leftJoin('#__viewlevels AS ag ON ag.id = content.access');
		$query->leftJoin('#__categories AS cats ON cats.id = content.catid');
		$query->leftJoin('#__users AS users ON users.id = content.created_by');
		$query->leftJoin('#__thm_groups_users_content AS qps ON qps.contentID = content.id');

		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$query->where("(content.title LIKE '%" . implode("%' OR content.title LIKE '%", explode(' ', $search)) . "%')");
		}

		$query->where("cats.parent_id= '$rootCategory' ");

		$userID = $this->getState('filter.author');
		if (!empty($userID))
		{
			$query->where("users.id = '$userID'");
		}

		$featured = $this->getState('filter.featured');
		if (isset($featured) AND $featured === 0)
		{
			$query->where("(qps.featured = '0' OR qps.featured IS NULL)");
		}
		elseif ($featured === 1)
		{
			$query->where("qps.featured = '1'");
		}

		$published = $this->getState('filter.published');
		if (isset($published) AND $published === 0)
		{
			$query->where("(qps.published = '0' OR qps.published IS NULL)");
		}
		elseif ($published === 1)
		{
			$query->where("qps.published = '1'");
		}

		$state = $this->getState('filter.status');
		if (is_numeric($state))
		{
			$query->where('content.state = ' . (int) $state);
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
		$rootCategory = THMLibThmQuickpages::getQuickpagesRootCategory();

		$return = array();

		if (!empty($rootCategory))
		{
			$items = parent::getItems();
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ROOT_CATEGORY_NOT_CONFIGURED'), 'notice');

			return $return;
		}

		if (empty($items))
		{
			return $return;
		}

		$generalOrder    = '<input type="text" style="display:none" name="order[]" size="5" ';
		$generalOrder    .= 'value="XX" class="width-20 text-area-order " />';
		$generalSortIcon = '<span class="sortable-handlerXXX"><i class="icon-menu"></i></span>';
		$canSort         = JFactory::getUser()->authorise('core.edit', 'com_thm_groups');
		$orderingActive  = $this->state->get('list.ordering') == 'content.ordering';
		$user            = JFactory::getUser();

		$index = 0;
		foreach ($items as $item)
		{
			$canEdit   = $user->authorise('core.edit', 'com_content.article.' . $item->id);
			$iconClass = '';

			if (!$canEdit)
			{
				$iconClass = ' inactive';
			}
			elseif (!$orderingActive)
			{
				$iconClass = ' inactive tip-top hasTooltip';
			}

			$specificOrder = ($canSort AND $orderingActive) ? str_replace('XX', $item->ordering, $generalOrder) : '';

			$return[$index] = array();

			$return[$index]['attributes'] = array('class' => 'order nowrap center', 'id' => $item->id);

			$return[$index]['ordering']['attributes'] = array('class' => "order nowrap center", 'style' => "width: 40px;");
			$return[$index]['ordering']['value']      = str_replace('XXX', $iconClass, $generalSortIcon) . $specificOrder;

			$return[$index][0] = JHtml::_('grid.id', $index, $item->id);

			$canEdit = JFactory::getUser()->authorise('core.edit', 'com_content.article.' . $item->id);
			if ($canEdit)
			{
				$url               = JRoute::_('index.php?option=com_content&task=article.edit&id=' . $item->id);
				$return[$index][1] = JHtml::link($url, $item->title);
			}
			else
			{
				$return[$index][1] = $item->title;
			}

			$return[$index][2] = $item->author_name;
			$return[$index][3] = $this->getToggle($item->id, $item->qp_published, 'quickpage', '', 'published');
			$return[$index][4] = $this->getToggle($item->id, $item->qp_featured, 'quickpage', '', 'featured');
			$return[$index][5] = $this->getStatusDropdown($index, $item);
			$return[$index][6] = $item->id;

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

		$headers              = array();
		$headers['order']     = JHtml::_('searchtools.sort', '', 'content.ordering', $direction, $ordering, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');
		$headers['checkbox']  = '';
		$headers['title']     = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_TITLE', 'title', $direction, $ordering);
		$headers['author']    = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_AUTHOR', 'author_name', $direction, $ordering);
		$headers['published'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_PUBLISH', 'qp_published', $direction, $ordering);
		$headers['featured']  = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_PROFILE_MENU', 'qp_featured', $direction, $ordering);
		$headers['status']    = JHtml::_('searchtools.sort', 'JSTATUS', 'content.state', $direction, $ordering);
		$headers['id']        = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'content.id', $direction, $ordering);

		return $headers;
	}

	/**
	 * Returns custom hidden fields for page
	 *
	 * @return array
	 */
	public function getHiddenFields()
	{
		return array();
	}

	/**
	 * Returns dropdown for changing content status
	 *
	 * @param   int    $index Current row index of an item
	 * @param   object $item  Item for which a dropdown will be created
	 *
	 * @return  string
	 */
	private function getStatusDropdown($index, $item)
	{
		$canChange = THM_GroupsHelperQuickpage::canEditState($item->id);

		$controllerName = 'quickpage';

		$status = '<div class="btn-group">';
		$status .= JHtml::_('jgrid.published', $item->state, $index, "$controllerName.", $canChange, 'cb', $item->publish_up, $item->publish_down);

		$archived = $item->state == 2 ? true : false;
		$action   = $archived ? 'unarchive' : 'archive';
		$status   .= JHtml::_('actionsdropdown.' . $action, 'cb' . $index, $controllerName);

		$trashed = $item->state == -2 ? true : false;
		$action  = $trashed ? 'untrash' : 'trash';
		$status  .= JHtml::_('actionsdropdown.' . $action, 'cb' . $index, $controllerName);

		$status .= JHtml::_('actionsdropdown.render', JFactory::getDbo()->escape($item->title));
		$status .= "</div>";

		return $status;
	}
}
