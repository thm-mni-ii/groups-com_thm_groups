<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Protected Attributes
define('FORENAME', 1);
define('SURNAME', 2);
define('EMAIL_ATTRIBUTE', 4);
define('TITLE', 5);
define('POSTTITLE', 7);

// Valid for both fields and attribute types
define('TEXT', 1);
define('URL', 3);
define('EMAIL', 6);

// Field types
define('EDITOR', 2);
define('FILE', 4);
define('CALENDAR', 5);
define('TELEPHONE', 7);

// Attribute types
define('HTML', 2);
define('IMAGE', 4);
define('DATE_EU', 5);
define('TELEPHONE_EU', 7);
define('NAME', 8);
define('SUPPLEMENT', 9);

// Protected Role
define('MEMBER', 1);

// Base URLs for which are often used
define('HELPERS', JPATH_ROOT . '/media/com_thm_groups/helpers/');
define('IMAGE_PATH', '/images/com_thm_groups/profile/');

/**
 * Class providing functions usefull to multiple component files
 */
class THM_GroupsHelperComponent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   object &$view  the view context calling the function
	 *
	 * @return void
	 */
	public static function addSubmenu(&$view)
	{
		$thisName = $view->get('name');

		// No submenu creation while editing a resource
		if (strpos($thisName, 'edit'))
		{
			return;
		}

		$baseURL    = JUri::base() . '?option=com_thm_groups&view=';
		$viewNames  = [
			'attribute_type_manager',
			'attribute_manager',
			'content_manager',
			'group_manager',
			'profile_manager',
			'role_manager',
			'template_manager'
		];
		$otherViews = [];
		foreach ($viewNames as $viewName)
		{
			$otherViews[JText::_('COM_THM_GROUPS_' . strtoupper($viewName))]
				= ['name' => $viewName, 'link' => "$baseURL$viewName"];
		}
		ksort($otherViews);

		$component = 'thm_groups';
		$home      = [JText::_('COM_THM_GROUPS_HOME') => ['name' => $component, 'link' => "$baseURL$component"]];
		$menuItems = $home + $otherViews;

		foreach ($menuItems as $displayedText => $viewData)
		{
			JHtmlSidebar::addEntry($displayedText, $viewData['link'], $thisName == $viewData['name']);
		}

		$view->sidebar = JHtmlSidebar::render();
	}

	/**
	 * Checks access for edit views
	 *
	 * @param   object &$model   the model checking permissions
	 * @param   int     $itemID  the id if the resource to be edited (empty for new entries)
	 *
	 * @return  bool  true if the user can access the edit view, otherwise false
	 */
	public static function allowEdit(&$model, $itemID = 0)
	{
		// Admins can edit anything. Department and monitor editing is implicitly covered here.
		if (self::isAdmin())
		{
			return true;
		}

		$name = $model->get('name');

		// Views accessible with component create/edit access
		$resourceEditViews = [
			'attribute_edit',
			'attribute_type_edit',
			'profile_edit',
			'role_edit',
			'template_edit'
		];
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
	 * @param   boolean  $isAdmin  whether the file is being called from the backend
	 *
	 * @return  void
	 * @throws Exception
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
	 * Clean the cache
	 *
	 * @return  void
	 * @throws Exception
	 */
	public static function cleanCache()
	{
		$conf = JFactory::getConfig();

		$options = [
			'defaultgroup' => 'com_thm_groups',
			'cachebase'    => JFactory::getApplication()->isClient('administrator') ?
				JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
			'result'       => true,
		];

		try
		{
			$cache = JCache::getInstance('callback', $options);
			$cache->clean();
		}
		catch (Exception $exception)
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
	 * @param   mixed  $array  the collection to be cleaned (array|object)
	 *
	 * @return array the converted array
	 */
	public static function cleanIntCollection($array)
	{
		if (!is_array($array))
		{
			if (!is_object($array))
			{
				return [];
			}

			$array = Joomla\Utilities\ArrayHelper::fromObject($array);
		}

		$array = Joomla\Utilities\ArrayHelper::toInteger(array_filter(array_unique($array)));

		return $array;
	}

	/**
	 * Filters texts to their alphabetic or alphanumeric components
	 *
	 * @param   string  $text  the text to be filtered
	 * @param   string  $type  the way in which the text should be filtered
	 *
	 * @return string the filtered text
	 */
	public static function filterText($text, $type = 'alpha')
	{
		switch ($type)
		{
			case 'alphanum':
				$pattern = '/[^\p{L}\p{N}]/';
				break;
			case 'alpha':
			default:
				$pattern = '/[^\p{L}]/';
				break;
		}

		$text = preg_replace($pattern, ' ', $text);

		// The letter filter seems to include periods
		$text = str_replace('.', '', $text);

		return self::trim($text);
	}

	/**
	 * Checks whether the user has admin access to the component.
	 *
	 * @return bool true if the user has admin access, otherwise false
	 */
	public static function isAdmin()
	{
		$user = JFactory::getUser();

		return ($user->authorise('core.admin') OR $user->authorise('core.admin', 'com_thm_groups'));
	}

	/**
	 * Checks whether the user has manage access to the component.
	 *
	 * @return bool true if the user has admin access, otherwise false
	 */
	public static function isManager()
	{
		return (self::isAdmin() or JFactory::getUser()->authorise('core.manage', 'com_thm_groups'));
	}

	/**
	 * Removes empty tags or tags with &nbsp; recursively
	 *
	 * @param   string  $original  the original text
	 *
	 * @return string the text without empty tags
	 */
	public static function removeEmptyTags($original)
	{
		$pattern = "/<[^\/>]*>([\s|\&nbsp;]?)*<\/[^>]*>/";
		$cleaned = preg_replace($pattern, '', $original);

		// If the text remains unchanged there is no more to be done => bubble up
		if ($original == $cleaned)
		{
			return $original;
		}

		// There could still be further empty tags which encased the original empties.
		return self::removeEmptyTags($cleaned);
	}

	/**
	 * Replaces special characters in a given string with their transliterations.
	 *
	 * @param   string  $text  the text to be processed
	 *
	 * @return string an ASCII compatible transliteration of the given string
	 */
	public static function transliterate($text)
	{
		// This will always be for alias related purposes => always lower case
		$text = mb_strtolower($text);

		$aSearch = ['à', 'á', 'â', 'ă', 'ã', 'å', 'ā', 'ą'];
		$text    = str_replace($aSearch, 'a', $text);

		$aeSearch = ['ä', 'æ'];
		$text     = str_replace($aeSearch, 'ae', $text);

		$cSearch = ['ć', 'č', 'ç'];
		$text    = str_replace($cSearch, 'c', $text);

		$dSearch = ['ď', 'ð'];
		$text    = str_replace($dSearch, 'd', $text);

		$eSearch = ['è', 'é', 'ê', 'ě', 'ë', 'ē', 'ę'];
		$text    = str_replace($eSearch, 'e', $text);

		$gSearch = ['ģ', 'ğ'];
		$text    = str_replace($gSearch, 'g', $text);

		$iSearch = ['ı', 'ì', 'í', 'î', 'ï', 'ī'];
		$text    = str_replace($iSearch, 'i', $text);

		$lSearch = ['ļ', 'ł'];
		$text    = str_replace($lSearch, 'l', $text);

		$text = str_replace('ķ', 'k', $text);

		$nSearch = ['ń', 'ň', 'ñ', 'ņ'];
		$text    = str_replace($nSearch, 'n', $text);

		$oSearch = ['ò', 'ó', 'ô', 'õ', 'ő', 'ø'];
		$text    = str_replace($oSearch, 'o', $text);

		$text = str_replace('ö', 'oe', $text);

		$text = str_replace('ř', 'r', $text);

		$sSearch = ['ś', 'š', 'ş', 'ș'];
		$text    = str_replace($sSearch, 's', $text);

		$text = str_replace('ß', 'ss', $text);

		$tSearch = ['ť', 'ț'];
		$text    = str_replace($tSearch, 't', $text);

		$text = str_replace('þ', 'th', $text);

		$uSearch = ['ù', 'ú', 'û', 'ű', 'ů', 'ū'];
		$text    = str_replace($uSearch, 'u', $text);

		$text = str_replace('ü', 'ue', $text);

		$ySearch = ['ý', 'ÿ'];
		$text    = str_replace($ySearch, 'y', $text);

		$zSearch = ['ź', 'ž', 'ż'];
		$text    = str_replace($zSearch, 'z', $text);

		return $text;
	}

	/**
	 * Removes excess spaces from a given string.
	 *
	 * @param   string  $text  the text to be trimmed
	 *
	 * @return string the trimmed text
	 */
	public static function trim($text)
	{
		return trim(preg_replace('/ +/u', ' ', $text));
	}
}
