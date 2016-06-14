<?php
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('groupedlist');

// TODO create regex manager view
class JFormFieldRegex extends JFormFieldGroupedList
{

	protected $type = 'regex';

	protected function getGroups()
	{
		$groups     = array();
		$groups[][] = JHtml::_('select.option', '', 'No regex');

		$groups['Text'][] = JHtml::_('select.option', '^([a-zA-ZäöüÄÖÜ])*[a-zA-ZäöüÄÖÜ]$', JText::_('COM_THM_GROUPS_REGEX_ONLY_LETTERS'));
		$groups['Text'][] = JHtml::_('select.option', '^[0-9a-zA-ZäöüÄÖÜ]+$', JText::_('COM_THM_GROUPS_REGEX_LETTERS_AND_NUMBERS'));

		// TODO check if it works without backspaces
		// $groups['Text'][] = JHtml::_('select.option', '^(\\\\+49 )?(\\\\([0-9]{1,5}\\\\)|[0-9]{0,5}|\\\\(0\\\\)[0-9]{3,4})(\\\\/| )?([0-9]+\\\\ ?)*(\\\\ \\\\+[0-9]{1,3})?$', JText::_('COM_THM_GROUPS_PHONE'));
		// $groups['Text'][] = JHtml::_('select.option', '^([0-9a-zA-Z\\\\.]+)@(([\\\\w]|\\\\.\\\\w)+)\\\\.(\\\\w+)$', JText::_('COM_THM_GROUPS_EMAIL'));

		// TODO make right regex for Rooms in THM
		//$groups['Text'][] = JHtml::_('select.option', '^[A-E]{1}([0-9]{2}\\\\.|\\\\.)[0-9]{1,2}\\\\.([0-9]{2}[a-z]{0,1})$', JText::_('COM_THM_GROUPS_ROOM'));

		$groups['Number'][] = JHtml::_('select.option', '^[0-9]*$', JText::_('COM_THM_GROUPS_REGEX_ONLY_NUMBERS'));
		$groups['Number'][] = JHtml::_('select.option', '^[0-9]{4,5}$', JText::_('COM_THM_GROUPS_PLZ'));

		// $groups['Link'][] = JHtml::_('select.option', '^(https?:\\\\/\\\\/)?([\\\\da-z\\\\.-]+)\\\\.([a-z\\\\.]{2,6})([\\\\/\\\\w \\\\.-]*)*\\\\/?$', JText::_('COM_THM_GROUPS_URL'));
		// $groups['Link'][] = JHtml::_('select.option', '(http|ftp|https:\\\\/\\\\/){0,1}[\\\\w\\\\-_]+(\\\\.[\\\\w\\\\-_]+)+([\\\\w\\\\-\\\\.,@?^=%&amp;:/~\\\\+#]*[\\\\w\\\\-\\\\@?^=%&amp;/~\\\\+#])?', JText::_('COM_THM_GROUPS_HYPERLINK'));

		return $groups;
	}
}