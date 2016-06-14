<?php
/**
 * @category    Joomla library
 * @package     THM_Core
 * @subpackage  lib_thm_core.site
 * @name        THM_GroupsHelperLanguage
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Wolf Rost, <wolf.rost@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

/**
 * Class providing functions usefull to multiple component files
 *
 * @category  Joomla.Library
 * @package   thm_core
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
