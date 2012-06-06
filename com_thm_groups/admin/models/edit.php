<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModeledit
 *@description THMGroupsModeledit file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
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
 * THMGroupsModeledit class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModeledit extends JModelForm
{

	/**
	 * Constructor
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
		$this->getForm();

	}

	/**
	 * Gets data
	 *
	 * @return	result
	 */
	public function getData()
	{
		$cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($cid, array(0));
		$types = $this->getTypes();
		$db = & JFactory::getDBO();
		$puffer = array();
		$result = array();

		foreach ($types as $type)
		{
			$query = "SELECT structid, value, publish FROM #__thm_groups_" . strtolower($type->Type) . " as a where a.userid = " . $cid[0];

			$db->setQuery($query);
			array_push($puffer, $db->loadObjectList());
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
	 * Gets structure
	 *
	 * @return	structure
	 */
	public function getStructure()
	{
		$db = & JFactory::getDBO();
		$query = "SELECT * FROM #__thm_groups_structure as a ORDER BY a.order";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Gets structure
	 *
	 * @return	structure
	 */
	public function getTypes()
	{
		$db = & JFactory::getDBO();
		$query = "SELECT Type FROM #__thm_groups_relationtable " .
				 "WHERE Type in (SELECT type FROM #__thm_groups_structure)";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to store a record
	 *
	 * @return	boolean	True on success
	 */
	public function store()
	{
		$db = & JFactory::getDBO();
		$structure = $this->getStructure();
		$userid = JRequest::getVar('userid');
		$err = 0;
		foreach ($structure as $structureItem)
		{
			$puffer = null;
			 $field = JRequest::getVar($structureItem->field, '', 'post', 'string', JREQUEST_ALLOWHTML);

			$publish = 0;
			if ($strucctIdemtureItem->type == 'MULTISELECT')
			{
				$field = implode(';', $field);
			}
			else
			{
			}

			$publishPuffer = JRequest::getVar('publish' . str_replace(" ", "", $structureItem->field));

			if (isset($publishPuffer))
			{
				$publish = 1;
			}
			else
			{
			}

			$query = "SELECT structid FROM #__thm_groups_" . strtolower($structureItem->type) .
					 " WHERE userid=" . $userid . " AND structid=" . $structureItem->id;
			$db->setQuery($query);
			$puffer = $db->loadObject();

			if (isset($structureItem->field))
			{
				if (isset($puffer))
				{
					$query = "UPDATE #__thm_groups_" . strtolower($structureItem->type) . " SET";
							if ($structureItem->type != 'PICTURE' && $structureItem->type != 'TABLE')
							{
		        				$query .= " value='" . $field . "',";
							}

	        				$query .= " publish='" . $publish . "'"
	       					. " WHERE userid=" . $userid . " AND structid=" . $structureItem->id;
				}
				else
				{
					$query = "INSERT INTO #__thm_groups_" . strtolower($structureItem->type) . " ( `userid`, `structid`, `value`, `publish`)"
					        . " VALUES ($userid"
					        . ", " . $structureItem->id
					        . ", '" . $field . "'"
					        . ", " . $publish . ")";
				}
				echo $query . "<br />";
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
		$db =& JFactory::getDBO();
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$query = "UPDATE #__thm_groups_picture SET value='anonym.jpg' WHERE userid = $uid AND structid=$structid";
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
	 * @param   String  $picField  Picture adresss
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function updatePic($uid, $structid, $picField)
	{

		require_once JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "helper" . DS . "thm_groups_pictransform.php";
		try
		{
			$pt = new PicTransform($_FILES[$picField]);
			$pt->safeSpecial(JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "img" . DS . "portraits" . DS, $uid . "_" . $structid, 200, 200, "JPG");
			if (JModuleHelper::isEnabled('mod_thm_groups')->id != 0)
			{
				$pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups" . DS . "images" . DS, $uid . "_" . $structid, 200, 200, "JPG");
			}
			if (JModuleHelper::isEnabled('mod_thm_groups_smallview')->id != 0)
			{
				$pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups_smallview" . DS . "images" . DS, $uid . "_" . $structid, 200, 200, "JPG");
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		$db =& JFactory::getDBO();
		$query = "UPDATE #__thm_groups_picture SET value='" . $uid . "_" . $structid . ".jpg' WHERE userid = $uid AND structid=$structid";
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
	 * @access	public
	 * @return	String value
	 */
	public function getExtra($structid, $type)
	{
		$db =& JFactory::getDBO();
		$query = "SELECT value FROM #__thm_groups_" . strtolower($type) . "_extra WHERE structid=" . $structid;
		$db->setQuery($query);
		$res = $db->loadObject();
		if (isset($res->value))
		{
		   	return $res->value;
		}
		else
		{
			return "";
		}
	}

	/**
	 * Add table row
	 *
	 * @access	public
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
			$headItem = str_replace(" ", "_", $headItem);
			$value = JRequest::getVar("TABLE$structid$headItem", '', 'POST', 'STRING', JREQUEST_ALLOWRAW);
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
	 * @access	public
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
	 * @access	public
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
		$query = "SELECT value FROM #__thm_groups_table_extra WHERE structid=" . $structid;
		$db->setQuery($query);
		$resHead = $db->loadObject();
		$head = explode(';', $resHead->value);

		foreach ($head as $headItem)
		{
			$headItem = str_replace(" ", "_", $headItem);
			$value = JRequest::getVar("TABLE . $structid . $headItem", '', 'POST', 'STRING', JREQUEST_ALLOWRAW);
			$arrRow[$headItem] = $value;
		}

		$arrValue[$key] = $arrRow;
		$jsonValue = json_encode($arrValue);
		$jsonValue = str_replace("\u00c4", "&Auml;", $jsonValue);
		$jsonValue = str_replace("\u00e4", "&auml;", $jsonValue);
		$jsonValue = str_replace("\u00d6", "&Ouml;", $jsonValue);
		$jsonValue = str_replace("\u00f6", "&ouml;", $jsonValue);
		$jsonValue = str_replace("\u00dc", "&Uuml;", $jsonValue);
		$jsonValue = str_replace("\u00fc", "&uuml;", $jsonValue);
		$jsonValue = str_replace("\u00df", "&szlig;", $jsonValue);
		$jsonValue = str_replace("\u20ac", "&euro;", $jsonValue);
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
	 * Method to apply
	 *
	 * @return	void
	 */
	public function apply()
	{
		$this->store();
	}

	/**
	 * Method to get form
	 * 
	 * @param   Array  $data      Data
	 * @param   Bool   $loadData  true
	 *
	 * @return	boolean	True on success
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
}
