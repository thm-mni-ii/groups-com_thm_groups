<?php
/**
 * @package     THM_Groups
 * @extension   mod_thm_groups_menu
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once 'categories.php';

use \Joomla\CMS\Factory as Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Data retrieval class for the THM Groups menu module.
 *
 * @category  Joomla.Module.Site
 * @package   thm_groups
 */
class THM_GroupsHelperMenu
{
    /**
     * Checks whether personal content is activated for the given profile
     *
     * @param   int $profileID the profile id
     *
     * @return  bool  true if enabled, otherwise false
     * @throws Exception
     */
    public static function contentEnabled($profileID)
    {
        $dbo   = Factory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('contentEnabled')->from('#__thm_groups_profiles')->where("id = '$profileID'");
        $dbo->setQuery($query);

        try {
            $result = $dbo->loadResult();
        } catch (Exception $exception) {
            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($result) ? false : true;
    }

    /**
     * Returns published content information for the profile's content category.
     *
     * @param   int $profileID the id of the profile
     *
     * @return    array  an array of table row objects
     * @throws Exception
     */
    public static function getContent($profileID)
    {
	    $categoryID = THM_GroupsHelperCategories::getIDByProfileID($profileID);
        // Load the parameters
        $dbo        = Factory::getDBO();
        $date       = Factory::getDate();
        $quotedDate = $dbo->quote($date->toSql());

        $query = $dbo->getQuery(true);
        $query->select('content.id, content.title, content.alias, content.catid');
        $query->from('#__content AS content');
        $query->innerJoin('#__thm_groups_content AS pContent ON pContent.id = content.id');
        $query->where("content.catid = '$categoryID'");
        $query->where("content.state = '1'");
        $query->where("pContent.featured = '1'");
        $query->where("content.publish_up <= $quotedDate");
        $query->where("( content.publish_down >= $quotedDate OR content.publish_down = '0' OR content.publish_down = '0000-00-00 00:00:00')");
        $query->order('content.ordering ASC');

        $dbo->setQuery($query);

        try {
            $contents = $dbo->loadObjectList();
        } catch (Exception $exception) {
            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($contents) ? [] : $contents;
    }

    /**
     * Creates a list item with a link
     *
     * @param bool   $active whether or not the link is currently active
     * @param array  $params the parameters used to generate the link
     * @param string $text   the text to be displayed in the link
     *
     * @return string the HTML for the list item
     */
    public static function getItem($active, $params, $text)
    {
        $attribs = [];

        if ($active) {
            $attribs['class'] = 'active_link current_link';
        }

        $href = THM_GroupsHelperRouter::build($params, true);
        $text = '<span class="item-title">' . $text . "</span>";

        $item = HTMLHelper::_('link', $href, $text, $attribs);

        return $item;
    }
}