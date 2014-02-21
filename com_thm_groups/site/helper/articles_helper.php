<?php

/**
 * @version     v3.4.4
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

/**
 * ArticleHelper class for component com_thm_groups
 *
 * @category  Joomla.Component
 * @package   com_thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.0
 */
class ArticleHelper
{
    /**
     * Returns state of article
     *
     * @param   Int  $a_id  article id
     * @return  boolean
     */
    public static function isArticleFeatured($a_id)
    {
        $db = JFactory::getDbo();
        $isFeaturedQuery = $db->getQuery(true);

        $isFeaturedQuery->select('*')
        ->from('#__thm_quickpages_featured')
        ->where("conid =" . $db->quote($a_id));
        $db->setQuery((string) $isFeaturedQuery);

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