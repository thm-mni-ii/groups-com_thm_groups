<?php

/**
 * @category    Joomla library
 * @package     THM_Quickpages
 * @subpackage  lib_thm_groups_quickpages
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

jimport('joomla.application.component.helper');
jimport('joomla.application.categories');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/content.php';

/**
 * Library of the Quickpages
 *
 * @category  Joomla.Library
 * @package   thm_quickpages
 * @since     v1.2.0
 */
class THM_GroupsQuickpagesData
{
	/**
	 * Returns the ID of the mapped category for the given user ID
	 *
	 * @param   int $profileID An array of all information to identify profile id and kind
	 *
	 * @return  mixed int the category ID on success, otherwise false
	 */
	private static function getCategoryByProfileData($profileID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);
		$query->select('categoriesID')->from('#__thm_groups_users_categories')->where("usersID = '$profileID'");
		$dbo->setQuery($query);

		try
		{
			$categoryID = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return empty($categoryID) ? false : $categoryID;
	}

	/**
	 * Creates a subcategory for a user
	 *
	 * @param   int    $profileID An user id
	 * @param   string $catTitle  A title of a new category
	 */
	public static function createQuickpageSubcategoryForProfile($profileID, $catTitle)
	{
		$user        = JFactory::getUser($profileID);
		$catAlias    = JFilterOutput::stringURLSafe($catTitle);
		$parentCatID = self::getCategoryByProfileData($profileID);

		if ($parentCatID > 0)
		{
			// Create category and get its ID
			$categoryID = self::createCategory($catTitle, $catAlias, $parentCatID, $user->id);

			// Change created_user_id attribute in db, because of bug
			self::changeCategoryUser($user->id, $categoryID);

			// Map category to profile
			self::mapUserCategory($profileID, $categoryID);
		}
	}

	/**
	 * Returns the Path to a given article item.
	 * This is a modified rip of a com_content routine.
	 *
	 * @param   object $articleItem    The data row object of an article
	 * @param   string $additionParams Additional request parameters
	 *
	 * @see  com_content/helpers/route.php
	 *
	 * @return  string    The path
	 */
	public static function getQuickpageRoute($articleItem, $additionParams = '')
	{
		$id    = $articleItem->title ? ($articleItem->id . ':' . $articleItem->title) : $articleItem->id;
		$catid = $articleItem->catid;

		$itemID = JRequest::getVar('Itemid', 0);

		$app  = JFactory::getApplication();
		$menu = $app->getMenu();
		$menu->setActive($itemID);

		$needles = array(
			'article' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_thm_groups&view=singlearticle&id=' . $id;

		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('Content');
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$needles['category']   = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link                  .= '&catid=' . $catid;
			}
		}

		// TODO: bind Joomla's implementation here
		if ($item = self::_findItem($needles))
		{
			/** @noinspection PhpToStringImplementationInspection */
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem())
		{
			/** @noinspection PhpToStringImplementationInspection */
			$link .= '&Itemid=' . $item;
		}

		if (!empty($additionParams))
		{
			$link .= $additionParams;
		}

		return $link;
	}

	/**
	 * This is a rip of a com_content routine
	 *
	 * @param   array $needles Neddles
	 *
	 * @see com_content/helpers/route.php
	 *
	 * @return  mixed
	 */
	private static function _findItem($needles = null)
	{
		static $lookup;
		$app   = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if ($lookup === null)
		{
			$lookup = array();

			$component = JComponentHelper::getComponent('com_content');
			$items     = $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];

					if (!isset($lookup[$view]))
					{
						$lookup[$view] = array();
					}

					if (isset($item->query['id']))
					{
						$queryID = $item->query['id'];

						if (is_array($queryID))
						{
							$queryID = reset($item->query['id']);
						}

						$lookup[$view][$queryID] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset($lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset($lookup[$view][(int) $id]))
						{
							return $lookup[$view][(int) $id];
						}
					}
				}
			}
		}
		else
		{
			$active = $menus->getActive();

			if ($active && $active->component == 'com_content')
			{
				return $active->id;
			}
		}

		return null;
	}

	public static function getQPParams()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->select('params')
			->from('#__thm_groups_settings')
			->where('type = "quickpages"');

		$dbo->setQuery($query);

		try
		{
			$result = $dbo->loadObject();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return $result;
	}
}
