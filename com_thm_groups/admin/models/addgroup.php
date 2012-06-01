<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelAddGroup
 *@description THMGroupsModelAddGroup file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Peter May,  peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.modelform');
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'SQLAbstractionLayer.php';

/**
 * THMGroupsModelAddGroup class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelAddGroup extends JModelForm
{
	/**
	 * getForm
	 *
	 * @param   Array  $data      Data
	 * @param   Bool   $loadData  true/false
	 * 
	 * @return	form
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_thm_groups.addgroup', 'addgroup', array('load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		else
		{
		}
		return $form;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store()
	{
		$gr_name = JRequest::getVar('gr_name');
		$gr_info = JRequest::getVar('groupinfo', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$gr_mode = JRequest::getVar('gr_mode');
		$gr_parent = JRequest::getVar('gr_parent');
		$gr_mode = $field = implode(';', $gr_mode);
		$id = null;

		$db =& JFactory::getDBO();
		$err = 0;

		/*
		*	Einfügen der Gruppe in die joomla usergroups Tabelle
		**/
		// Gruppe einfügen
		$query = "INSERT INTO #__usergroups (parent_id, title, lft, rgt) " .
				"VALUES (" . $gr_parent . ", '" . $gr_name . "', 0, 0)";
		$db->setQuery($query);
		$db->query();

		// Hol grad neu hinzugefügte hübsche joomla Gruppe
		$query = "SELECT id " .
				"FROM `#__usergroups` " .
				"WHERE parent_id = " . $gr_parent . " AND lft = 0 AND rgt = 0";
		$db->setQuery($query);
		$gr_id = $db->loadObject();
		$gr_id = $gr_id->id;

		// Elterngruppe aus Datenbank lesen
		$query = "SELECT * " .
				 "FROM `#__usergroups` " .
				 "WHERE id = " . $gr_parent;
		$db->setQuery($query);
		$parent = $db->loadObject();

		// Gruppe einsortieren
		$query = "SELECT * " .
				 "FROM `#__usergroups` " .
				 "WHERE parent_id = " . $gr_parent . " " .
				 "ORDER BY title";

		$db->setQuery($query);
		$jsortgrps = $db->loadObjectlist();

		// Finde neuen linken Index
		$leftneighbor = null;
		foreach ($jsortgrps as $grp)
		{
			if ($grp->id == $gr_id)
			{
				break;
			}
			else
			{
				$leftneighbor = $grp;
			}
		}
		if ($leftneighbor == null)
		{
			$lft = $parent->lft + 1;
		}
		else
		{
			$lft = $leftneighbor->rgt + 1;
		}

		// Rechten Index aktualisieren
		$query = "UPDATE `#__usergroups` " .
				 "SET rgt = rgt + 2 " .
				 "WHERE rgt >= " . $lft;
		$db->setQuery($query);
		$db->query();

		// Linken Index aktualisieren
		$query = "UPDATE `#__usergroups` " .
				 "SET lft = lft + 2 " .
				 "WHERE lft >= " . $lft;
		$db->setQuery($query);
		$db->query();

		// Linken und rechten Index der neuen Gruppe aktualisieren
		$query = "UPDATE `#__usergroups` " .
				 "SET lft = " . $lft . ", rgt = " . $lft . " + 1 " .
				 "WHERE id = " . $gr_id;
		$db->setQuery($query);
		$db->query();

		$query = "INSERT INTO #__thm_groups_groups ( id , name, info, picture, mode)"
        . " VALUES ("
        . " '" . $gr_id . "'"
        . ", '" . $gr_name . "'"
        . ", '" . $gr_info . "'"
        . ", 'anonym.jpg'"
        . ", '" . $gr_mode . "')";

        $db->setQuery($query);

        if ($db->query())
        {
            $id = $db->insertid();
       		JRequest::setVar('cid[]', $id);
        }
        else
        {
        	$err = 1;
        }

        if (isset($id) && $_FILES['gr_picture']['name'] != "")
        {
        	if (!$this->updatePic($id, 'gr_picture'))
        	{
				$err = 1;
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
	 * Method to update a picture
	 *
	 * @param   Int     $gid       GroupID
	 * @param   Object  $picField  PicField
	 * 
	 * @return	boolean	True on success
	 */
	public function updatePic($gid, $picField)
	{
		require_once JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "helper" . DS . "thm_groups_pictransform.php";

		try
		{
			$pt = new PicTransform($_FILES[$picField]);
			$pt->safeSpecial(JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "img" . DS . "portraits" . DS, "g" . $gid, 200, 200, "JPG");
			if (JModuleHelper::isEnabled('mod_thm_groups')->id != 0)
			{
				$pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups" . DS . "images" . DS, "g" . $gid, 200, 200, "JPG");
			}
			if (JModuleHelper::isEnabled('mod_thm_groups_smallview')->id != 0)
			{
				$pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups_smallview" . DS . "images" . DS, "g" . $gid, 200, 200, "JPG");
			}
		}
		catch (Exception $e)
		{
			return false;
		}

		$db =& JFactory::getDBO();
		$query = "UPDATE #__thm_groups_groups SET picture='g" . $gid . ".jpg' WHERE id = $gid ";
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
	 * Get extra from db
	 *
	 * @param   Int     $structid  StructID
	 * @param   String  $type      Type
	 *
	 * @return	boolean	True on success
	 */
	public function getExtra($structid, $type)
	{
		$db =& JFactory::getDBO();
		$query = "SELECT value FROM #__thm_groups_" . $type . "_extra WHERE structid=" . $structid;
		$db->setQuery($query);
		$res = $db->loadObject();
		   	return $res->value;
	}

	/**
	 * Add table row
	 *
	 * @return	boolean	True on success
	 */
	public function addTableRow()
	{
		$db =& JFactory::getDBO();
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$arrRow = array();
		$arrValue = array();
		$err = 0;

		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		$db->setQuery($query);
		$res = $db->loadObject();
		$oValue = json_decode($res->value);
		foreach ($oValue as $row)
		{
			$arrValue[] = $row;
		}

		$query = "SELECT value FROM #__thm_groups_table_extra WHERE structid=" . $structid;
		$db->setQuery($query);
		$resHead = $db->loadObject();
		$head = explode(';', $resHead->value);

		foreach ($head as $headItem)
		{
			$arrRow[$headItem] = JRequest::getVar("TABLE$structid$headItem");
		}
		$arrValue[] = $arrRow;

		$jsonValue = json_encode($arrValue);
		if (isset($res))
		{
			$query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
		}
		else
		{
			$query = "INSERT INTO #__thm_groups_table ( `userid`, `structid`, `value`)"
		        . " VALUES ($uid"
		        . ", " . $structid
		        . ", '" . $jsonValue . "')";
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
	 * Delete table row
	 *
	 * @return	boolean	True on success
	 */
	public function delTableRow()
	{
		$db =& JFactory::getDBO();
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$key = JRequest::getVar('tablekey');
		$arrRow = array();
		$arrValue = array();
		$err = 0;

		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		$db->setQuery($query);
		$res = $db->loadObject();
		$oValue = json_decode($res->value);
		foreach ($oValue as $row)
		{
			$arrValue[] = $row;
		}
		array_splice($arrValue, $key, 1);

		$jsonValue = json_encode($arrValue);
		$query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
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
	 * Edit table row
	 *
	 * @return	boolean	True on success
	 */
	public function editTableRow()
	{
		$db =& JFactory::getDBO();
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$key = JRequest::getVar('tablekey');
		$arrRow = array();
		$arrValue = array();
		$err = 0;

		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		$db->setQuery($query);
		$res = $db->loadObject();
		$oValue = json_decode($res->value);
		foreach ($oValue as $row)
		{
			$arrValue[] = $row;
		}

		foreach ($arrValue[$key] as $field => $row)
		{
			$arrRow[$field] = JRequest::getVar('TABLE' . $structid . $field);
		}
		$arrValue[$key] = $arrRow;
		$jsonValue = json_encode($arrValue);
		$query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
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
	 * Apply
	 *
	 * @return	void
	 */
	public function apply()
	{
		$this->store();
	}

	/**
	 * Get all groups
	 *
	 * @return	boolean	True on success
	 */
	public function getAllGroups()
	{
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__usergroups ORDER BY lft";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
