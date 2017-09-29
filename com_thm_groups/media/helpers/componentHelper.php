<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        provides functions useful to multiple component files
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing functions usefull to multiple component files
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperComponent
{
	/**
	 * Set variables for user actions.
	 *
	 * @param   object &$object the view context calling the function
	 *
	 * @return void
	 */
	public static function addActions(&$object)
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$path    = JPATH_ADMINISTRATOR . '/components/com_thm_groups/access.xml';
		$actions = JAccess::getActionsFromFile($path, "/access/section[@name='component']/");
		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, 'com_thm_groups'));
		}

		$object->actions = $result;
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   object &$view the view context calling the function
	 *
	 * @return void
	 */
	public static function addSubmenu(&$view)
	{
		$viewName = $view->get('name');

		// No submenu creation while editing a resource
		if (strpos($viewName, 'edit'))
		{
			return;
		}

		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_HOME'),
			'index.php?option=com_thm_groups&view=thm_groups',
			$viewName == 'thm_groups'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_CONTENT_MANAGER'),
			'index.php?option=com_thm_groups&view=content_manager',
			$viewName == 'content_manager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER'),
			'index.php?option=com_thm_groups&view=dynamic_type_manager',
			$viewName == 'dynamic_type_manager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_GROUP_MANAGER'),
			'index.php?option=com_thm_groups&view=group_manager',
			$viewName == 'group_manager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_PROFILE_MANAGER'),
			'index.php?option=com_thm_groups&view=profile_manager',
			$viewName == 'profile_manager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_TEMPLATE_MANAGER'),
			'index.php?option=com_thm_groups&view=template_manager',
			$viewName == 'template_manager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER'),
			'index.php?option=com_thm_groups&view=attribute_manager',
			$viewName == 'attribute_manager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_ROLE_MANAGER'),
			'index.php?option=com_thm_groups&view=role_manager',
			$viewName == 'role_manager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_THM_GROUPS_STATIC_TYPE_MANAGER'),
			'index.php?option=com_thm_groups&view=static_type_manager',
			$viewName == 'static_type_manager'
		);

		$view->sidebar = JHtmlSidebar::render();
	}

	/**
	 * Checks access for edit views
	 *
	 * @param   object &$model the model checking permissions
	 * @param   int    $itemID the id if the resource to be edited (empty for new entries)
	 *
	 * @return  bool  true if the user can access the edit view, otherwise false
	 */
	public static function allowEdit(&$model, $itemID = 0)
	{
		// Admins can edit anything. Department and monitor editing is implicitly covered here.
		$isAdmin = $model->actions->{'core.admin'};
		if ($isAdmin)
		{
			return true;
		}

		$name = $model->get('name');

		// Views accessible with component create/edit access
		$resourceEditViews = array('attribute_edit', 'dynamic_type_edit', 'profile_edit', 'role_edit', 'template_edit');
		if (in_array($name, $resourceEditViews))
		{
			if ((int) $itemID > 0)
			{
				return $model->actions->{'core.edit'};
			}

			return $model->actions->{'core.create'};
		}

		return false;
	}

	/**
	 * Calls the appropriate controller
	 *
	 * @param boolean $isAdmin whether the file is being called from the backend
	 *
	 * @return  void
	 */
	public static function callController($isAdmin = true)
	{
		$basePath = $isAdmin ? JPATH_COMPONENT_ADMINISTRATOR : JPATH_COMPONENT_SITE;

		$handler = explode(".", JFactory::getApplication()->input->getCmd('task', ''));

		if (count($handler) > 1)
		{
			$task = $handler[1];
		}
		else
		{
			$task = $handler[0];
		}

		/** @noinspection PhpIncludeInspection */
		require_once $basePath . '/controller.php';
		$controllerObj = new THM_GroupsController;
		$controllerObj->execute($task);
		$controllerObj->redirect();
	}

	/**
	 * Checks if the current user is a super admin in joomla or a admin of a thm_groups component. Content access rights
	 * are handled by com content.
	 *
	 * @return boolean  true if the user is a site or component administrator
	 */
	public static function canEdit()
	{
		$user               = JFactory::getUser();
		$isSuperUser        = $user->authorise('core.admin');
		$isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

		return ($isSuperUser OR $isComponentManager) ? true : false;
	}

	/**
	 * Method to check if the current user can edit the profile
	 *
	 * @param   int $profileID the id of the profile user
	 *
	 * @return  boolean  true if the current user is authorized to edit the profile, otherwise false
	 */
	public static function canEditProfile($profileID)
	{
		$user = JFactory::getUser();

		if (empty($user->id))
		{
			return false;
		}

		$isAdmin = ($user->authorise('core.admin', 'com_thm_groups') OR $user->authorise('core.manage', 'com_thm_groups'));

		if ($isAdmin)
		{
			return true;
		}

		$params  = JComponentHelper::getParams('com_thm_groups');
		$allowed = (!empty($profileID) AND $params->get('editownprofile', 0) == 1 AND $user->id == $profileID);

		return $allowed;
	}

	/**
	 * Clean the cache
	 *
	 * @return  void
	 */
	public static function cleanCache()
	{
		$conf = JFactory::getConfig();

		$options = array(
			'defaultgroup' => 'com_thm_groups',
			'cachebase'    => JFactory::getApplication()->isClient('administrator') ?
				JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
			'result'       => true,
		);

		try
		{
			$cache = JCache::getInstance('callback', $options);
			$cache->clean();
		}
		catch (JCacheException $exception)
		{
			$options['result'] = false;
		}
		// Set the clean cache event
		if (isset($conf['event_clean_cache']))
		{
			$event = $conf['event_clean_cache'];
		}
		else
		{
			$event = 'onContentCleanCache';
		}

		// Trigger the onContentCleanCache event.
		JEventDispatcher::getInstance()->trigger($event, $options);
	}

	/**
	 * Cleans a given collection. Converts to array as necessary. Removes duplicate values. Enforces int type. Removes
	 * 0 value indexes.
	 *
	 * @param mixed $array the collection to be cleaned (array|object)
	 *
	 * @return array the converted array
	 */
	public static function cleanIntCollection($array)
	{
		if (!is_array($array))
		{
			if (!is_object($array))
			{
				return array();
			}

			$array = Joomla\Utilities\ArrayHelper::fromObject($array);
		}

		$array = Joomla\Utilities\ArrayHelper::toInteger(array_filter(array_unique($array)));

		return $array;
	}

	/**
	 * Redirects to the homepage and displays a message about missing access rights
	 *
	 * @return  void
	 */
	public static function noAccess()
	{
		$app  = JFactory::getApplication();
		$msg  = JText::_('JLIB_RULES_NOT_ALLOWED');
		$link = JRoute:: _('index.php');
		$app->Redirect($link, $msg);
	}
}
