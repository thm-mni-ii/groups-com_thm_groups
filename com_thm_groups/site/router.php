<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name		THMGroups component site router
 * @description Template file of module mod_thm_groups_groups
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
 * @param   array  &$query  Array, containing the query
 * @return  array  all SEF Elements as list
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

    buildOptionsRoute($query, $segments);

    // User options
    if (!empty($query['userID']))
    {
        $profileSegment = '';
        if (!empty($query['groupID']))
        {
            $profileSegment .= "{$query['groupID']}:{$query['userID']}";
            unset($query['groupID']);
            unset($query['userID']);
            if (!empty($query['name']))
            {
                $profileSegment .= "-" . $query['name'];
                unset($query['name']);
            }
        }
        elseif (!empty($query['name']))
        {
            $profileSegment .= $query['userID'] . "-" . $query['name'];
            unset($query['userID']);
            unset($query['name']);
        }
        else
        {
            $profileSegment .= $query['userID'];
            unset($query['userID']);
        }
        $segments[] = $profileSegment;
    }

    if (isset ($query['id']))
    {
        if (isset ($query['nameqp']))
        {
            $segments[] = $query['id'] . "-" . $query['nameqp'];
            unset($query['id']);
            unset($query['nameqp']);
        }
    }

    return $segments;
}

/**
 * builds back options
 *
 * @param   array  &$query     query
 * @param   array  &$segments  segments
 *
 * @return  void
 */
function buildOptionsRoute(&$query, &$segments)
{
    if (isset($query['option_back']) && isset($query['view_back']))
    {
        // $temp = $query['option_back'] . '-' . $query['view_back'];
        unset($query['option_back']);
        unset($query['view_back']);

        if (isset ($query['layout_back']))
        {
            // $temp .= '-' . $query['layout_back'];
            unset($query['layout_back']);
        }

        if (isset ($query['Itemid_back']))
        {
            //  $temp .= '-' . $query['Itemid_back'];
            unset($query['Itemid_back']);
        }

        // $segments[] = $temp;
    }
}

/**
 * Parses the route and calculates the parts from SEF
 *
 * @param   array  $segments  all SEF Elements as list
 *
 * @return  array  Accessable elements from SEF
 */
function THM_groupsParseRoute($segments)
{
    /* TODO: Fails when popup modal in frontend profile_edit is closed
       TODO: because third element of Array is 'index.php', see -> case: default.*/

    // NM PATCH: switch between different views profile/singlearticle
    $vars = array();

    $doRoute = (!empty($segments) AND end($segments) != 'index' AND !empty($segments[0]));
    if (!$doRoute)
    {
        return $vars;
    }

    $vars['view']   = $segments[0];
    switch ($segments[0])
    {
        // NM PATCH Administration Quickpages
        case 'articles':
            if (isset ($segments[1]))
            {
                $arrVar = explode(':', $segments[1]);

                if (isset ($arrVar[0]) && isset ($arrVar[1]))
                {
                    $vars['userID'] = $arrVar[0];
                    $vars['name']  = $arrVar[1];
                }
            }
            break;


        case 'singlearticle':

            if (isset ($segments[1]))
            {
                $arrVar = explode(':', $segments[1]);

                if (isset ($arrVar[0]) && isset ($arrVar[1]))
                {
                    $vars['userID'] = $arrVar[0];
                    $vars['name']  = $arrVar[1];
                }
            }

            if (isset ($segments[2]))
            {
                $arrVar = explode(':', $segments[2]);

                if (isset ($arrVar[0]) && isset ($arrVar[1]))
                {
                    $vars['id'] = $arrVar[0];
                    $vars['nameqp']  = $arrVar[1];
                }
            }
            break;

        case 'profile':
        case 'profile_edit':
            parseProfileSegment($vars, end($segments));
            break;

        // THM Groups default case (original code)
        default:
            $numberOfSegments = count($segments);

            $vars['view']   = $segments[0];

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
                        $vars['view_back'] = $backOptions[0];

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
 * Parses the segment with profile information
 *
 * @param   array   $vars            the input variables
 * @param   string  $profileSegment  the segment with profile information
 *
 * @return  void  sets indexes in &$vars
 */
function parseProfileSegment(&$vars, $profileSegment)
{
    $profileData = explode('-', $profileSegment);
    if (!empty($profileData[1]))
    {
        $vars['name']  = $profileData[1];
    }

    // $firstID will always be set irregardless of whether the delimiter was found
    $profileIDs = explode(':', $profileData[0]);

    // Only the userID
    if (empty($profileIDs[1]))
    {
        $vars['userID'] = $profileIDs[0];
    }
    else
    {
        $vars['groupID'] = $profileIDs[0];
        $vars['userID'] = $profileIDs[1];
    }
}
