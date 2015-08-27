<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name		THMGroups component site router
 * @description Template file of module mod_thm_groups_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

/**
 * Creates route for SEF
 *
 * @param   array  &$query  Array, containing the query
 *
 * @since  Method available since Release 1.0
 *
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

    // NM PATCH SEF singlearticle view

    /*if (isset ($query['id']))
    {
        if (isset ($query['catid']))
        {
            $temp = $query['catid'];
        }

        $idAndName = explode(':', $query['id']);
        $temp .= '-' . $idAndName[0];
        $temp .= '-' . $idAndName[1];

        $segments[] = $temp;

        unset($query['id']);
        unset($query['catid']);
    }*/

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
    /* TODO: Fails when popup modal in frontend user_edit is closed
       TODO: because third element of Array is 'index.php', see -> case: default.*/

    // NM PATCH: switch between different views profile/singlearticle
    $vars = array();

    if ($segments[0] != "")
    {
        switch ($segments[0])
        {
            // NM PATCH Administration Quickpages
            case 'articles':

                $vars['view']   = $segments[0];

                if (isset ($segments[1]))
                {
                    $arrVar = explode(':', $segments[1]);

                    if (isset ($arrVar[0]) && isset ($arrVar[1]))
                    {
                        $vars['gsuid'] = $arrVar[0];
                        $vars['name']  = $arrVar[1];
                    }
                }
                break;


            case 'singlearticle':
                /*$vars['view'] = 'singlearticle';

                if (isset ($segments[1]))
                {
                    $backOptionsTemp = explode(':', $segments[1]);
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

                if (isset ($segments[2]))
                {
                    $temp = explode(':', $segments[2]);
                    $vars['gsuid'] = $temp[0];
                }

                if (isset ($segments[3]))
                {
                    $temp = explode(':', $segments[3]);
                    $vars['catid'] = $temp[0];

                    if (isset ($temp[1]))
                    {
                        $idAndName = explode('-', $temp[1]);
                        $vars['id'] = $idAndName[0];
                        $vars['name'] = $idAndName[1];
                    }
                }
                break;*/
                $vars['view'] = 'singlearticle';

                if (isset ($segments[1]))
                {
                    $arrVar = explode(':', $segments[1]);

                    if (isset ($arrVar[0]) && isset ($arrVar[1]))
                    {
                        $vars['gsuid'] = $arrVar[0];
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

                        $userInfoTemp = explode(':', $segments[2]);
                    }

                    if (isset ($userInfoTemp[0]))
                    {
                        $userInfo = explode('-', $userInfoTemp[1]);

                        if (isset ($userInfo[1]))
                        {
                            $vars['gsgid'] = $userInfoTemp[0];
                            $vars['gsuid'] = $userInfo[0];
                            $vars['name']  = $userInfo[1];
                        }
                        else
                        {
                            $vars['gsuid'] = $userInfoTemp[0];
                            $vars['name']  = $userInfoTemp[1];
                        }
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
                    if (isset ($segments[3]))
                    {
                        $userInfoTemp = explode(':', $segments[3]);
                    }

                    if (isset ($userInfoTemp[0]))
                    {
                        $userInfo = explode('-', $userInfoTemp[1]);

                        if (isset ($userInfo[1]))
                        {
                            $vars['gsgid'] = $userInfoTemp[0];
                            $vars['gsuid'] = $userInfo[0];
                            $vars['name']  = $userInfo[1];
                        }
                        else
                        {
                            $vars['gsuid'] = $userInfoTemp[0];
                            $vars['name']  = $userInfoTemp[1];
                        }
                    }
                }
                break;
        }
    }

    return $vars;
}
