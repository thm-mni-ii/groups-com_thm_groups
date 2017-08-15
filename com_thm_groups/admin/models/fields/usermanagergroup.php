<?php
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldUsermanagergroup extends JFormFieldList
{

	protected $type = 'usermanagergroup';

	/**
	 *
	 * @var    array
	 */
	protected static $options = array();

	/**
	 * Retrieves a list of non-empty user groups associated with roles. (Public and registered are ignored.)
	 *
	 * @return  Array
	 */
	public function getGroupsFromDB()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$nestedQuery = $dbo->getQuery(true);
		$nestedQuery
			->select('id')
			->from('#__thm_groups_users');


		$query
			->select('g.id, g.title')
			->from('#__usergroups AS g')
			->innerJoin('#__thm_groups_usergroups_roles AS a ON g.id = a.usergroupsID')
			->innerJoin('#__thm_groups_users_usergroups_roles AS b ON a.id = b.usergroups_rolesID')
			->where('b.usersID IN (' . $nestedQuery . ')')
			->where('g.id NOT IN  (1,2)')
			->group('g.id')
			->order('g.title ASC');

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
			$options[] = JHTML::_('select.option', '', JText::_('COM_THM_GROUPS_FILTER_BY_GROUP'));
			foreach ($arrayOfGroups as $key => $value) :
				$options[] = JHTML::_('select.option', $value['id'], $value['title']);
			endforeach;

			static::$options[$hash] = array_merge(static::$options[$hash], $options);
		}

		return static::$options[$hash];
	}
}