<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
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
    $segments = [];
    $view     = '';

    if (!empty($query['view'])) {
        $view       = $query['view'];
        $segments[] = $query['view'];
        unset($query['view']);
    }

    // Group & Profile/Name Segments
    if (!empty($query['profileID']) or !empty($query['userID'])) {
        $profileSegment = empty($query['profileID']) ? $query['userID'] : $query['profileID'];
        unset($query['profileID']);
        unset($query['userID']);

        if (!empty($query['groupID']) or !empty($query['name'])) {
            $profileSegment .= ":";

            if (!empty($query['name'])) {
                $profileSegment .= $query['name'];
                unset($query['name']);

                $profileSegment .= empty($query['groupID']) ? '' : '-';
            }

            if (!empty($query['groupID'])) {
                $profileSegment .= $query['groupID'];
                unset($query['groupID']);
            }
        }
        $segments[] = $profileSegment;
    }

    if (isset($query['id'])) {
        if (isset($query['alias'])) {
            $segments[] = $query['id'] . "-" . $query['alias'];
            unset($query['id']);
            unset($query['alias']);
        }
    }

    // The view name should never be at the end of the groups own routing.
    if (end($segments) == $view) {
        array_pop($segments);
    }

    return $segments;
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
    $vars         = [];
    $vars['view'] = $segments[0];

    switch ($segments[0]) {
        case 'content':

            if (count($segments) == 3) {
                if (!empty($segments[1])) {
                    parseProfileSegment($vars, $segments[1]);
                }
                if (!empty($segments[1])) {
                    parseContentSegment($vars, $segments[2]);
                }
            } elseif (count($segments) == 2) {
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

            if (isset($segments[3])) {
                $vars['layout'] = $segments[1];
            }

            if ($numberOfSegments == 3) {
                // User information
                if (isset($segments[2])) {
                    // TODO: This temporary prevents a crash.
                    if ($segments[2] == 'index.php') {
                        // Leeds to previous page.
                        $vars = [];

                        return $vars;
                    }

                    parseProfileSegment($vars, $segments[2]);
                }
            }

            if ($numberOfSegments == 4) {
                // Back options
                if (isset($segments[2])) {
                    $backOptionsTemp = explode(':', $segments[2]);
                }

                if ((isset($backOptionsTemp[0]) && ($backOptionsTemp[0] == 'com_thm_groups'))) {
                    $backOptions = explode('-', $backOptionsTemp[1]);

                    if (isset($backOptions[0])) {
                        $vars['option_back'] = $backOptionsTemp[0];
                        $vars['view_back']   = $backOptions[0];

                        if (isset($backOptions[1])) {
                            $vars['layout_back'] = $backOptions[1];
                        }

                        if (isset($backOptions[2])) {
                            $vars['Itemid_back'] = $backOptions[2];
                        }
                    }
                }

                // User information
                if (!empty($segments[3])) {
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
    // Best case only the profile ID, worst case invalid
    if (strpos($segment, ':') === false) {
        if (is_numeric($segment)) {
            $vars['profileID'] = $segment;
        }

        return;
    }

    // The Joomla Standard is ID:alias, Groups uses id:alias, id:alias-groupID, or groupID:id-alias
    $standardParts = explode(':', $segment);
    $firstPart     = $standardParts[0];

    // No nested segmentation
    if (strpos($standardParts[1], '-') === false) {
        $vars['profileID'] = $firstPart;
        if (empty($standardParts[1])) {
            return;
        }

        if (is_numeric($standardParts[1])) {
            $vars['groupID'] = $standardParts[1];
        } else {
            $vars['name'] = $standardParts[1];
        }

        return;
    }

    // Nested segmentation
    $otherParts = explode('-', $standardParts[1]);
    $groupFormat  = is_numeric(end($otherParts));
    $deprecatedFormat  = is_numeric(reset($otherParts));

    if ($groupFormat) {
        // Format <profileID>-<name>-<groupID>
        $vars['profileID'] = $firstPart;
        $vars['groupID']   = array_pop($otherParts);
    } elseif ($deprecatedFormat) {
        // Format from before my time <groupID>-<profileID>-<name>
        $vars['profileID'] = array_shift($otherParts);
        $vars['groupID']   = $firstPart;
    } else {
        // <profileID>-<name>
        $vars['profileID'] = $firstPart;
    }

    // Anything left after the pop are the names
    $vars['name'] = implode('-', $otherParts);
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

    $validID = (!empty($qpParameters[0]) and intval($qpParameters[0]) !== 0);
    if ($validID) {
        $vars['id'] = $qpParameters[0];
    }
    if (count($qpParameters) == 2) {
        $vars['alias'] = $qpParameters[1];
    }

    return;
}
