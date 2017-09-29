<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsRouter
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Creates route for SEF
 *
 * @param   array &$query Array, containing the query
 *
 * @return  array  all SEF Elements as list
 */
function THM_GroupsBuildRoute(&$query)
{
	// Contains all SEF Elements as list
	$segments = array();
	$view     = '';

	if (!empty($query['view']))
	{
		$view       = $query['view'];
		$segments[] = $query['view'];
		unset($query['view']);
	}

	buildOptionsRoute($query);

	// Group & Profile/Name Segments
	if (!empty($query['profileID']))
	{
		$profileSegment = $query['profileID'];
		unset($query['profileID']);

		if (!empty($query['groupID']) OR !empty($query['name']))
		{
			$profileSegment .= ":";

			if (!empty($query['name']))
			{
				$profileSegment .= $query['name'];
				unset($query['name']);

				$profileSegment .= empty($query['groupID']) ? '' : '-';
			}

			if (!empty($query['groupID']))
			{
				$profileSegment .= $query['groupID'];
				unset($query['groupID']);
			}
		}
		$segments[] = $profileSegment;
	}

	if (isset ($query['id']))
	{
		if (isset ($query['alias']))
		{
			$segments[] = $query['id'] . "-" . $query['alias'];
			unset($query['id']);
			unset($query['alias']);
		}
	}

	// The view name should never be at the end of the groups own routing.
	if (end($segments) == $view)
	{
		array_pop($segments);
	}

	return $segments;
}

/**
 * builds back options
 *
 * @param   array &$query query
 *
 * @return  void
 */
function buildOptionsRoute(&$query)
{
	if (isset($query['option_back']) && isset($query['view_back']))
	{
		unset($query['option_back']);
		unset($query['view_back']);

		if (isset ($query['layout_back']))
		{
			unset($query['layout_back']);
		}

		if (isset ($query['Itemid_back']))
		{
			unset($query['Itemid_back']);
		}
	}
}

/**
 * Parses the route and calculates the parts from SEF
 *
 * @param   array $segments all SEF Elements as list
 *
 * @return  array  Accessable elements from SEF
 */
function THM_GroupsParseRoute($segments)
{
	$vars         = array();
	$vars['view'] = $segments[0];

	switch ($segments[0])
	{
		case 'content':

			if (count($segments) == 3)
			{
				if (!empty($segments[1]))
				{
					parseProfileSegment($vars, $segments[1]);
				}
				if (!empty($segments[1]))
				{
					parseContentSegment($vars, $segments[2]);
				}
			}
			elseif (count($segments) == 2)
			{
				parseContentSegment($vars, $segments[1]);
			}
			break;

		case 'advanced':
		case 'profile':
		case 'profile_edit':
		case 'overview':
		case 'content_manager':
			parseProfileSegment($vars, end($segments));
			break;

		// THM Groups default case (original code)
		default:
			$numberOfSegments = count($segments);

			$vars['view'] = $segments[0];

			if (isset ($segments[3]))
			{
				$vars['layout'] = $segments[1];
			}

			if ($numberOfSegments == 3)
			{
				// User information
				if (isset ($segments[2]))
				{
					// TODO: This temporary prevents a crash.
					if ($segments[2] == 'index.php')
					{
						// Leeds to previous page.
						$vars = array();

						return $vars;
					}

					parseProfileSegment($vars, $segments[2]);
				}
			}

			if ($numberOfSegments == 4)
			{
				// Back options
				if (isset ($segments[2]))
				{
					$backOptionsTemp = explode(':', $segments[2]);
				}

				if ((isset ($backOptionsTemp[0]) && ($backOptionsTemp[0] == 'com_thm_groups')))
				{
					$backOptions = explode('-', $backOptionsTemp[1]);

					if (isset ($backOptions[0]))
					{
						$vars['option_back'] = $backOptionsTemp[0];
						$vars['view_back']   = $backOptions[0];

						if (isset ($backOptions[1]))
						{
							$vars['layout_back'] = $backOptions[1];
						}

						if (isset ($backOptions[2]))
						{
							$vars['Itemid_back'] = $backOptions[2];
						}
					}
				}

				// User information
				if (!empty ($segments[3]))
				{
					parseProfileSegment($vars, $segments[3]);
				}
			}
			break;
	}

	return $vars;
}

/**
 * Parses the segment with profile information.
 * Format profileID\:profile-surname?-?groupID?
 *
 * @param   array  $vars    the input variables
 * @param   string $segment the segment with profile information
 *
 * @return  void  sets indexes in &$vars
 */
function parseProfileSegment(&$vars, $segment)
{
	if (strpos($segment, ':') === false)
	{
		return;
	}

	list($profileID, $profileData) = explode(':', $segment);

	$vars['profileID'] = $profileID;

	if (!empty($profileData))
	{
		$profileData = explode('-', $profileData);

		if (is_numeric(end($profileData)))
		{
			$vars['groupID'] = array_pop($profileData);
		}

		// Anything left is the profile's surname
		if (!empty($profileData))
		{
			$vars['name'] = implode('-', $profileData);
		}
	}
}

/**
 * Parses the segment with profile information
 *
 * @param   array  $vars    the input variables
 * @param   string $segment the segment with profile information
 *
 * @return  void  sets indexes in &$vars
 */
function parseContentSegment(&$vars, $segment)
{
	$qpParameters = explode(':', $segment);

	$validID = (!empty($qpParameters[0]) AND intval($qpParameters[0]) !== 0);
	if ($validID)
	{
		$vars['id'] = $qpParameters[0];
	}
	if (count($qpParameters) == 2)
	{
		$vars['alias'] = $qpParameters[1];
	}

	return;
}
