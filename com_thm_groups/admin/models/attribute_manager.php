<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelAttribute_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

define('VORNAME', 1);
define('NACHNAME', 2);
define('EMAIL', 4);
define('TITEL', 5);
define('POSTTITEL', 7);

require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THM_GroupsModelAttribute_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsModelAttribute_Manager extends THM_GroupsModelList
{
	protected $defaultOrdering = 'attribute.id';

	protected $defaultDirection = 'ASC';

	/**
	 * Constructor
	 *
	 * @param   array $config The config
	 */
	public function __construct($config = array())
	{
		$config['filter_fields'] = array(
			'attribute.id',
			'attribute.name',
			'dynamic.name'
		);

		parent::__construct($config);
	}

	/**
	 * Deletes attribute from database and removes all related entries from users_attribute
	 *
	 * @return bool
	 */
	public function delete()
	{
		$postVariables = JFactory::getApplication()->input->post->getArray(array());
		$attributeID   = $postVariables['cid'][0];

		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		// Remove related pictures from folder:
		$query->select('*')->from($dbo->qn('#__thm_groups_attribute'))->where('id = ' . (int) $attributeID);
		$dbo->setQuery($query);
		$attribute = $dbo->loadObject();

		if ($this->deletePictures($attribute))
		{
			// Delete attribute from database:
			$query = $dbo->getQuery(true);

			$query->delete($dbo->qn('#__thm_groups_attribute'))
				->where($dbo->qn('id') . ' = ' . $attributeID);

			$dbo->setQuery($query);

			$result = $dbo->execute();

			return $result? true : false;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Deletes pictures from folder
	 *
	 * @param   Object $attribute Object of attribute
	 *
	 * @return  boolean true on success, otherwise false
	 */
	private function deletePictures($attribute)
	{
		$dbo = JFactory::getDbo();

		// Get all Pictures from attribute
		$usersAttributeQuery = $dbo->getQuery(true);
		$usersAttributeQuery->select($dbo->qn(array('ID', 'value', 'attributeID')))
			->from($dbo->qn('#__thm_groups_users_attribute'))
			->where($dbo->qn('attributeID') . ' = ' . $attribute->id . '');
		$dbo->setQuery($usersAttributeQuery);
		$pictures = $dbo->loadObjectList();

		// Get path
		$path = json_decode($attribute->options)->path;

		// Delete files
		if (!empty($path))
		{
			foreach (scandir($path) as $folderPic)
			{
				foreach ($pictures as $pic)
				{
					if ($folderPic == $pic->value)
					{
						unlink($path . $folderPic);
					}
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$select = 'attribute.id, attribute.name, attribute.options, attribute.published, attribute.ordering, attribute.description, ';
		$select .= 'dynamic.name as dynamic_type_name';

		$query->select($select)->from('#__thm_groups_attribute AS attribute')
			->innerJoin('#__thm_groups_dynamic_type AS dynamic ON attribute.dynamic_typeID = dynamic.id');

		$this->setIDFilter($query, 'attribute.published', array('filter.published'));

		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$query->where("(attribute.name LIKE '%" . implode("%' OR attribute.name LIKE '%", explode(' ', $search)) . "%')");
		}

		$dynamic = $this->getState('filter.dynamic');

		if (!empty($dynamic) && $dynamic != '*')
		{
			$query->where("attribute.dynamic_typeID = '$dynamic'");
		}

		$this->setOrdering($query);

		return $query;
	}

	/**
	 * Function to get table headers
	 *
	 * @return array including headers
	 */
	public function getHeaders()
	{
		$ordering  = $this->state->get('list.ordering');
		$direction = $this->state->get('list.direction');

		$headers                = array();
		$headers['order']       = JHtml::_('searchtools.sort', '', 'attribute.ordering', $direction, $ordering, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');
		$headers['checkbox']    = '';
		$headers['id']          = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'attribute.id', $direction, $ordering);
		$headers['attribute']   = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_NAME', 'attribute.name', $direction, $ordering);
		$headers['published']   = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ATTRIBUTE_PUBLISHED', 'attribute.published', $direction, $ordering);
		$headers['dynamic']     = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_DYNAMIC_TYPE', 'dynamic.name', $direction, $ordering);
		$headers['description'] = JText::_('COM_THM_GROUPS_DESCRIPTION');

		return $headers;
	}

	/**
	 * Function to feed the data in the table body correctly to the list view
	 *
	 * @return array consisting of items in the body
	 */
	public function getItems()
	{
		$items  = parent::getItems();
		$return = array();

		if (empty($items))
		{
			return $return;
		}

		$url = "index.php?option=com_thm_groups&view=attribute_edit&id=";
		$generalOrder    = '<input type="text" style="display:none" name="order[]" size="5" ';
		$generalOrder    .= 'value="XX" class="width-20 text-area-order " />';
		$generalSortIcon = '<span class="sortable-handlerXXX"><i class="icon-menu"></i></span>';
		$generalLock     = '<span class="icon-lock hasTooltip" title="XXXX"></span>';
		$doNotDelete     = array(VORNAME, NACHNAME, EMAIL, TITEL, POSTTITEL);
		$canEdit         = JFactory::getUser()->authorise('core.edit', 'com_thm_groups');
		$index           = 0;

		$return['attributes'] = array('class' => 'ui-sortable');

		foreach ($items as $item)
		{
			$orderingActive = $this->state->get('list.ordering') == 'attribute.ordering';
			$iconClass      = '';

			if (!$canEdit)
			{
				$iconClass = ' inactive';
			}
			elseif (!$orderingActive)
			{
				$iconClass = ' inactive tip-top hasTooltip';
			}

			$lockTip       = JHtml::tooltipText($item->name, "COM_THM_GROUPS_CANT_DELETE_PREDEFINED_ELEMENT");
			$specificLock  = in_array($item->id, $doNotDelete) ? str_replace('XXXX', $lockTip, $generalLock) : '';
			$attributeText = ($canEdit) ? JHtml::_('link', $url . $item->id, $item->name) : $item->name;
			$specificOrder = ($canEdit AND $orderingActive) ? str_replace('XX', $item->ordering, $generalOrder) : '';

			$return[$index] = array();

			$return[$index]['attributes'] = array('class' => 'order nowrap center', 'id' => $item->id);

			$return[$index]['ordering']['attributes'] = array('class' => "order nowrap center", 'style' => "width: 40px;");
			$return[$index]['ordering']['value']      = str_replace('XXX', $iconClass, $generalSortIcon) . $specificOrder;

			$return[$index][0] = JHtml::_('grid.id', $index, $item->id);
			$return[$index][1] = $item->id;
			$return[$index][2] = $specificLock . $attributeText;
			$return[$index][3] = $this->getToggle($item->id, $item->published, 'attribute', '', 'published');
			$return[$index][4] = $item->dynamic_type_name;
			$return[$index][5] = $item->description;
			$index++;
		}

		return $return;
	}

	/**
	 * Overwrites the JModelList populateState function
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void  sets object state variables
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$static = $app->getUserStateFromRequest($this->context . '.filter.static', 'filter_static');
		$this->setState('filter.dynamic', $static);

		parent::populateState("attribute.id", "ASC");
	}
}
