<?php
/**
 *@category    Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups.site
 *@name		   THMGroups component site router
 *@description Template file of module mod_thm_groups_groups
 *@author	   Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Christian Güth, christian.gueth@mni.fh-giessen.de
 *@author	   Sascha Henry, sascha.henry@mni.fh-giessen.de
 *@author	   Severin Rotsch, severin.rotsch@mni.fh-giessen.de
 *@author      Martin Karry, martin.karry@mni.fh-giessen.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link		   www.mni.thm.de
 *@version	   3.0
 */

/**
 *  Creates route for SEF
 *@since  Method available since Release 1.0
 *
 *@param   array  &$query  Array, containing the query
 *
 *@return  array  all SEF Elements as list
 */
function THM_groupsBuildRoute(&$query)
{
	// Contains all SEF Elements as list
	$segments = array();
	if (isset ($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}

	if (isset ($query['layout']))
	{
		$segments[] = $query['layout'];
		unset($query['layout']);
	}

	if (isset ($query['gsuid']))
	{
		if (isset ($query['name']))
		{
			if (isset ($query['gsgid']))
			{
				$segments[] = $query['gsgid'] . "-" . $query['gsuid'] . "-" . $query['name'];
				unset($query['gsgid']);
			}
			else
			{
				$segments[] = $query['gsuid'] . "-" . $query['name'];
			}
			unset($query['gsuid']);
			unset($query['name']);
		}
		else
		{
			$segments[] = $query['gsuid'];
			unset($query['gsuid']);
		}
	}
	else
	{
	}
	return $segments;
}

/**
 *  parses the route and calculates the parts from SEF
 *@since  Method available since Release 1.0
 *
 *@param   array  $segments  all SEF Elements as list
 *
 *@return  array  Accessable elements from SEF
 */
function THM_groupsParseRoute($segments)
{
	$vars = array();
	if ($segments[0] != "")
	{
		if (isset ($segments[2]))
		{
			$arrVar1 = explode(':', $segments[2]);
		}
		$vars['view']   = $segments[0];
		$vars['layout'] = $segments[1];
		if (isset ($arrVar1[0]))
		{
			$arrVar2 = explode('-', $arrVar1[1]);
			if (isset ($arrVar2[1]))
			{
				$vars['gsgid'] = $arrVar1[0];
				$vars['gsuid'] = $arrVar2[0];
				$vars['name']  = $arrVar2[1];
			}
			else
			{
				$vars['gsuid'] = $arrVar1[0];
				$vars['name']  = $arrVar1[1];
			}
		}
	}
	return $vars;
}
