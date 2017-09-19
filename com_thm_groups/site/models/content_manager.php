<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelContent_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/content.php';

/**
 * Class retrieves information about content for the profile's content category
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelContent_Manager extends JModelList
{
	public $categoryID;

	/**
	 * Constructor
	 *
	 * @param   array $config config array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array();
		}

		$this->categoryID = THM_GroupsHelperContent::getCategoryID(JFactory::getUser()->id);

		parent::__construct($config);
	}

	/**
	 * Function to feed the data in the table body correctly to the list view
	 *
	 * @return array consisting of items in the body
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return empty($items) ? [] : $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$contentSelect = 'content.id, content.title, content.alias, content.checked_out, content.checked_out_time, ';
		$contentSelect .= 'content.catid, content.state, content.access, content.created, ';
		$contentSelect .= 'content.created_by, content.ordering, content.language, content.hits, content.publish_up, ';
		$contentSelect .= 'content.publish_down';
		$query->select($contentSelect);
		$query->select('language.title AS language_title');
		$query->select('ag.title AS access_level');
		$query->select('cats.title AS category_title');
		$query->select('users.name AS author_name');

		// TODO: Apparently these are VERY poorly named module parameters. RENAME THESE!
		$query->select('pContent.featured AS groups_featured');
		$query->from('#__content AS content');
		$query->leftJoin('#__languages AS language ON language.lang_code = content.language');
		$query->leftJoin('#__viewlevels AS ag ON ag.id = content.access');
		$query->leftJoin('#__categories AS cats ON cats.id = content.catid');
		$query->leftJoin('#__users AS users ON users.id = content.created_by');
		$query->leftJoin('#__thm_groups_users_content AS pContent ON pContent.contentID = content.id');
		$query->where("cats.id = '$this->categoryID'");

		$query->order('ordering ASC');

		return $query;
	}

	/**
	 * Method to test whether the session user has the permission to create a new article.
	 *
	 * @return    boolean    True if permission granted.
	 */
	public function hasUserRightToCreateArticle()
	{
		return JFactory::getUser()->authorise('core.create', 'com_content.category.' . $this->categoryID);
	}

	/**
	 * Overwrites the JModelList populateState function
	 *
	 * @param   string $ordering  the column by which the table is should be ordered
	 * @param   string $direction the direction in which this column should be ordered
	 *
	 * @return  void  sets object state variables
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$session = JFactory::getSession();
		$session->set($this->context . '.ordering', "ordering ASC");

		$this->setState('list.ordering', $ordering);
		$this->setState('list.direction', $direction);
		$this->setState('list.start', 0);
		$this->setState('list.limit', 0);
	}
}
