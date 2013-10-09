<?php

/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author		Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

class ArticleHelper
{
    public static function isArticleFeatured($a_id)
    {
        $db = JFactory::getDbo();
        $isFeaturedQuery = $db->getQuery(true);

        $isFeaturedQuery->select('*')
        ->from('#__thm_quickpages_featured')
        ->where("conid = $a_id");
        $db->setQuery((string)$isFeaturedQuery);

        try
        {
            $result = $db->loadObject();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        return $result;
    }
}