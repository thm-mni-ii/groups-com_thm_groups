<?php
/**
 * @version     v0.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;
// @codingStandardsIgnoreStart
/**
 * Content component helper.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class ContentHelper
{
    public static $extension = 'com_thm_groups';

    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     */
    public static function addSubmenu($vName)
    {
        JSubMenuHelper::addEntry(
            JText::_('JGLOBAL_ARTICLES'),
            'index.php?option=com_content&view=articles',
            $vName == 'articles_old'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_CONTENT_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&extension=com_content',
            $vName == 'categories');
        JSubMenuHelper::addEntry(
            JText::_('COM_CONTENT_SUBMENU_FEATURED'),
            'index.php?option=com_content&view=featured',
            $vName == 'featured'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   int  $categoryId  The category ID.
     * @param   int  $articleId   The article ID.
     *
     * @return  JObject
     */
    public static function getActions($categoryId = 0, $articleId = 0)
    {
        $user	= JFactory::getUser();
        $result	= new JObject;

        if (empty($articleId) && empty($categoryId))
        {
            $assetName = 'com_content';
        }
        elseif (empty($articleId))
        {
            $assetName = 'com_content.category.' . (int) $categoryId;
        }
        else
        {
            $assetName = 'com_content.article.' . (int) $articleId;
        }

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action)
        {
            $result->set($action,	$user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
    * Applies the content tag filters to arbitrary text as per settings for current user group
    *
    * @param   text  $text  The string to filter
    *
    * @return  string  The filtered string
    */
    public static function filterText($text)
    {
        // Filter settings
        jimport('joomla.application.component.helper');
        $config		= JComponentHelper::getParams('com_content');
        $user		= JFactory::getUser();
        $userGroups	= JAccess::getGroupsByUser($user->get('id'));

        $filters = $config->get('filters');

        $blackListTags			= array();
        $blackListAttributes	= array();

        $whiteListTags			= array();
        $whiteListAttributes	= array();

        $noHtml		= false;
        $whiteList	= false;
        $blackList	= false;
        $unfiltered	= false;

        // Cycle through each of the user groups the user is in.
        // Remember they are include in the Public group as well.
        foreach ($userGroups AS $groupId)
        {
            // May have added a group by not saved the filters.
            if (!isset($filters->$groupId))
            {
                continue;
            }
            else
            {
            }

            // Each group the user is in could have different filtering properties.
            $filterData = $filters->$groupId;
            $filterType	= strtoupper($filterData->filter_type);

            if ($filterType == 'NH')
            {
                // Maximum HTML filtering.
                $noHtml = true;
            }
            elseif ($filterType == 'NONE')
            {
                // No HTML filtering.
                $unfiltered = true;
            }
            else
            {
                // Black or white list.
                // Preprocess the tags and attributes.
                $tags			= explode(',', $filterData->filter_tags);
                $attributes		= explode(',', $filterData->filter_attributes);
                $tempTags		= array();
                $tempAttributes	= array();

                foreach ($tags AS $tag)
                {
                    $tag = trim($tag);

                    if ($tag)
                    {
                        $tempTags[] = $tag;
                    }
                    else
                    {
                    }
                }

                foreach ($attributes AS $attribute)
                {
                    $attribute = trim($attribute);

                    if ($attribute)
                    {
                        $tempAttributes[] = $attribute;
                    }
                    else
                    {
                    }
                }

                // Collect the black or white list tags and attributes.
                // Each list is cummulative.
                if ($filterType == 'BL')
                {
                    $blackList				= true;
                    $blackListTags			= array_merge($blackListTags, $tempTags);
                    $blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
                }
                elseif ($filterType == 'WL')
                {
                    $whiteList				= true;
                    $whiteListTags			= array_merge($whiteListTags, $tempTags);
                    $whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
                }
                else
                {
                }
            }
        }

        // Remove duplicates before processing (because the black list uses both sets of arrays).
        $blackListTags			= array_unique($blackListTags);
        $blackListAttributes	= array_unique($blackListAttributes);
        $whiteListTags			= array_unique($whiteListTags);
        $whiteListAttributes	= array_unique($whiteListAttributes);

        // Unfiltered assumes first priority.
        if ($unfiltered)
        {
            $filter = JFilterInput::getInstance(array(), array(), 1, 1, 0);
        }
        // Black lists take second precedence.
        elseif ($blackList)
        {
            // Remove the white-listed attributes from the black-list.
            $filter = JFilterInput::getInstance(
                array_diff($blackListTags, $whiteListTags), 			/* Blacklisted tags */
                array_diff($blackListAttributes, $whiteListAttributes), /* Blacklisted attributes */
                1,														/* Blacklist tags */
                1														/* Blacklist attributes */
            );
        }
        // White lists take third precedence.
        elseif ($whiteList)
        {
            $filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  /* Turn off xss auto clean */
        }
        // No HTML takes last place.
        else
        {
            $filter = JFilterInput::getInstance();
        }

        $text = $filter->clean($text, 'html');

        return $text;
    }
}
// @codingStandardsIgnoreEnd
