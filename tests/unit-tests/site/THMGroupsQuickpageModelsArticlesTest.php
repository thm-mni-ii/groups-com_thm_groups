<?php

/**
 * @version     v1.2.0
 * @category    Joomla component
 * @package	    THM_Quickpages
 * @subpackage  com_thm_quickpages.site
 * @author	    Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link		www.mni.thm.de
 */

require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_thm_groups' .
	DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'articles.php';

/**
 * LibThmQuickpagesTest class for library lib_thm_quickpages
 *
 * @category  Joomla.Component.Site
 * @package   thm_quickpages
 * @since     v1.2.0
 */
class ModelsArticleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var lib_thm_quickpages
	 */
	protected $instance;

	protected static $comThmQuickpagesExists, $quickpagesCatId, $quickpagesCatIdExists, $repositoryCatId, $repositoryCatIdExists, $comThmQuickpagesId;

	/**
	 * @var The ID for DB entries.
	 */
	const ENTRY_ID = 99999;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called one time before all tests are executed.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass()
	{
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['SCRIPT_NAME'] = '/joomla/index.php';

		$db =& JFactory::getDBO();

		// Extension entry for com_thm_quickpages
		$comThmQuickpages = JComponentHelper::getComponent('com_thm_quickpages');

		self::$comThmQuickpagesExists = isset($comThmQuickpages->id);

		if (self::$comThmQuickpagesExists)
		{
			self::$comThmQuickpagesId = $comThmQuickpages->id;

			// Check params
			self::$quickpagesCatId = ($comThmQuickpages->params->get('content_category'));
			self::$quickpagesCatIdExists = self::$quickpagesCatId > 0;

			self::$repositoryCatId = ($comThmQuickpages->params->get('repository_category'));
			self::$repositoryCatIdExists = self::$repositoryCatId > 0;

			if (self::$quickpagesCatIdExists)
			{
			}
			else
			{
				self::$quickpagesCatId = self::createCategory('quickpages');
			}

			if (self::$repositoryCatIdExists)
			{
			}
			else
			{
				self::$repositoryCatId = self::createCategory('repository');
			}
		}
		else
		{
			// Insert new
			self::$quickpagesCatId = self::createCategory('quickpages');
			self::$quickpagesCatId = self::createCategory('repository');
		}

		// Update params
		JComponentHelper::getParams('com_thm_quickpages')->set('content_category', self::$quickpagesCatId);
		JComponentHelper::getParams('com_thm_quickpages')->set('repository_category', self::$repositoryCatId);

		// Insert DB entries for testing
		$db =& JFactory::getDBO();

		// #__categories
		$query = $db->getQuery(true);

		$query->insert("#__categories")
			->set("id = " . self::ENTRY_ID)
			->set("extension = 'com_content'")
			->set("alias = 'testCategory'");

		$db->setQuery($query);
		$db->query();

		// #__content
		$query = $db->getQuery(true);

		$query->insert("#__content")
			->set("id = " . self::ENTRY_ID)
			->set("asset_id = " . self::ENTRY_ID)
			->set("title = 'test'")
			->set("alias = 'testArticle'")
			->set("catid = " . self::ENTRY_ID)
			->set("state = 1")
			->set("created_by = " . self::ENTRY_ID);

		$db->setQuery($query);
		$db->query();

		// #__users
		$query = $db->getQuery(true);

		$query->insert("#__users")
			->set("id = " . self::ENTRY_ID)
			->set("name = 'testName'")
			->set("username = 'testUserName'");

		$db->setQuery($query);
		$db->query();

		// #__thm_quickpages_map
		$query = $db->getQuery(true);

		$query->insert(THMLibThmQuickpages::TABLE_NAME)
			->set("id = " . self::ENTRY_ID)
			->set("id_kind = '" . THMLibThmQuickpages::TABLE_USER_ID_KIND . "'")
			->set("catid = " . self::ENTRY_ID);

		$db->setQuery($query);
		$db->query();

		// #__thm_groups_groups
		$query = $db->getQuery(true);

		$query->insert(THMLibThmQuickpages::TABLE_NAME_THM_GROUPS_GROUPS)
			->set("id = " . self::ENTRY_ID)
			->set("name = 'testGroup'")
			->set("mode = 'quickpage'");

		$db->setQuery($query);
		$db->query();

		// #__thm_groups_groups_map
		$query = $db->getQuery(true);

		$query->insert(THMLibThmQuickpages::TABLE_NAME_THM_GROUPS_GROUPS_MAP)
			->set("uid = " . self::ENTRY_ID)
			->set("gid = " . self::ENTRY_ID);

		$db->setQuery($query);
		$db->query();

		// #__thm_groups_multiselect
		$query = $db->getQuery(true);

		$query->insert(THMLibThmQuickpages::TABLE_NAME_THM_GROUPS_MULTISELECT)
			->set("userid = " . self::ENTRY_ID)
			->set("structid = 6")
			->set("value = 'Quickpage'");

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Create an entry in #__categories for testing
	 *
	 * @param   string  $alias  The alias of the category
	 *
	 * @return  int  ID of the category entry
	 */
	private function createCategory($alias)
	{
		$properties['path'] = $alias;
		if ($alias == 'repository')
		{
			$properties['extension'] = THMLibThmQuickpages::COM_NAME_REPOSITORY;
		}
		else
		{
			$properties['extension'] = 'com_content';
		}
		$properties['title'] = ucfirst($alias);
		$properties['alias'] = $alias;

		$table = JTable::getInstance('Category');
		$table->setLocation(1, 'last-child');
		$table->save($properties);

		return $table->get('id');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called one time after all tests are executed.
	 *
	 * @return void
	 */
	public static function tearDownAfterClass()
	{
		$db =& JFactory::getDBO();

		$table = JTable::getInstance('Category');

		if (self::$comThmQuickpagesExists)
		{
			if (!self::$quickpagesCatIdExists && !self::$repositoryCatIdExists)
			{
				$table->load(self::$quickpagesCatId);
				$table->delete();
				$table->load(self::$repositoryCatId);
				$table->delete();
			}
			elseif (!self::$quickpagesCatIdExists) /* self::$repositoryCatIdExists == true */
			{
				$table->load(self::$quickpagesCatId);
				$table->delete();
			}
			elseif (!self::$repositoryCatIdExists) /* self::$quickpagesCatIdExists == true */
			{
				$table->load(self::$repositoryCatId);
				$table->delete();
			}
			else /* self::$repositoryCatIdExists && self::$quickpagesCatIdExists == true */
			{
			}
		}
		else
		{
			$table->load(self::$quickpagesCatId);
			$table->delete();
			$table->load(self::$repositoryCatId);
			$table->delete();

			$query = $db->getQuery(true);

			$query->delete()
				->from("#__extensions")
				->where("extension_id = " . self::$comThmQuickpagesId);

			$db->setQuery($query);
			$db->query();
		}

		// Delete DB entries after testing
		$db =& JFactory::getDBO();

		// #__categories
		$query = $db->getQuery(true);

		$query->delete()
			->from("#__categories")
			->where("id = " . self::ENTRY_ID);

		$db->setQuery($query);
		$db->query();

		// #__content
		$query = $db->getQuery(true);

		$query->delete()
			->from("#__content")
			->where("id = " . self::ENTRY_ID);

		$db->setQuery($query);
		$db->query();

		// #__users
		$query = $db->getQuery(true);

		$query->delete()
			->from("#__users")
			->where("id = " . self::ENTRY_ID)
			->where("name = 'testName'")
			->where("username = 'testUserName'");

		$db->setQuery($query);
		$db->query();

		// #__thm_quickpages_map
		$query = $db->getQuery(true);

		$query->delete()
			->from(THMLibThmQuickpages::TABLE_NAME)
			->where("id = " . self::ENTRY_ID, 'OR')
			->where("id = " . (self::ENTRY_ID + 2), 'OR')
			->where("id = " . (self::ENTRY_ID + 3), 'OR')
			->where("id = " . (self::ENTRY_ID + 4));

		$db->setQuery($query);
		$db->query();

		// #__thm_groups_groups
		$query = $db->getQuery(true);

		$query->delete()
			->from(THMLibThmQuickpages::TABLE_NAME_THM_GROUPS_GROUPS)
			->where("id = " . self::ENTRY_ID)
			->where("name = 'testGroup'")
			->where("mode = 'quickpage'");

		$db->setQuery($query);
		$db->query();

		// #__thm_groups_groups_map
		$query = $db->getQuery(true);

		$query->delete()
			->from(THMLibThmQuickpages::TABLE_NAME_THM_GROUPS_GROUPS_MAP)
			->where("uid = " . self::ENTRY_ID)
			->where("gid = " . self::ENTRY_ID);

		$db->setQuery($query);
		$db->query();

		// #__thm_groups_multiselect
		$query = $db->getQuery(true);

		$query->delete()
			->from(THMLibThmQuickpages::TABLE_NAME_THM_GROUPS_MULTISELECT)
			->where("userid = " . self::ENTRY_ID)
			->where("structid = 6")
			->where("value = 'Quickpage'");

		$db->setQuery($query);
		$db->query();

		$categoriesTable = JTable::getInstance('Category');
		$categoriesTable->rebuild();
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		// Current user
		$currUser = JFactory::getUser();
		$currUser->set('id', self::ENTRY_ID);

		$this->instance = new THMGroupsModelArticles;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$this->instance = null;
		parent::tearDown();
	}

	/**
	 * Test the getProfileIdentData
	 *
	 * @return void
	 */
	public function testgetProfileIdentData()
	{
		$result_1 = $this->instance->getProfileIdentData();

		$result_2 = THMLibThmQuickpages::getPageProfileDataByRequest(self::ENTRY_ID);

		$this->assertEquals($result_1, $result_2);
	}

	/**
	 * Test the getCategories
	 *
	 * @return void
	 */
	public function testgetCategories()
	{
		$result = $this->instance->getCategories();
		$result = $result[0];

		$this->assertEquals($result->id, self::ENTRY_ID);
		$this->assertEquals($result->alias, "testCategory");
	}

	/**
	 * Test the getAuthors
	 *
	 * @return void
	 */
	public function testgetAuthors()
	{
		$results = $this->instance->getAuthors();

		$notFound = true;

		foreach ($results as $result)
		{
			if ($result->value == self::ENTRY_ID)
			{
				$this->assertEquals($result->text, "testName");

				$notFound = false;
			}
		}

		$this->assertFalse($notFound);
	}
}
