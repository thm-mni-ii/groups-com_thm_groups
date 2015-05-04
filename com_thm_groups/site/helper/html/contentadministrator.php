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

/**
 * JHtml Content Administrator
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
abstract class JHtmlContentAdministrator
{
    /**
     * Featured
     *
     * @param   int      $index      I
     * @param   int      $value      The state value
     * @param   boolean  $canChange  Can change
     *
     * @return String
     */
    static public function featured($index,$value = 0, $canChange = true)
    {
        // Array of image, task, title, action
        $states	= array(
            0	=> array('disabled.png',	'articles_old.featured',	'COM_CONTENT_UNFEATURED',	'COM_CONTENT_TOGGLE_TO_FEATURE'),
            1	=> array('featured.png',	'articles_old.unfeatured',	'COM_CONTENT_FEATURED',		'COM_CONTENT_TOGGLE_TO_UNFEATURE'),
        );
        $state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
        $html	= JHtml::_('image', 'admin/' . $state[0], JText::_($state[2]), null, true);
        if ($canChange)
        {
            $html	= '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $index . '\',\'' . $state[1] . '\')" title="' .
            JText::_($state[3]) . '">' . $html . '</a>';
        }

        return $html;
    }
}
