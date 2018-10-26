<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Wolf Rost, <wolf.rost@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing functions usefull to multiple component files
 */
class THM_GroupsHelperLanguage
{
    /**
     * Sets the Joomla Language based on input from the language switch
     *
     * @param string $shortTag the language's short tag for manual requests
     *
     * @return JLanguage
     * @throws Exception
     */
    public static function getLanguage($shortTag = null)
    {
        $requested          = empty($shortTag) ? self::getShortTag() : $shortTag;
        $supportedLanguages = ['en', 'de'];

        if (in_array($requested, $supportedLanguages)) {
            switch ($requested) {
                case 'de':
                    $lang = new JLanguage('de-DE');
                    break;
                case 'en':
                default:
                    $lang = new JLanguage('en-GB');
                    break;
            }
        } else {
            $lang = new JLanguage('en-GB');
        }

        $lang->load('com_thm_groups');

        return $lang;
    }

    /**
     * Retrieves the two letter language identifier
     *
     * @return string
     * @throws Exception
     */
    public static function getLongTag()
    {
        return self::resolveShortTag(self::getShortTag());
    }

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
