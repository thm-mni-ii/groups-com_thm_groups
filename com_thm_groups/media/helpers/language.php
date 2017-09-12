<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsHelperLanguage
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Wolf Rost, <wolf.rost@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing functions usefull to multiple component files
 *
 * @category  Joomla.Library
 * @package   THM_Groups
 */
class THM_GroupsHelperLanguage
{
	/**
	 * Retrieves the two letter language identifier
	 *
	 * @return  string
	 */
	public static function getShortTag()
	{
		$fullTag  = JFactory::getLanguage()->getTag();
		$tagParts = explode('-', $fullTag);

		return $tagParts[0];
	}
}
