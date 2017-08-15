<?php
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldGroup extends JFormFieldList
{

	protected $type = 'group';

	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 */
	protected static $options = array();

	/**
	 * Returns a list of all user groups
	 *
	 * @return  Array
	 */
	public function getGroupsFromDB()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->select('a.id, a.title')
			->from('#__usergroups AS a')
			->innerJoin('#__thm_groups_usergroups_roles AS b ON a.id = b.usergroupsID')
			->group('a.title');

		$dbo->setQuery($query);
		$dbo->execute();

		return $dbo->loadAssocList();
	}

	/**
	 * Method to get the options to populate to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 */
	protected function getOptions()
	{
		// Accepted modifiers
		$hash = md5($this->element);

		if (!isset(static::$options[$hash]))
		{
			static::$options[$hash] = parent::getOptions();
			$options                = array();

			$arrayOfGroups = $this->getGroupsFromDB();

			// Convert array to options
			$options[] = JHTML::_('select.option', '', JText::_('JALL'));
			foreach ($arrayOfGroups as $key => $value) :
				$options[] = JHTML::_('select.option', $value['id'], $value['title']);
			endforeach;

			static::$options[$hash] = array_merge(static::$options[$hash], $options);
		}

		return static::$options[$hash];
	}
}