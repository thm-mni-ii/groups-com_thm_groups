<?php
/**
 * HelperClass class
 *
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelMembers
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die('Restricted access');

/**
 * THMGroupsModelMembers class for component com_thm_groups
 *
 * @category  Joomla.Library
 * @package   lib_thm_groups
 * @link      www.thm.de
 * @since     Class available since Release 3.0
 */
class THMGroupsModelMembers
{
	/**
	 * The function, which returns a structure, that will be shown.
	 *
	 * @return    array     $strucitems contains sturcture with elements like Name, Lastname and so on
	 */
	public function getStrucktur()
	{
		$strucitems = array();
		$dbo        = JFactory::getDBO();
		$query      = $dbo->getQuery(true);

// 		$temp = 'SELECT a.id, a.field FROM `#__thm_groups_structure` as a Order by a.order';

		$query->select("a.id,a.field");
		$query->from("#__thm_groups_structure as a");
		$query->order("a.order");
		$dbo->setQuery($query);
		$data = $dbo->loadObjectList();

		foreach ($data as $structur)
		{
			$element        = new stdClass;
			$element->id    = $structur->id;
			$element->value = $structur->field;
			array_push($strucitems, $element);
		}

		return $strucitems;
	}

	/**
	 * Function, which returns input parameters
	 *
	 * @return    array     $dbo contains user information
	 */
	public function getKeyword()
	{

		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);
		$query->select('params');
		$query->from('#__extensions');
		$query->where('element = \'plg_thm_groups_content_members\'');
		$dbo->setQuery($query);
		$data       = $dbo->loadObjectList();
		$parameters = $data[0]->params;
		$dec        = json_decode($parameters, true);
		$keyword    = $dec['Keywords'];

		echo '<input type="hidden" id="keyword" name="keyword" value="' . $keyword . '">';
	}

	/**
	 * Returns a list of groups
	 *
	 * @param   int $count 1-person,2-group,3-list
	 *
	 * @return string the html for the group select box
	 */
	public function getListOfGroups($count)
	{
		$name = "";
		switch ($count)
		{
			case 2:
				$name = "groups";
				break;
			case 3:
				$name = "groups_list";
				break;
		}

		// Warning? value is not defined

		$selected = $this->value;
		$dbo      = JFactory::getDbo();
		$query    = $dbo->getQuery(true);
		$query->select('id,name');
		$query->from('#__thm_groups_groups');
		$dbo->setQuery($query);
		$list = $dbo->loadObjectList();

		$html = '<select name="' . $name . '" id="' . $name . '" size="1" id="paramsdefault_user" class="styled">'
			. "<option value=''>" . JText::_('COM_THM_GROUPS_GROUP') . "</option>";
		foreach ($list as $group)
		{
			$sel = '';
			if ($group->id == $selected)
			{
				$sel = "selected";
			}

			$html .= "<option value=" . $group->id . " $sel>" . $group->name . " </option>";
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Method to get select options
	 *
	 * @return array the group selection options
	 */
	public function getGroupSelectOptions()
	{

		$groups = $this->getGroupsHirarchy();

		// $jgroups = $this->getJoomlaGroups(); Alte Methode, kann gelöscht werden...
		$jgroups = $this->getUsergroups(true);

		$wasinjoomla   = false;
		$selectOptions = array();

		foreach ($groups as $group)
		{
			$injoomla = $group->injoomla == 1 ? true : false;
			if ($injoomla != $wasinjoomla)
			{
				$selectOptions[] = JHTML::_('select.option', -1, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - -', 'value', 'text', true);
			}

			$tempgroup = $group;
			$hirarchy  = "";
			while ($tempgroup->parent_id != 0)
			{
				$hirarchy .= "- ";
				foreach ($jgroups as $actualgroup)
				{
					if ($tempgroup->parent_id == $actualgroup->id)
					{
						$tempgroup = $actualgroup;
					}
				}
			}
			foreach ($jgroups as $jgroup)
			{
				if ($group->id == $jgroup->id)
				{
					$selectOptions[] = JHTML::_('select.option', $group->id, $hirarchy . $group->name);
				}
			}

			$wasinjoomla = $injoomla;
		}

		return $selectOptions;
	}

	/**
	 * Gets list of all groups.
	 *
	 * @access  public
	 * @return    bool|array  "false" on error|indexed rows with associative colums.
	 */
	public function getGroupsHirarchy()
	{
		$dbo = JFactory::getDBO();

		// Create SQL query string

		$nestedQuery = $dbo->getQuery(true);
		$nestedQuery->select('*');
		$nestedQuery->from("#__thm_groups_groups");
		$nestedQuery->where("injoomla = 0");
		$nestedQuery->order("name");

		$nestedQuery1 = $dbo->getQuery(true);
		$nestedQuery1->select('*');
		$nestedQuery1->from("#__thm_groups_groups");

		$nestedQuery2 = $dbo->getQuery(true);
		$nestedQuery2->select('joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla');
		$nestedQuery2->from("#__usergroups AS joo");
		$nestedQuery2->leftJoin("($nestedQuery1) AS thm ON joo.id = thm.id");
		$nestedQuery2->order("lft");

		$query = $dbo->getQuery(true);
		$query->select('thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla');
		$query->from("#__usergroups AS joo");
		$query->rightJoin("($nestedQuery) AS thm ON joo.id = thm.id UNION $nestedQuery2");

		$dbo->setQuery($query);
		$dbo->query();

		return $dbo->loadObjectList();
	}

	/**
	 * Returns a UL list of user groups with check boxes
	 *
	 * @param   boolean $checkSuperAdmin If false only super admins can add to super admin groups
	 *
	 * @return  bool|array  "false" on error|indexed rows with associative colums.
	 *
	 * @since   11.1
	 */
	public function getUsergroups($checkSuperAdmin = false)
	{
		static $count;

		$count++;

		$isSuperAdmin = JFactory::getUser()->authorise('core.admin');

		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('a.*, COUNT(DISTINCT b.id) AS level');
		$query->from($dbo->quoteName('#__usergroups') . ' AS a');
		$query->join('LEFT', $dbo->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->group('a.id, a.title, a.lft, a.rgt, a.parent_id');
		$query->order('a.lft ASC');
		$dbo->setQuery($query);
		$groups = $dbo->loadObjectList();

		// Check for a database error.
		if ($dbo->getErrorNum())
		{
			JError::raiseNotice(500, $dbo->getErrorMsg());

			return null;
		}

		$res = array();

		for ($i = 0, $n = count($groups); $i < $n; $i++)
		{
			$item = &$groups[$i];

			// If checkSuperAdmin is true, only add item if the user is superadmin or the group is not super admin
			if ((!$checkSuperAdmin) || $isSuperAdmin || (!JAccess::checkGroup($item->id, 'core.admin')))
			{
				$res[] = $item;
			}
		}

		return $res;
	}
}
