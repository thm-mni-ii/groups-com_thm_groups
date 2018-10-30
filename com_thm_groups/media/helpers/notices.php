<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperNotices
{
    /**
     * Creates an icon with a tooltip explaining that the element's display cannot be labeled
     *
     * @return string the HTML for the notice
     */
    public static function getLabelingNotice()
    {
        $icon = '<i class="icon-minus-circle hasTooltip" title="XXXX"></i>';
        return str_replace('XXXX', JText::_('COM_THM_GROUPS_NO_LABELING'), $icon);
    }

    /**
     * Creates an icon with a tooltip explaining that the element's display is not influenced by its ordering
     *
     * @return string the HTML for the notice
     */
    public static function getOrderingNotice()
    {
        $icon = '<i class="icon-notification-circle hasTooltip" title="XXXX"></i>';

        return str_replace('XXXX', JText::_('COM_THM_GROUPS_NO_ORDERING'), $icon);
    }

    /**
     * Creates an icon with a tooltip explaining that the element's display is protected from deletion and may have
     * limited editing options.
     *
     * @return string the HTML for the notice
     */
    public static function getProtectedNotice()
    {
        $icon = '<i class="icon-lock hasTooltip" title="XXXX"></i>';
        return str_replace('XXXX', JText::_('COM_THM_GROUPS_PROTECTED_ELEMENT'), $icon);
    }

    /**
     * Creates an icon with a tooltip explaining that the element will always be published.
     *
     * @return string the HTML for the notice
     */
    public static function getPublicationNotice()
    {
        $icon = '<i class="icon-checkmark-circle hasTooltip" title="XXXX"></i>';

        return str_replace('XXXX', JText::_('COM_THM_GROUPS_NO_HIDING'), $icon);
    }

    /**
     * Creates an icon with a tooltip explaining that the element's display is protected from deletion and may have
     * limited editing options.
     *
     * @return string the HTML for the notice
     */
    public static function getSuppressionNotice()
    {
        $icon = '<i class="icon-question-circle hasTooltip" title="XXXX"></i>';
        return str_replace('XXXX', JText::_('COM_THM_GROUPS_LIMITED_SUPPRESSION'), $icon);
    }
}
