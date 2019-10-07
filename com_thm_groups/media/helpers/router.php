<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once 'content.php';
require_once 'profiles.php';

use \Joomla\CMS\Uri\Uri as URI;

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperRouter
{
    /**
     * Method to build the displayed URL
     *
     * @param array $params   the parameters used to build internal links
     * @param bool  $asString true if the url should be functional, false if an array of segments
     *
     * @return mixed string if the URL should be complete, otherwise an array of terms to use in the URL
     * @throws Exception
     */
    public static function build($params, $asString = true)
    {
        $default = $asString ? '' : [];
        if (empty($params['view']) or empty($params['profileID'])) {
            return $default;
        }

        $profileAlias = THM_GroupsHelperProfiles::getAlias($params['profileID']);
        if (empty($profileAlias)) {
            return $default;
        } elseif (empty($params['view'])) {
            $params['view'] = 'profile';
        }

        $path = [$profileAlias];
        switch ($params['view']) {
            case 'content':
                if (!empty($params['id'])) {
                    $path[] = THM_GroupsHelperContent::getAlias($params['id']);
                }
                break;
            case 'content_manager':
                $path[] = JText::_('COM_THM_GROUPS_CONTENT_MANAGER_ALIAS');
                break;
            case 'profile':
                if (!empty($params['format'])) {
                    $path[] = $params['format'];
                }
                break;
            case 'profile_edit':
                $path[] = JText::_('COM_THM_GROUPS_EDIT_ALIAS');
                break;
        }

        return $asString ? URI::base() . implode('/', $path) : $path;
    }

    /**
     * Attempts to resolve a URL path to a menu item
     *
     * @param string $possibleMenuPath the path string to check against
     *
     * @return array the id, title and url of the menu item on success, otherwise empty
     * @throws Exception
     */
    public static function getMenuByPath($possibleMenuPath)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('id, title')->select($query->concatenate(["'" . URI::base() . "'", 'path']) . ' AS URL')
            ->from('#__menu')
            ->where("path = '$possibleMenuPath'")->where("link LIKE '%option=com_thm_groups%'");
        $dbo->setQuery($query);

        try {
            $menu = $dbo->loadAssoc();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($menu) ? [] : $menu;
    }

    /**
     * Retrieves the path items free of masked subdomains, query items, extension coding, the word index, and empty items.
     *
     * @param string $url the URL to be parsed into path items.
     *
     * @return array the relevant items in the url path
     */
    public static function getPathItems($url)
    {

        $rawPath = str_replace(URI::base(), '', $url);

        // The URL is external and therefore irrelevant
        if ($rawPath === $url) {
            return [];
        }

        // Attempt to resolve using modern rules
        $queryFreePath     = preg_replace('/\?.+/', '', $rawPath);
        $extensionFreePath = str_replace(['.html', '.htm', '.php'], '', $queryFreePath);
        $indexFreePath     = str_replace('/index', '', $extensionFreePath);
        $rawPathItems      = explode('/', $indexFreePath);

        return array_filter($rawPathItems);
    }

    /**
     * Checks whether the segments provide the information required for dynamic linking
     *
     * @param array $segments the segments of the path
     *
     * @return array the parameters which were able to be parsed from the given segments
     * @throws Exception
     */
    public static function parse($segments)
    {
        $return         = [];
        $lastItem       = rawurldecode(array_pop($segments));
        $secondLastItem = rawurldecode(array_pop($segments));

        $lang = JFactory::getLanguage();
        $lang->load('com_thm_groups');

        // Resolve modern sef links first
        if ($lastItem === JText::_('COM_THM_GROUPS_OVERVIEW_ALIAS')) {
            $return['view'] = 'overview';
        } elseif ($secondLastItem === JText::_('COM_THM_GROUPS_DISAMBIGUATION_ALIAS')) {
            $return['view']   = 'overview';
            $return['search'] = $lastItem;
        } elseif ($lastItem === JText::_('COM_THM_GROUPS_CONTENT_MANAGER_ALIAS')) {
            $profileID = THM_GroupsHelperProfiles::getProfileIDByAlias($secondLastItem);
            if (!empty($profileID)) {
                $return['view']      = 'content_manager';
                $return['profileID'] = $profileID;
            }
        } elseif ($lastItem === JText::_('COM_THM_GROUPS_EDIT_ALIAS')) {
            $profileID = THM_GroupsHelperProfiles::getProfileIDByAlias($secondLastItem);
            if (!empty($profileID)) {
                $return['view']      = 'profile_edit';
                $return['profileID'] = $profileID;
            }
        } elseif ($lastItem === 'vcf') {
            $profileID = THM_GroupsHelperProfiles::getProfileIDByAlias($secondLastItem);
            if (!empty($profileID)) {
                $return['view']      = 'profile';
                $return['profileID'] = $profileID;
                $return['format']    = $lastItem;
            }
        } elseif (empty($secondLastItem)) {
            $profileID = THM_GroupsHelperProfiles::getProfileIDByAlias($lastItem);
            if (!empty($profileID)) {
                $return['view']      = 'profile';
                $return['profileID'] = $profileID;
            }
        } else {
            $profileID = THM_GroupsHelperProfiles::getProfileIDByAlias($secondLastItem);
            if (!empty($profileID)) {
                if (is_numeric($profileID)) {
                    $contentID = THM_GroupsHelperContent::getIDByAlias($lastItem, $profileID);
                    if (empty($contentID)) {
                        $return['view']      = 'profile';
                        $return['profileID'] = $profileID;
                    } else {
                        $return['view']      = 'content';
                        $return['id']        = $contentID;
                        $return['profileID'] = $profileID;
                    }
                }
            }
        }

        if (!empty($return['profileID']) and !is_numeric($return['profileID'])) {
            $return['view']   = 'overview';
            $return['search'] = $return['profileID'];
            unset($return['profileID']);
        }

        if (empty($return['view'])) {
            return $segments;
        }

        $return['option'] = 'com_thm_groups';

        return $return;
    }

    /**
     * Checks whether the segments provide the information required for dynamic linking via legacy configurations
     *
     * @param array $segments the segments of the path
     *
     * @return array the parameters which were able to be parsed from the given segments
     * @throws Exception
     */
    public static function parseLegacy($segments)
    {
        $return        = [];
        $dynamicViews  = ['articles', 'content', 'content_manager', 'profile', 'profile_edit', 'singlearticle'];
        $allowedDepth2 = ['articles', 'content_manager', 'profile', 'profile_edit'];
        $allowedDepth3 = ['content', 'singlearticle'];

        // Remove the useless segment if existent
        if ($layoutSegment = array_search('default', $segments)) {
            unset($segments[$layoutSegment]);
        }

        $segmentCount = count($segments);

        // Legacy SER URLs require at lease the view name and a resource ID
        if ($segmentCount <= 2) {
            return $return;
        }

        $lastItem = rawurldecode(array_pop($segments));

        // The view name is positioned wrong
        if (in_array($lastItem, $dynamicViews)) {
            return $return;
        }

        $secondLastItem = rawurldecode(array_pop($segments));

        // The views expected at a depth of two were not found.
        if (!in_array($secondLastItem, $allowedDepth2) and $segmentCount >= 3) {
            $thirdLastItem = rawurldecode(array_pop($segments));

            // Depth exceeded
            if (!in_array($thirdLastItem, $allowedDepth3)) {
                return $return;
            }
        }

        if (empty($thirdLastItem)) {
            switch ($secondLastItem) {
                case 'content':
                case 'singlearticle':
                    $return['id'] = self::parseContent($lastItem);

                    if (empty($return['id'])) {
                        return [];
                    }

                    $return['profileID'] = THM_GroupsHelperContent::getProfileID($return['id']);
                    $return['view']      = 'content';

                    $return['view'] = $segments[0];
                    break;

                case 'articles':
                case 'content_manager':
                    $return['profileID'] = self::parseProfile($lastItem);
                    $return['view']      = 'content_manager';
                    break;

                case 'profile':
                case 'profile_edit':
                    $return['profileID'] = self::parseProfile($lastItem);
                    $return['view']      = $secondLastItem;
                    break;

            }
        } else {
            $return['profileID'] = self::parseProfile($secondLastItem);
            $return['id']        = self::parseContent($lastItem);

            // Invalid profile id, but valid content id => use the profileID associated with the content
            if (empty($return['profileID']) and !empty($return['id'])) {
                $return['profileID'] = THM_GroupsHelperContent::getProfileID($return['id']);
            }

            $return['view'] = 'content';
        }

        return $return;
    }

    /**
     * Parses the given string to check for a category associated with the component
     *
     * @param string $potentialCategory the segment being checked
     *
     * @return mixed true if the category is the root category, int the profile id if associated with a profile, false
     *               if the category is not associated with groups
     * @throws Exception
     */
    public static function parseCategory($potentialCategory)
    {
        if (is_numeric($potentialCategory)) {
            $categoryID = $potentialCategory;
        } elseif (preg_match('/(\d+)\-[a-zA-Z\-]+/', $potentialCategory, $matches)) {
            $categoryID = $matches[1];
        } else {
            $categoryID = $potentialCategory;
        }

        if (empty($categoryID)) {
            return $categoryID;
        }

        if (THM_GroupsHelperCategories::isRoot($categoryID)) {
            return true;
        }

        $profileID = THM_GroupsHelperCategories::getProfileID($categoryID);

        return empty($profileID) ? false : $profileID;
    }

    /**
     * Parses the given string to check for content associated with the component
     *
     * @param string $potentialContent the segment being checked
     * @param int    $profileID        the ID of the profile with which this content should be associated
     *
     * @return int the id of the associated content if existent, otherwise 0
     * @throws Exception
     */
    public static function parseContent($potentialContent, $profileID = null)
    {
        $contentID = 0;
        if (is_numeric($potentialContent)) {
            $contentID = $potentialContent;
        } elseif (preg_match('/(\d+)\-[a-zA-Z\-]+/', $potentialContent, $matches)) {
            $contentID = $matches[1];
        }

        if (empty($contentID)) {
            return $contentID;
        }

        $profileID = THM_GroupsHelperContent::isAssociated($contentID, $profileID);

        return empty($profileID) ? 0 : $contentID;
    }

    /**
     * Parses the given string to check for a valid profile
     *
     * @param string $potentialProfile the segment being checked
     *
     * @return mixed int the id if a distinct profile was found, string if no distinct profile was found, otherwise 0
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    public static function parseProfile($potentialProfile)
    {
        if (is_numeric($potentialProfile)) {
            $profileID = $potentialProfile;
        } // Corrected pre 3.8 URL formatting
        elseif (preg_match('/(\d+)\-([a-zA-Z\-]+)-\d+/', $potentialProfile, $matches)) {
            $profileID      = $matches[1];
            $potentialAlias = $matches[2];
        } // Original faulty URL formatting
        elseif (preg_match('/\d+-(\d+)-([a-zA-Z\-]+)/', $potentialProfile, $matches)) {
            $profileID      = $matches[1];
            $potentialAlias = $matches[2];
        } else {
            $profileID = THM_GroupsHelperProfiles::getProfileIDByAlias($potentialProfile);
        }

        if ($profileID and is_numeric($profileID)) {
            $profileAlias     = THM_GroupsHelperProfiles::getAlias($profileID);
            $profilePublished = THM_GroupsHelperProfiles::isPublished($profileID);
            $relevant         = empty($potentialAlias) ? true : strpos($profileAlias, $potentialAlias);

            return ($relevant and $profilePublished and $profileAlias) ? $profileID : 0;
        }

        if ($profileID and is_string($profileID)) {
            return $profileID;
        }

        return 0;
    }

    /**
     * Redirects to a sef conform THM Groups URL
     *
     * @param $params
     *
     * @return void redirects to a sef conform url
     * @throws Exception
     */
    public static function redirect($params)
    {
        $app  = JFactory::getApplication();
        $lang = JFactory::getLanguage();
        $lang->load('com_thm_groups');
        $code    = empty($params['code']) ? false : $params['code'];
        $msg     = false;
        $msgType = 'message';

        if (empty($params['view'])) {

            if (empty($code)) {
                return;
            }

            $code         = $params['code'];
            $codeConstant = "COM_THM_GROUPS_ERROR_$code";
            $msg          = JText::_($codeConstant);

            switch ($code) {
                case 401:
                    $url     = URI::root();
                    $msgType = 'error';
                    break;
                case 404:
                    $url     = $app->input->server->getString('HTTP_REFERER');
                    $msgType = 'warning';
                    break;
            }
        } else {
            switch ($params['view']) {
                case 'profile':
                    $profileAlias = THM_GroupsHelperProfiles::getAlias($params['profileID']);
                    $url          = URI::root() . $profileAlias;
                    $code         = $code ? $code : 301;
                    break;
                case 'profile_edit':
                    $profileAlias = THM_GroupsHelperProfiles::getAlias($params['profileID']);
                    $editAlias    = JText::_('COM_THM_GROUPS_EDIT_ALIAS');
                    $url          = URI::root() . "$profileAlias/$editAlias";
                    $code         = $code ? $code : 301;
                    break;
                case 'content':
                    $profileAlias = THM_GroupsHelperProfiles::getAlias($params['profileID']);
                    $contentAlias = THM_GroupsHelperContent::getAlias($params['id']);
                    $url          = URI::root() . "$profileAlias/$contentAlias";
                    $code         = $code ? $code : 301;
                    break;
                case 'content_manager':
                    $profileAlias = THM_GroupsHelperProfiles::getAlias($params['profileID']);
                    $managerAlias = JText::_('COM_THM_GROUPS_CONTENT_MANAGER_ALIAS');
                    $url          = URI::root() . "$profileAlias/$managerAlias";
                    $code         = $code ? $code : 301;
                    break;
                case 'overview':
                    $lang->load('com_thm_groups');
                    // Masks the root content category for profile content
                    if (empty($params['search'])) {
                        $overviewTitle = JText::_('COM_THM_GROUPS_OVERVIEW_ALIAS');
                        $url           = URI::root() . $overviewTitle;
                        $code          = $code ? $code : 301;
                    } // The given names did not deliver a distinct result
                    else {
                        $disambiguationAlias = JText::_('COM_THM_GROUPS_DISAMBIGUATION_ALIAS');
                        $cleanedSearch       = $params['search'];
                        $url                 = URI::root() . "$disambiguationAlias/$cleanedSearch";
                        $code                = $code ? $code : 409;
                        $msg                 = JText::_('COM_THM_GROUPS_ERROR_409');
                        $msgType             = 'notice';
                    }
                    break;
            }
        }

        http_response_code($code);

        if ($msg) {
            $app->enqueueMessage($msg, $msgType);
        }

        $app->redirect($url, $code);
    }

    /**
     * Sets the path for dynamic groups content
     *
     * @return void sets the pathway items
     * @throws Exception
     */
    public static function setPathway()
    {
        $app       = JFactory::getApplication();
        $profileID = $app->input->getInt('profileID');

        if (empty($profileID)) {
            return;
        }

        // Get the pathway and empty and default items from Joomla

        $contentID   = $app->input->getInt('id');
        $pathway     = $app->getPathway();
        $profileName = THM_GroupsHelperProfiles::getDisplayName($profileID);
        $profileURL  = THM_GroupsHelperRouter::build(['view' => 'profile', 'profileID' => $profileID]);
        $session     = JFactory::getSession();

        $pathway->setPathway([]);

        if (empty($contentID)) {

            $profileAlias = THM_GroupsHelperProfiles::getAlias($profileID);
            $pathItems    = self::getPathItems($app->input->server->getString('HTTP_REFERER'));

            // Redirect back from a profile dependent item.
            if (in_array($profileAlias, $pathItems)) {
                $referrerName = $session->get('referrerName', '', 'thm_groups');
                $referrerURL  = $session->get('referrerUrl', '', 'thm_groups');
                if (empty($referrerName) or empty($referrerURL)) {
                    $pathway->addItem(JText::_('COM_THM_GROUPS_HOME'), URI::base());
                } else {
                    $pathway->addItem($referrerName, $referrerURL);
                }
            } else {
                $possibleMenuPath = implode('/', $pathItems);
                $menu             = self::getMenuByPath($possibleMenuPath);
                if (empty($menu)) {
                    $pathway->addItem(JText::_('COM_THM_GROUPS_HOME'), URI::base());
                } else {
                    $session->set('referrerName', $menu['title'], 'thm_groups');
                    $session->set('referrerUrl', $menu['URL'], 'thm_groups');
                    $pathway->addItem($menu['title'], $menu['URL']);
                }
            }

            $pathway->addItem($profileName, $profileURL);
        } else {

            $referrerName = $session->get('referrerName', '', 'thm_groups');
            $referrerURL  = $session->get('referrerUrl', '', 'thm_groups');
            if (empty($referrerName) or empty($referrerURL)) {
                $pathway->addItem(JText::_('COM_THM_GROUPS_HOME'), URI::base());
            } else {
                $pathway->addItem($referrerName, $referrerURL);
            }

            $pathway->addItem($profileName, $profileURL);

            $contentTitle  = THM_GroupsHelperContent::getTitle($contentID);
            $contentParams = ['view' => 'content', 'profileID' => $profileID, 'id' => $contentID];
            $contentURL    = THM_GroupsHelperRouter::build($contentParams);
            $pathway->addItem($contentTitle, $contentURL);
        }
    }

    /**
     * Validates the query against the dynamic content parameters
     *
     * @param array &$query the query parameters
     *
     * @return mixed true if the query has all required parameters and they are valid, false if the query is invalid,
     *               int 0 if the validity could not be determined due to missing parameters.
     * @throws Exception
     */
    public static function validateQuery(&$query)
    {
        // Explicitly not THM Groups
        if (!empty($query['option']) and $query['option'] !== 'com_thm_groups') {
            return false;
        }

        // Explicitly not a dynamic THM Groups view that would be called by query
        $dynamicViews = ['content', 'content_manager', 'profile', 'profile_edit'];
        if (!empty($query['view']) and !in_array($query['view'], $dynamicViews)) {
            return false;
        }

        // This is missing the required profileID parameter, which may be aliased in the url
        $pIDDependentViews = ['content_manager', 'profile', 'profile_edit'];
        $pIDDependent      = (!empty($query['view']) and in_array($query['view'], $pIDDependentViews));
        if ($pIDDependent and empty($query['profileID'])) {
            return 0;
        }

        // Invalid Profile
        $profileAlias = empty($query['profileID']) ? '' : THM_GroupsHelperProfiles::getAlias($query['profileID']);
        if (!empty($query['profileID']) and empty($profileAlias)) {
            return false;
        } elseif ($pIDDependent and empty($profileAlias)) {
            return false;
        } elseif ($pIDDependent and !empty($profileAlias)) {
            return true;
        }

        // This is missing the required id parameter, which may be aliased in the url
        $isContent = (!empty($query['view']) and $query['view'] == 'content');
        if ($isContent and empty($query['id'])) {
            return 0;
        }

        // Invalid content
        $contentAlias = empty($query['id']) ? '' : THM_GroupsHelperContent::getAlias($query['id']);
        if (!empty($query['id']) and empty($contentAlias)) {
            return false;
        } elseif ($isContent and empty($contentAlias)) {
            return false;
        }

        // Nothing was explicitly invalid, but nothing was confirmed either
        if (empty($profileAlias) and empty($contentAlias)) {
            return 0;
        }

        if ($contentAlias) {
            $profileID = empty($query['profileID']) ?
                THM_GroupsHelperContent::isAssociated($query['id']) :
                THM_GroupsHelperContent::isAssociated($query['id'], $query['profileID']);

            if (empty($profileID)) {
                return false;
            }

            $query['profileID'] = $profileID;
            $query['view']      = 'content';

            return true;
        }

        $query['view'] = 'profile';

        return true;
    }
}
