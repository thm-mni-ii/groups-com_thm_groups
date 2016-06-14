<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        provides options
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

define('TEXT', 1);
define('TEXTFIELD', 2);
define('LINK', 3);
define('PICTURE', 4);
define('MULTISELECT', 5);
define('TABLE', 6);
define('NUMBER', 7);
define('DATE', 8);
define('TEMPLATE', 9);

/**
 * Class providing options
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperStatic_Type
{
	/**
	 * Returns extra options like length or path for pictures for static types
	 *
	 * @param   Int $staticTypeID Static type ID
	 *
	 * @return  stdClass
	 */
	public static function getOption($staticTypeID)
	{
		$options = new stdClass;
		switch ($staticTypeID)
		{
			case TEXT:
				$options->length = 40;
				break;
			case TEXTFIELD:
				$options->length = 120;
				break;
			case PICTURE:
				$options->filename = 'anonym.jpg';
				$options->path     = '/images/com_thm_groups/profile';
				break;
		}

		return $options;
	}
}
