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

use Joomla\Registry\Registry;

require_once HELPERS . 'content.php';

/**
 * Content Model
 */
class THM_GroupsModelContent extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_content.article';

	/**
	 * Method to check a row in if the necessary properties/fields exist. Checking a row in will allow other users the
	 * ability to edit the row.
	 *
	 * @return  bool  true on success, otherwise false
	 * @throws Exception
	 */
	public function checkIn()
	{
		$selectedArray = JFactory::getApplication()->input->get('cid', [], 'array');

		if (empty($selectedArray) or empty($selectedArray[0]))
		{
			return true;
		}

		$table = JTable::getInstance('Content', 'JTable');

		return $table->checkIn($selectedArray[0]);
	}

	/**
	 * Retrieves the content for the given ID.
	 *
	 * @param $contentID
	 *
	 * @return mixed object on success, otherwise false
	 */
	private function getContent($contentID)
	{
		$user  = JFactory::getUser();
		$dbo   = $this->getDbo();
		$query = $dbo->getQuery(true);

		$query->select(
			$this->getState(
				'item.select', 'art.id, art.asset_id, art.title, art.alias, art.introtext, art.fulltext, ' .
				'art.state, art.catid, art.created, art.created_by, art.created_by_alias, ' .
				// Use created if modified is 0
				'CASE WHEN art.modified = ' . $dbo->quote($dbo->getNullDate()) . ' THEN art.created ELSE art.modified END as modified, ' .
				'art.modified_by, art.checked_out, art.checked_out_time, art.publish_up, art.publish_down, ' .
				'art.images, art.urls, art.attribs, art.version, art.ordering, ' .
				'art.metakey, art.metadesc, art.access, art.hits, art.metadata, art.featured, art.language, art.xreference'
			)
		);
		$query->from('#__content AS art')->where('art.id = ' . (int) $contentID);

		// Join on category table.
		$query->select('cat.title AS category_title, cat.alias AS category_alias, cat.access AS category_access');
		$query->innerJoin('#__categories AS cat on cat.id = art.catid')->where('cat.published > 0');

		// Join on user table.
		$query->select('usr.name AS author')->join('LEFT', '#__users AS usr on usr.id = art.created_by');

		// Join over the categories to get parent category titles
		$query->select('parent.title AS parent_title, parent.id AS parent_id, parent.path AS parent_route, parent.alias AS parent_alias');
		$query->join('LEFT', '#__categories as parent ON parent.id = cat.parent_id');

		//TODO: does anyone need this?
		// Join on voting table
		$query->select('ROUND(v.rating_sum / v.rating_count, 0) AS rating, v.rating_count AS rating_count');
		$query->join('LEFT', '#__content_rating AS v ON art.id = v.content_id');

		$canEdit      = $user->authorise('core.edit', 'com_content');
		$canEditState = $user->authorise('core.edit.state', 'com_content');
		$cannotEdit   = (!$canEditState and !$canEdit);

		// If they cannot edit they need only be interested in published items.
		if ($cannotEdit)
		{
			// Filter by start and end dates.
			$nullDate = $dbo->quote($dbo->getNullDate());
			$date     = JFactory::getDate();
			$nowDate  = $dbo->quote($date->toSql());

			$query->where('(art.publish_up = ' . $nullDate . ' OR art.publish_up <= ' . $nowDate . ')');
			$query->where('(art.publish_down = ' . $nullDate . ' OR art.publish_down >= ' . $nowDate . ')');
		}

		// Filter by published state.
		$published = $this->getState('filter.published');
		$archived  = $this->getState('filter.archived');

		if (is_numeric($published) or is_numeric($archived))
		{
			$query->where('(art.state = ' . (int) $published . ' OR art.state =' . (int) $archived . ')');
		}

		$dbo->setQuery($query);

		try
		{
			$content = $dbo->loadObject();
		}
		catch (Exception $exception)
		{
			if ($exception->getCode() == 404)
			{
				// Need to go thru the error handler to allow Redirect to work.
				JError::raiseError($exception->getCode(), $exception->getMessage());
			}
			else
			{
				$this->setError($exception);

				return false;
			}
		}

		return empty($content) ? false : $content;
	}

	/**
	 * Method to get content data.
	 *
	 * @return  object|boolean|JException  Menu item data object on success, boolean false or JException instance on error
	 * @throws Exception
	 */
	public function getItem()
	{
		$contentID = JFactory::getApplication()->input->get('id');
		$contentID = (!empty($contentID)) ? $contentID : (int) $this->getState('article.id');

		if (empty($this->_item))
		{
			$this->_item = [];
		}

		if (!isset($this->_item[$contentID]))
		{
			$content = $this->getContent($contentID);

			if (empty($content))
			{
				return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
			}

			// Filter by published state.
			$published = $this->getState('filter.published');
			$archived  = $this->getState('filter.archived');

			$wrongPublishState = (is_numeric($published) and $content->state != $published);
			$wrongArchiveState = (is_numeric($archived) and $content->state != $archived);

			// Check for published state if filter set.
			if ($wrongPublishState and $wrongArchiveState)
			{
				return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
			}

			// Convert parameter fields to objects.
			$registry = new Registry($content->attribs);

			$content->params = clone $this->getState('params');
			$content->params->merge($registry);

			$content->metadata = new Registry($content->metadata);

			$this->setEditAccess($content);
			$this->setViewAccess($content);

			// Add router helpers.
			$content->slug        = empty($content->alias) ? $content->id : "$content->id:$content->alias";
			$content->catslug     = empty($content->category_alias) ? $content->catid : "$content->catid:$content->category_alias";
			$content->parent_slug = empty($content->parent_alias) ? $content->parent_id : "$content->parent_id:$content->parent_alias";

			// No link for ROOT category
			if ($content->parent_alias === 'root')
			{
				$content->parent_slug = null;
			}

			$content->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($content->slug, $content->catslug));

			if ($content->params->get('show_associations'))
			{
				$content->associations = ContentHelperAssociation::displayAssociations($content->id);
			}

			$this->_item[$contentID] = $content;
		}

		return $this->_item[$contentID];
	}

	/**
	 * Increment the hit counter for the article.
	 *
	 * @param   integer  $contentID  Optional primary key of the article to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($contentID = 0)
	{
		$contentID = (!empty($contentID)) ? $contentID : (int) $this->getState('article.id');

		$table = JTable::getInstance('Content', 'JTable');
		$table->load($contentID);
		$table->hit($contentID);

		return true;
	}

	/**
	 * Method to populate the model state.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$contentID = $app->input->getInt('id');
		$this->setState('article.id', $contentID);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		// TODO: Tune these values based on other permissions.
		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_content')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

	/**
	 * Method to change the core published state of THM Groups articles.
	 *
	 * @return  boolean  true on success, otherwise false
	 * @throws Exception
	 */
	public function publish()
	{
		return THM_GroupsHelperContent::publish();
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array  $contentIDs  an array of primary content ids
	 * @param   array  $order       the order for the content items
	 *
	 * @return  mixed
	 * @throws Exception
	 */
	public function saveorder($contentIDs = null, $order = null)
	{
		return THM_GroupsHelperContent::saveorder($contentIDs, $order);
	}

	/**
	 * Sets the user's edit access rights to the content.
	 *
	 * @param   object  $content  the content being created.
	 *
	 * @return void modifies the content object.
	 */
	private function setEditAccess(&$content)
	{
		$user = JFactory::getUser();

		if ($user->get('guest'))
		{
			return;
		}

		$asset = 'com_content.article.' . $content->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			$content->params->set('access-edit', true);

			return;
		}

		if ($user->id != $content->created_by)
		{
			return;
		}

		// Now check if edit.own is available.
		if ($user->authorise('core.edit.own', $asset))
		{
			$content->params->set('access-edit', true);

			return;
		}
	}

	/**
	 * Sets the user's view access rights to the content.
	 *
	 * @param   object  $content  the content being created.
	 *
	 * @return void modifies the content object.
	 */
	private function setViewAccess(&$content)
	{
		// Compute view access permissions.
		if ($access = $this->getState('filter.access'))
		{
			// If the access filter has been set, we already know this user can view.
			$content->params->set('access-view', true);

			return;
		}

		// If no access filter is set, the layout takes some responsibility for display of limited information.
		$user          = JFactory::getUser();
		$groups        = $user->getAuthorisedViewLevels();
		$contentAccess = in_array($content->access, $groups);

		if ($content->catid == 0 or $content->category_access === null)
		{
			$content->params->set('access-view', $contentAccess);

			return;
		}

		$categoryAccess = in_array($content->category_access, $groups);
		$content->params->set('access-view', $contentAccess and $categoryAccess);
	}

	/**
	 * Toggles THM Groups article attributes like 'published' and 'featured'
	 *
	 * @return  mixed  integer on success, otherwise false
	 * @throws Exception
	 */
	public function toggle()
	{
		return THM_GroupsHelperContent::toggle();
	}
}
