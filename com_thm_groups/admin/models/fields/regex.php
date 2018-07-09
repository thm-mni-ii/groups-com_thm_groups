<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('groupedlist');

// TODO create regex manager view
class JFormFieldRegex extends JFormFieldGroupedList
{

    protected $type = 'regex';

    /**
     * Creates predefined regexes for typical case scenarios
     *
     * @return array an array of regexes grouped according to their usage
     *
     * @since version
     */
    protected function getGroups()
    {
        $groups     = [];
        $groups[][] = JHtml::_('select.option', '', 'No regex');

        $groups['Text'][] = JHtml::_('select.option', '^([a-zA-ZäöüÄÖÜ])*[a-zA-ZäöüÄÖÜ]$',
            JText::_('COM_THM_GROUPS_REGEX_ONLY_LETTERS'));
        $groups['Text'][] = JHtml::_('select.option', '^[0-9a-zA-ZäöüÄÖÜ]+$',
            JText::_('COM_THM_GROUPS_REGEX_LETTERS_AND_NUMBERS'));

        $groups['Number'][] = JHtml::_('select.option', '^[0-9]*$', JText::_('COM_THM_GROUPS_REGEX_ONLY_NUMBERS'));
        $groups['Number'][] = JHtml::_('select.option', '^[0-9]{4,5}$', JText::_('COM_THM_GROUPS_PLZ'));

        return $groups;
    }
}
