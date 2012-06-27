<?php
/**
 *@category    Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups.site
 *@name		   THMGroupsModelAdvanced
 *@description Advanced model of com_thm_groups
 *@author	   Dennis Priefer, dennis.priefer@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link		   www.mni.thm.de
 *@version	   3.0
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

/**
 * Advanced model class of component com_thm_groups
 *
 * Model for advanced context
 *
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelAdvanced extends JModel
{
	/**
	 * DAO
	 *
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Constructor
	 *@since Available since Release 3.0
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_db = & JFactory::getDBO();
	}

	/**
	 * Returns the correct view template
	 *
	 * @return string
	 */
	public function getView()
	{
		return $this->getHead() . $this->getList();
	}

	/**
	 * Get View parameters
	 *
	 * @return Object Parameter object
	 */
	public function getViewParams()
	{
		$mainframe = Jfactory::getApplication();
		return $mainframe->getParams();
	}

	/**
	 * Get Group number
	 *
	 * @return integer Group number
	 */
	public function getGroupNumber()
	{
		$params = $this->getViewParams();
		return $params->get('selGroup');
	}

	/**
	 * Print object
	 *
	 * @param   string  $topic   Topic
	 * @param   string  $object  Object
	 *
	 * @return void
	 */
	public function printObject($topic = '', $object = '')
	{
		if (!empty ($topic))
		{
			$topic = "<div class='com_gs_topic'>$topic</div>";
		}
		echo "<div>$topic$object</div>";
	}

	/**
	 * Get image output
	 *
	 * @param   string  $path  Path
	 * @param   string  $text  Text
	 * @param   string  $cssc  CSS class
	 *
	 * @return string
	 */
	public function getImage($path, $text, $cssc)
	{
		return JHTML::image(
				"modules/mod_thm_groups/$path",
				$text,
				array (
					'class' => $cssc
				)
			);
	}

	/**
	 * Get link output
	 *
	 * @param   string  $path  Path
	 * @param   string  $text  Text
	 * @param   string  $cssc  CSS class
	 *
	 * @return string
	 */
	public function getLink($path, $text, $cssc = '')
	{
		return "<a class=\"$cssc\" href=\"$path\" target=\"_blank\">$text</a>";
	}

	/**
	 * Get unsorted roles of a specific group
	 *
	 * @param   integer  $gid  Group id
	 *
	 * @return  array    Array with all roles of group with $gid
	 */
	public function getUnsortedRoles($gid)
	{
		$query = $this->_db->getQuery(true);
		$query->select('distinct rid');
		$query->from('#__thm_groups_groups_map');
		$query->where("gid=$gid");

		$this->_db->setQuery($query);
		$unsortedRoles = $this->_db->loadObjectList();
		$arrUnsortedRoles = array ();
		if (isset ($unsortedRoles))
		{
			foreach ($unsortedRoles as $role)
			{
				$arrUnsortedRoles[] = $role->rid;
			}
		}
		return $arrUnsortedRoles;
	}

	/**
	 * Qery, if actual user can edit the group member attributes
	 *
	 * @return  integer  if can edit return 1, else 0
	 */
	public function canEdit()
	{
		$canEdit = 0;
		$groupid = $this->getGroupNumber();
		$user    = & JFactory::getUser();
		$query   = $this->_db->getQuery(true);

		$query->select('rid');
		$query->from($db->qn('#__thm_groups_groups_map'));
		$query->where('uid = ' . $user->id);
		$query->where("gid = $groupid", 'AND');

		$this->_db->setQuery($query);
		$userRoles = $this->_db->loadObjectList();
		foreach ($userRoles as $userRole)
		{
			if ($userRole->rid == 2)
			{
				$canEdit = 1;
			}
		}
		return $canEdit;
	}

	/**
	 * Get all attribute types
	 *
	 * @return  Object
	 */
	public function getTypes()
	{
		$nestedQuery = $this->_db->getQuery(true);
		$query       = $this->_db->getQuery(true);

		$nestedQuery->select('a.type');
		$nestedQuery->from($db->qn('#__thm_groups_structure') . ' as a');
		$nestedQuery->order('a.order');

		$query->select('Type');
		$query->from($db->qn('#__thm_groups_relationtable'));
		$query->where('Type in (' . $nestedQuery . ')');

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Returns array with every group members and related attribute. The group is predefined as view parameter
	 *
	 * @return  array  array with group members and related user attributes
	 */
	public function getData()
	{
		// Contains the number of the group, e.g. 10
		$groupid           = $this->getGroupNumber();
		$params            = $this->getViewParams();
		$sortedRoles       = $params->get('sortedgrouproles');
		$types             = $this->getTypes();
		$puffer            = array();
		$result            = array();
		$usedUser          = array();
		$showStructure     = array();
		$paramStructSelect = $params->get('struct');
		$data             = array();

		if ($sortedRoles == "")
		{
			$arrSortedRoles = $this->getUnsortedRoles($groupid);
		}
		else
		{
			$arrSortedRoles = explode(",", $sortedRoles);
		}

		if (isset ($paramStructSelect))
		{
	        foreach ($paramStructSelect as $item)
	        {
				$tempItem              = array();
				$tempItem['id']        = substr($item, 0, strlen($item) - 2);
				$tempItem['showName']  = substr($item, -2, 1) == "1" ? true : false;
				$tempItem['wrapAfter'] = substr($item, -1, 1) == "1" ? true : false;
				$showStructure[]       = $tempItem;
	        }
		}
		else
		{
		}

		foreach ($arrSortedRoles as $sortRole)
		{
			$query = $this->_db->getQuery(true);

			$query->select('distinct gm.uid, t.value');
			$query->from($db->qn('#__thm_groups_groups_map') . ' as gm');
			$query->from($db->qn('#__thm_groups_text') . ' as t');
			$query->where("gm.gid = $groupid");
			$query->where('gm.rid != 2', 'AND');
			$query->where('gm.uid = t.userid', 'AND');
			$query->where('t.structid = 2', 'AND');
			$query->where("gm.rid = $sortRole", 'AND');
			$query->order('t.value');

			$this->_db->setQuery($query);
			$groupMember = $this->_db->loadObjectList();

			foreach ($groupMember as $member)
			{
				foreach ($types as $type)
				{
					$query->clear();
					$query->select('structid, value, publish');
					$query->from($db->qn('#__thm_groups_' . strtolower($type->Type)) . '  as a');
					$query->from($db->qn('#__thm_groups_groups_map') . ' as gm');
					$query->where('a.userid = ' . $member->uid);
					$query->where('a.userid = gm.uid', 'AND');
					$query->where("gm.rid = $sortRole", 'AND');
					$query->where("gm.gid = $groupid", 'AND');

					$this->_db->setQuery($query);

					$puffer                 = $this->_db->loadObjectList();
					$result[$member->uid][] = $puffer;
				}

				if (!in_array($member->uid, $usedUser))
				{
					$sortedMember[$member->uid] = $result[$member->uid];
					$usedUser[]                 = $member->uid;
				}
				else
				{
				}
			}
		}
		$structure = $this->getStructure();
		foreach ($sortedMember as $key => $memberdata)
		{
			$data[$key] = array();
			foreach ($structure as $structureItem)
			{
				foreach ($memberdata as $type)
				{
					foreach ($type as $struct)
					{
						foreach ($showStructure as $selection)
						{
							if ($struct->structid == $selection['id'] && $struct->structid == $structureItem->id)
							{
								$puffer['structid']   = $struct->structid;
								$puffer['structname'] = $selection['showName'];
								$puffer['structwrap'] = $selection['wrapAfter'];
								$puffer['type']       = $structureItem->type;
								if ($struct->value == "" && $structureItem->type == "PICTURE")
								{
									$puffer['value'] = $this->getExtra($struct->structid, $structureItem->type);
								}
								else
								{
									$puffer['value'] = $struct->value;
								}
								array_push($data[$key], $puffer);
							}
						}
					}
				}
			}
		}
		return $data;
	}

	/**
	 * Get attribute structure
	 *
	 * @return  ObjectList  Objectlist with defined structure of attributes
	 */
	public function getStructure()
	{
		$query = $this->_db->getQuery(true);

		$query->select('*');
		$query->from('#__thm_groups_structure AS a');
		$query->order('a.order');

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get additional attribute parameter
	 *
	 * @param   integer  $structid  Structure id
	 * @param   string   $type      Attribute type
	 *
	 * @return  Object    Array with all roles of group with $gid
	 */
	public function getExtra($structid, $type)
	{
		$query = $this->_db->getQuery(true);

		$query->select('value');
		$query->from('#__thm_groups_' . strtolower($type) . '_extra');
		$query->where("structid = $structid");

		$this->_db->setQuery($query);
		$res = $this->_db->loadObject();
		if (isset($res))
		{
		   	return $res->value;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get Data for table view
	 *
	 * @return  array    Two-dimensional array with group members (left and right)
	 */
	public function getDataTable()
	{
		$memberleft = array();
		$memberright = array();
		$i = 0;
		$_data = $this->getData();
		if (!empty($_data))
		{
			foreach ($_data as $key => $member)
			{
				if ($i == 0)
				{
					$memberleft[$key] = $member;
					$i++;
				}
				else
				{
					$memberright[$key] = $member;
					$i--;
				}
			}
		}

		$_data = array();
		$_data['left']  = $memberleft;
		$_data['right'] = $memberright;

		return $_data;
	}
}
