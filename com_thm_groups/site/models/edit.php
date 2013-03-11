<?php
/**
 * @version     v3.2.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name		THMGroupsModeledit
 * @description THMGroupsModeledit file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modelform');

/**
 * THMGroupsModeledit class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModeledit extends JModelForm
{
	/**
	 * Constructor

	 * gets form for edit
	 */
	public function __construct()
	{
		parent::__construct();
		$this->getForm();
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * 
	 * @return mixed A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_thm_groups.edit', 'edit', array('load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get data.
	 *
	 * @return data object from database
	 */
	public function getData()
	{
		$cid   = JRequest::getVar('gsuid', '');
		$types = $this->getTypes();
		$db = JFactory::getDBO();
		$puffer = array();
		$result = array();

		foreach ($types as $type)
		{
			/*
			$query = "SELECT structid, value, publish FROM #__thm_groups_" . strtolower($type->Type) . " as a where a.userid = " . $cid;
			*/
			$query = $db->getQuery(true);
			$query->select('structid, value, publish');
			$query->from($db->qn('#__thm_groups_' . strtolower($type->Type)) . ' AS a');
			$query->where('a.userid = ' . $cid);

			$db->setQuery($query);
			if (!is_null($db->loadObjectList()))
			{
				array_push($puffer, $db->loadObjectList());
			}
		}

		foreach ($puffer as $type)
		{
			foreach ($type as $row)
			{
				array_push($result, $row);
			}
		}
		return $result;
	}

	/**
	 * Method to get structure
	 *
	 * @return data object from database
	 */
	public function getStructure()
	{
		$db = JFactory::getDBO();
		/*
		$query = "SELECT * FROM #__thm_groups_structure as a ORDER BY a.order";
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_structure') . " AS a");
		$query->order('a.order');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to get types
	 *
	 * @return data object from database
	 */
	public function getTypes()
	{
		$db = JFactory::getDBO();
		/*
		$query = "SELECT Type FROM #__thm_groups_relationtable " . "WHERE Type in (SELECT type FROM #__thm_groups_structure)";
		*/
		$nestedQuery = $db->getQuery(true);
		$query = $db->getQuery(true);

		$nestedQuery->select('type');
		$nestedQuery->from($db->qn('#__thm_groups_structure'));

		$query->select('Type');
		$query->from($db->qn('#__thm_groups_relationtable'));
		$query->where('Type in (' . $nestedQuery . ')');

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store()
	{
		$db = JFactory::getDBO();
		$structure = $this->getStructure();
		$userid	= JRequest::getVar('userid');
		$err	   = 0;
		foreach ($structure as $structureItem)
		{
			$puffer = null;
			$field  = JRequest::getVar($structureItem->field, '', 'post', '', JREQUEST_ALLOWHTML);
			$publish = 0;
			if ($structureItem->type == 'MULTISELECT')
			{
				$field = implode(';', $field);
			}

			$publishPuffer = JRequest::getVar('publish' . str_replace(" ", "", $structureItem->field));

			if (isset($publishPuffer))
			{
				$publish = 1;
			}

			/*
			$query = "SELECT structid FROM #__thm_groups_" . strtolower($structureItem->type)
			. " WHERE userid=" . $userid . " AND structid=" . $structureItem->id;
			*/
			$query = $db->getQuery(true);
			$query->select('structid');
			$query->from($db->qn('#__thm_groups_' . strtolower($structureItem->type)));
			$query->where('userid = ' . $userid);
			$query->where('structid = ' . $structureItem->id);

			$db->setQuery($query);
			$puffer = $db->loadObject();
			
			if (isset($structureItem->field))
			{
				if (isset($puffer))
				{
					$query = $db->getQuery(true);
					/*
					$query = "UPDATE #__thm_groups_" . strtolower($structureItem->type) . " SET";
					*/
					$query->update($db->qn('#__thm_groups_' . strtolower($structureItem->type)));
					if ($structureItem->type != 'PICTURE' && $structureItem->type != 'TABLE')
					{
						/*
						$query .= " value='" . $field . "',";
						*/
						$query->set("`value` = \"" . htmlspecialchars($field) . "\"");
					}
					/*
					 $query .= " publish='" . $publish . "'" . " WHERE userid=" . $userid . " AND structid=" . $structureItem->id;
					 */
					$query->set("`publish` = '" . $publish . "'");
					$query->where('userid = ' . $userid);
					$query->where('structid = ' . $structureItem->id);
				}
				else
				{
					/*
					$query = "INSERT INTO #__thm_groups_" . strtolower($structureItem->type)
					. " ( `userid`, `structid`, `value`, `publish`)"
					. " VALUES ($userid" . ", " . $structureItem->id . ", '" . $field . "'" . ", " . $publish . ")";
					*/
					$query = $db->getQuery(true);
					$query->insert($db->qn('#__thm_groups_' . strtolower($structureItem->type)));
					$query->set("`userid` = '" . $userid . "'");
					$query->set("`structid` = '" . $structureItem->id . "'");
					$query->set("`value` = \"" . htmlspecialchars($field) . "\"");
					$query->set("`publish` = '" . $publish . "'");
				}
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
			}
			if ($structureItem->type == 'PICTURE' && $_FILES[$structureItem->field]['name'] != "")
			{
				if (!$this->updatePic($userid, $structureItem->id, $structureItem->field))
				{
					$err = 1;
				}
			}
		}
		if (!$err)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete a picture
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function delPic()
	{
		$db = JFactory::getDBO();
		$uid	  = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$extra = $this->getExtra($structid, "PICTURE");
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_picture'));
		$query->set("`value` = '$extra'");
		$query->where('userid = ' . $uid);
		$query->where('structid = ' . $structid);

		$db->setQuery($query);

		if ($db->query())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to update a picture
	 *
	 * @param   Int     $uid       UserID
	 * @param   Int     $structid  StructID
	 * @param   Object  $picField  Picturefield
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function updatePic($uid, $structid, $picField)
	{
		require_once JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "helper" . DS . "thm_groups_pictransform.php";
		try
		{
			$pt = new THMPicTransform($_FILES[$picField]);
			$compath = "com_thm_groups";
			$pt->safeSpecial(
				JPATH_ROOT . DS . $this->getPicPath($structid) . DS, // "components" . DS . "com_thm_groups" . DS . "img" . DS . "portraits" . DS,
				$uid . "_" . $structid, 200, 200, "JPG");
			if (JModuleHelper::isEnabled('mod_thm_groups')->id != 0)
			{
				$pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups" . DS . "images" . DS, $uid . "_" . $structid, 200, 200, "JPG");
			}
			if (JModuleHelper::isEnabled('mod_thm_groups_smallview')->id != 0)
			{
				$modpath = "mod_thm_groups_smallview";
				$pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . $modpath . DS . "images" . DS, $uid . "_" . $structid, 200, 200, "JPG");
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		$db = JFactory::getDBO();
		/*
		$query = "UPDATE #__thm_groups_picture SET value='" . $uid . "_" . $structid . ".jpg' WHERE userid = $uid AND structid=$structid";
		*/
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_picture'));
		$query->set("`value` = '" . $uid . "_" . $structid . ".jpg'");
		$query->where('userid = ' . $uid);
		$query->where('structid = ' . $structid);
		$db->setQuery($query);

		if ($db->query())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get extra data
	 *
	 * @param   Int     $structid  StructID
	 * @param   String  $type      Picturefield
	 *
	 * @access	public
	 * @return	null / value
	 */
	public function getExtra($structid, $type)
	{
		$db = JFactory::getDBO();
		/*
		$query = "SELECT value FROM #__thm_groups_" . strtolower($type) . "_extra WHERE structid=" . $structid;
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_' . strtolower($type) . '_extra'));
		$query->where('structid = ' . $structid);
		$db->setQuery($query);
		$res = $db->loadObject();
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
	 * Method to get extra data
	 *
	 * @param   Int  $structid  StructID
	 *
	 * @access	public
	 * @return	null / value
	 */
	public function getPicPath($structid)
	{
		$db = JFactory::getDBO();
		/*
			$query = "SELECT value FROM #__thm_groups_" . strtolower($type) . "_extra WHERE structid=" . $structid;
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_picture_extra'));
		$query->where('structid = ' . $structid);
		$db->setQuery($query);
		$res = $db->loadObject();
		if (isset($res->path))
		{
			return $res->path;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Adds Row to database table
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function addTableRow()
	{
		$db = JFactory::getDBO();
		$uid	  = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$arrRow   = array();
		$arrValue = array();
		$err	  = 0;

		/*
		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		*/
		$query = $db->getQuery(true);
		$query->select('value');
		$query->from($db->qn('#__thm_groups_table'));
		$query->where('structid = ' . $structid);
		$query->where('userid = ' . $uid);

		$db->setQuery($query);
		$res	= $db->loadObject();
		$oValue = json_decode($res->value);
		foreach ($oValue as $row)
		{
			$arrValue[] = $row;
		}

		/*
		$query = "SELECT value FROM #__thm_groups_table_extra WHERE structid=" . $structid;
		*/
		$query = $db->getQuery(true);
		$query->select('value');
		$query->from($db->qn('#__thm_groups_table_extra'));
		$query->where('structid = ' . $structid);
		$db->setQuery($query);

		$resHead = $db->loadObject();
		$head	= explode(';', $resHead->value);

		foreach ($head as $headItem)
		{
			$headItem		  = str_replace(" ", "_", $headItem);
			$value			 = JRequest::getVar("TABLE$structid$headItem", '', 'POST', 'STRING', JREQUEST_ALLOWRAW);
			$arrRow[$headItem] = $value;
		}

		$arrValue[] = $arrRow;
		$jsonValue = json_encode($arrValue);

		$jsonValue = str_replace("\u00c4", "&Auml;", $jsonValue);
		$jsonValue = str_replace("\u00e4", "&auml;", $jsonValue);
		$jsonValue = str_replace("\u00d6", "&Ouml;", $jsonValue);
		$jsonValue = str_replace("\u00f6", "&ouml;", $jsonValue);
		$jsonValue = str_replace("\u00dc", "&Uuml;", $jsonValue);
		$jsonValue = str_replace("\u00fc", "&uuml;", $jsonValue);
		$jsonValue = str_replace("\u00df", "&szlig;", $jsonValue);
		$jsonValue = str_replace("\u20ac", "&euro;", $jsonValue);

		if (isset($res))
		{
			/*
			$query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
			*/
			$query = $db->getQuery(true);
			$query->update($db->qn('#__thm_groups_table'));
			$query->set("`value` = '" . $jsonValue . "'");
			$query->where('userid = ' . $uid);
			$query->where('structid = ' . $structid);
		}
		else
		{
			/*
			$query = "INSERT INTO #__thm_groups_table ( `userid`, `structid`, `value`)"
			. " VALUES ($uid" . ", " . $structid . ", '" . $jsonValue . "')";
			*/
			$query = $db->getQuery(true);
			$query->insert($db->qn('#__thm_groups_table'));
			$query->set("`userid` = '" . $uid . "'");
			$query->set("`structid` = '" . $structid . "'");
			$query->set("`value` = '" . $jsonValue . "'");
		}
		$db->setQuery($query);
		if (!$db->query())
		{
			$err = 1;
		}
		if (!$err)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Deletes Row from database table
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function delTableRow()
	{
		$db = JFactory::getDBO();
		$uid	  = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$key	  = JRequest::getVar('tablekey');

		// $arrRow   = array();
		$arrValue = array();
		$err	  = 0;

		/*
		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		*/
		$query = $db->getQuery(true);
		$query->select('value');
		$query->from($db->qn('#__thm_groups_table'));
		$query->where('structid = ' . $structid);
		$query->where('userid = ' . $uid);

		$db->setQuery($query);
		$res	= $db->loadObject();
		$oValue = json_decode($res->value);
		foreach ($oValue as $row)
		{
			$arrValue[] = $row;
		}
		array_splice($arrValue, $key, 1);

		$jsonValue = json_encode($arrValue);
		/*
		$query	 = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
		*/
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_table'));
		$query->set("`value` = '" . $jsonValue . "'");
		$query->where('userid = ' . $uid);
		$query->where('structid = ' . $structid);
		$db->setQuery($query);

		if (!$db->query())
		{
			$err = 1;
		}

		if (!$err)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Edits Row from database table
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function editTableRow()
	{
		$db = JFactory::getDBO();
		$uid	  = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$key	  = JRequest::getVar('tablekey');
		$arrRow   = array();
		$arrValue = array();
		$err	  = 0;

		/*
		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		*/
		$query = $db->getQuery(true);
		$query->select('value');
		$query->from($db->qn('#__thm_groups_table'));
		$query->where('structid = ' . $structid);
		$query->where('userid = ' . $uid);

		$db->setQuery($query);
		$res	= $db->loadObject();
		$oValue = json_decode($res->value);
		foreach ($oValue as $row)
		{
			$arrValue[] = $row;
		}

		/*
		$query = "SELECT value FROM #__thm_groups_table_extra WHERE structid=" . $structid;
		*/
		$query = $db->getQuery(true);
		$query->select('value');
		$query->from($db->qn('#__thm_groups_table_extra'));
		$query->where('structid = ' . $structid);

		$db->setQuery($query);
		$resHead = $db->loadObject();
		$head	= explode(';', $resHead->value);

		foreach ($head as $headItem)
		{
			$headItem		  = str_replace(" ", "_", $headItem);
			$value			 = JRequest::getVar("TABLE$structid$headItem", '', 'POST', 'STRING', JREQUEST_ALLOWRAW);
			$arrRow[$headItem] = $value;
		}
		/*foreach($arrValue[$key] as $field=>$row) {
		$arrRow[$field] = JRequest::getVar('TABLE'.$structid.$field);
		var_dump('TABLE'.$structid.utf8_decode($field));
		}*/

		$arrValue[$key] = $arrRow;
		$jsonValue	  = json_encode($arrValue);
		$jsonValue	  = str_replace("\u00c4", "&Auml;", $jsonValue);
		$jsonValue	  = str_replace("\u00e4", "&auml;", $jsonValue);
		$jsonValue	  = str_replace("\u00d6", "&Ouml;", $jsonValue);
		$jsonValue	  = str_replace("\u00f6", "&ouml;", $jsonValue);
		$jsonValue	  = str_replace("\u00dc", "&Uuml;", $jsonValue);
		$jsonValue	  = str_replace("\u00fc", "&uuml;", $jsonValue);
		$jsonValue	  = str_replace("\u00df", "&szlig;", $jsonValue);
		$jsonValue	  = str_replace("\u20ac", "&euro;", $jsonValue);
		/*
		$query		  = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
		*/
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_table'));
		$query->set("`value` = '" . $jsonValue . "'");
		$query->where('userid = ' . $uid);
		$query->where('structid = ' . $structid);

		$db->setQuery($query);
		if (!$db->query())
		{
			$err = 1;
		}

		if (!$err)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Gets the groupnumber
	 *
	 * @access	public
	 * @return	Int GroupID
	 */
	public function getGroupNumber()
	{
		$gsgid = JRequest::getVar('gsgid', 1);
		return $gsgid;
	}

	/**
	 * Gets the Moderator
	 *
	 * @access	public
	 * @return  bool is Moderator, true/False
	 */
	public function getModerator()
	{
		$user =& JFactory::getUser();
		$id = $user->id;
		$gid = $this->getGroupNumber();
		$this->_isModerator = false;
		$db =& JFactory::getDBO();
		if (!empty($id) && !empty($gid))
		{
			/*
			$query = "SELECT rid FROM `#__thm_groups_groups_map` where uid=$id AND gid=$gid";
			*/
			$query = $db->getQuery(true);
			$query->select('rid');
			$query->from($db->qn('#__thm_groups_groups_map'));
			$query->where('uid = ' . $id);
			$query->where('gid = ' . $gid);

			$db->setQuery($query);
			$roles = $db->loadObjectList();
			foreach ($roles as $role)
			{
				if ($role->rid == 2)
				{
					$this->_isModerator = true;
				}
			}
		}
		return $this->_isModerator;
	}

	/**
	 * Apply
	 *
	 * @return void
	 */
	public function apply()
	{
		$this->store();
	}

	/**
	 *  Method to get the link, where the redirect has to go
	 *@since  Method available since Release 2.0
	 *
	 *@return   string  link.
	 */
	public function getLink()
	{
		$itemid			   = $itemid = JRequest::getVar('Itemid', 0);
		$id				   = JRequest::getVar('id', 0);
		$userInfo['lastName'] = JRequest::getVar('lastName', 0);
		$letter			   = strtoupper(substr($userInfo['lastName'], 0, 1));
		$db =& JFactory::getDBO();
		/*
		$query = "SELECT link FROM `#__menu` where id= $itemid";
		*/
		$query = $db->getQuery(true);
		$query->select('link');
		$query->from($db->qn('#__menu'));
		$query->where('id = ' . $itemid);

		$db->setQuery($query);
		$item = $db->loadObject();
		$link = substr($item->link . "&Itemid=" . $itemid, 0, strlen($item->link . "&Itemid=" . $itemid));
		return $link . "&/$id-" . $userInfo['lastName'] . "&letter=$letter";
	}
}
