<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldAuthors
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
require_once JPATH_ROOT . "/media/com_thm_groups/helpers/content.php";

/**
 * Class JFormFieldAuthors which returns authors of specific content.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class JFormFieldAuthors extends JFormFieldList
{

	protected $type = 'authors';

	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 */
	protected static $options = array();

	/**
	 * Returns a list of all authors associated with THM Groups, even they don't have
	 * articles in their categories
	 *
	 * @return  mixed  array on success, otherwise false
	 */
	public function getQPAuthors()
	{
		$dbo      = JFactory::getDbo();
		$catQuery = $dbo->getQuery(true);

		$rootCategory = THM_GroupsHelperContent::getRootCategory();
		$catQuery
			->select('users.id, users.name, cat.id AS catid')
			->from('#__users AS users')
			->leftJoin('#__categories AS cat on cat.created_user_id = users.id')
			->where("cat.parent_id = $rootCategory")
			->where("cat.published = 1")
			->order('users.name')
			->group('users.id');

		$dbo->setQuery($catQuery);

		try
		{
			$allProfiles = $dbo->loadAssocList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		foreach ($allProfiles as $index => $profile)
		{
			$contentQuery = $dbo->getQuery(true);
			$contentQuery->select("count('*')")->from('#__content')->where("catid = '{$profile['catid']}'");
			$dbo->setQuery($contentQuery);

			try
			{
				$contentCount = $dbo->loadResult();
			}
			catch (Exception $exception)
			{
				JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

				return false;
			}

			if (empty($contentCount))
			{
				unset($allProfiles[$index]);
			}
		}

		return $allProfiles;
	}

	/**
	 * Method to get the options to populate to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 */
	protected function getOptions()
	{
		$options = array();

		$rootCategory = THM_GroupsHelperContent::getRootCategory();

		if (empty($rootCategory))
		{
			return parent::getOptions();
		}

		$qpAuthors = $this->getQPAuthors();

		// Convert array to options
		foreach ($qpAuthors as $key => $value)
		{
			$options[] = JHTML::_('select.option', $value['id'], $value['name']);
		}

		return array_merge(parent::getOptions(), $options);
	}
}