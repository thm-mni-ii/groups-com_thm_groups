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
 * Class providing functions useful to multiple component files
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
     * @return string
     * @throws Exception
     */
    public static function getShortTag()
    {
        $app          = JFactory::getApplication();
        $requestedTag = $app->input->get('languageTag');

        if (!empty($requestedTag)) {
            return $requestedTag;
        }

        $menu       = $app->getMenu();
        if (empty($menu) or empty($menu->getActive()) or empty($menu->getActive()->params->get('initialLanguage'))) {

            // Called outside of the normal Joomla context.
            if (empty(JFactory::$language)) {
                return 'de';
            }

            $fullTag    = JFactory::getLanguage()->getTag();
            $defaultTag = explode('-', $fullTag)[0];
            return $defaultTag;
        }

        return $menu->getActive()->params->get('initialLanguage');
    }

    /**
     * Extends the tag to the regular language constant.
     *
     * @param string $shortTag the short tag for the language
     *
     * @return string the longTag
     */
    private static function resolveShortTag($shortTag = 'de')
    {
        switch ($shortTag) {
            case 'en':
                $tag = 'en-GB';
                break;
            case 'de':
            default:
                $tag = 'de-DE';
        }

        return $tag;
    }
}
