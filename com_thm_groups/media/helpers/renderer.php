<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

use \Joomla\CMS\Uri\Uri as URI;

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperRenderer
{
	const HREF = 0, URL = 1, PATH = 2, QUERY = 3;

	/**
	 * Replaces raw content links with groups links as appropriate.
	 *
	 * @param   string &$html  the html which will be rendered
	 *
	 * @return void modifies $html
	 */
	private static function contentQueries(&$html)
	{
		$pattern = '/href="(([^"]+)\?([^"]+(category|article)[^"]+))"/';
		if (!preg_match_all($pattern, $html, $matches))
		{
			return;
		}

		foreach (array_unique($matches[self::URL]) as $index => $url)
		{
			// Menu item or pre-resolved URL item
			if (COUNT(THM_GroupsHelperRouter::getPathItems($matches[self::PATH][$index])))
			{
				continue;
			}

			$query = html_entity_decode($matches[self::QUERY][$index]);
			parse_str($query, $params);

			if (!empty($params['option']) and $params['option'] !== 'com_content')
			{
				continue;
			}

			$params['option'] = 'com_content';
			if (empty($params['view']) or !in_array($params['view'], ['article', 'category']))
			{
				continue;
			}

			if (THM_GroupsHelperRouter::translateContent($params))
			{
				$newURL = THM_GroupsHelperRouter::build($params);
				$html   = str_replace($url, $newURL, $html);
			}
		}
	}

	/**
	 * Replaces SEF content links with groups links as appropriate. As Joomla does not do this on it's own I'll have to
	 * wait to implement this.
	 *
	 * @param   string &$html  the html which will be rendered
	 *
	 * @return void modifies $html
	 */
	private static function contentSEFRURLs(&$html)
	{
		return;
	}

	/**
	 * Replaces content links with groups links as appropriate. As Joomla does not do this on it's own I'll have to
	 * wait to implement this.
	 *
	 * @param   string &$html  the html which will be rendered
	 *
	 * @return void modifies $html
	 */
	public static function contentURLS(&$html)
	{
		self::contentQueries($html);
		self::contentSEFRURLs($html);
	}

	/**
	 * Replaces groups query links with groups sef links.
	 *
	 * @param   string &$html  the html which will be rendered
	 *
	 * @return void modifies $html
	 */
	public static function groupsQueries(&$html)
	{
		$pattern = '/href="([^"]+\?[^"]+thm_groups[^"]+)"/';
		if (!preg_match_all($pattern, $html, $matches))
		{
			return;
		}

		$modalViews = ['profile_select'];
		$queries    = array_unique($matches[1]);

		foreach ($queries as $query)
		{
			$uri = Uri::getInstance($query);
			$uri->parse($query);
			$params = $uri->getQuery(true);

			if ((!empty($params['view']) and in_array($params['view'], $modalViews))
				or !empty($params['task']))
			{
				continue;
			}

			if ($url  = THM_GroupsHelperRouter::build($params))
			{
				$html = str_replace($query, $url, $html);
			}
		}
	}

	/**
	 * Removes embedded Groups Parameters from the HTML output.
	 *
	 * @param   string &$html  the html which will be rendered
	 *
	 * @return void modifies $html
	 */
	public static function modProfilesParams(&$html)
	{
		$pattern = '/{thm[_]?groups[A-Za-z0-9]*\s.*?}/';
		$html    = preg_replace($pattern, "", $html);
	}
}
