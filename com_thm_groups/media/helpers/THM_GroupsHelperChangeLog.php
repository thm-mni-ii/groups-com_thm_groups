<?php
/**
 * @category    Joomla library
 * @package     THM_Repo
 * @subpackage  lib_thm_repo
 * @name        THM_GroupsHelperChangeLog
 * @author      Ilja Michajlow, <Ilja.Michajlow@mni.thm.de>
 * @author      Andrej Sajenko, <Andrej.Sajenko@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root(true) . '/media/com_thm_groups/css/THMChangelogColoriser.css');

/**
 * Class to colorise an Changelog.
 *
 * @category  Joomla.Library
 * @package   thm_core.log
 */
class THM_GroupsHelperChangeLog
{
	/**
	 * Colorise a changelog by usind prefix of the changelog entries.
	 *
	 * Prefix:
	 *  = - Skip/Comment will not be used in the result.
	 *  + - added
	 *  - - removed
	 *  ~ - changed
	 *  ! - important
	 *  # - fixed
	 *    - (without prefix) Will displayed normal.
	 *
	 * @param   string $file     Path to changelog.
	 * @param   bool   $onlyLast Trigger to display only last changelog entry
	 *
	 * @return string The colorised HTML String
	 */
	public static function colorise($file, $onlyLast = false)
	{
		$ret   = '';
		$lines = file($file);

		if (empty($lines))
		{
			return $ret;
		}

		array_shift($lines);

		foreach ($lines as $line)
		{

			$line = trim($line);
			if (empty($line))
			{
				continue;
			}
			$type = substr($line, 0, 1);

			switch ($type)
			{
				case '=':
					continue;
				case '+':
					$ret .= "\t" . '<li class="THM-iCampus-added"><span></span>' . htmlentities(trim(substr($line, 2))) . "</li>\n";
					break;

				case '-':
					$ret .= "\t" . '<li class="THM-iCampus-removed"><span></span>' . htmlentities(trim(substr($line, 2))) . "</li>\n";
					break;

				case '~':
					$ret .= "\t" . '<li class="THM-iCampus-changed"><span></span>' . htmlentities(trim(substr($line, 2))) . "</li>\n";
					break;

				case '!':
					$ret .= "\t" . '<li class="THM-iCampus-important"><span></span>' . htmlentities(trim(substr($line, 2))) . "</li>\n";
					break;

				case '#':
					$ret .= "\t" . '<li class="THM-iCampus-fixed"><span></span>' . htmlentities(trim(substr($line, 2))) . "</li>\n";
					break;

				default:
					if (!empty($ret))
					{
						$ret .= "</ul>";
						if ($onlyLast)
						{
							return $ret;
						}
					}
					if (!$onlyLast)
					{
						$ret .= "<h3 class=\"THM-iCampus\">$line</h3>\n";
					}
					$ret .= "<ul class=\"THM-iCampus\">\n";
					break;
			}
		}

		return $ret;
	}
}