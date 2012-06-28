<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelEditGroup
 *@description THMGroupsModelEditGroup file from com_thm_groups
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

/**
 * THMGroupsModelEditGroup class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelEditGroup extends JModelForm
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->getForm();
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
		$form = $this->loadForm('com_thm_groups.editgroup', 'editgroup', array('load_data' => $loadData));
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
	 * Method to build query
	 *
	 * @return	query
	 */
	public function _buildQuery()
	{
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		JArrayHelper::toInteger($cid, array(0));
		/*
		 $query = "SELECT * FROM #__thm_groups_groups WHERE id=" . $cid[0];
		 */
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_groups'));
		$query->where("`id` = '" . $cid[0] . "'");

		return $query->__toString();
	}

	/**
	 * Method to get data
	 *
	 * @return	query
	 */
	public function getData()
	{
		$query = $this->_buildQuery();
		$this->_data = $this->_getList($query);
		return $this->_data;
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
		$gr_mode = implode(';', $gr_mode);
		$gid = JRequest::setVar('gid');

		$db =& JFactory::getDBO();
		$err = 0;

		/*
		$query = "UPDATE #__thm_groups_groups SET"
			. " name='" . $gr_name . "'"
			. ", info='" . $gr_info . "'"
			. ", mode='" . $gr_mode . "'"
			. " WHERE id=" . $gid;
			*/
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_groups'));
		$query->set("`name` = '" . $gr_name . "'");
		$query->set("`info` = '" . $gr_info . "'");
		$query->set("`mode` = '" . $gr_mode . "'");
		$query->where("`id` = '" . $gid . "'");

		$db->setQuery($query);
		if (!$db->query())
		{
			$err = 1;
		}
		else
		{
		}

		if ($_FILES['gr_picture']['name'] != "")
		{
			if (!$this->updatePic($gid, 'gr_picture'))
			{
				$err = 1;
			}
			else
			{
			}
		}
		else
		{
		}

		/*
		$query = "SELECT injoomla "
			. "FROM `#__thm_groups_groups` "
			. "WHERE id = " . $gid;
		*/
		$query = $db->getQuery(true);
		$query->select('injoomla');
		$query->from($db->qn('#__thm_groups_groups'));
		$query->where("`id` = '" . $gid . "'");

		$db->setQuery($query);
		$injoomla = $db->loadObject();
		$injoomla = $injoomla->injoomla;

		// Joomla Gruppe nur anpassen wenn sie da auch exisitiert
		if ($injoomla == 1)
		{
			// Gruppe anpassen

			/*
			$query = "UPDATE #__usergroups "
				. "SET parent_id = " . $gr_parent . ", title = '" . $gr_name . "' "
				. "WHERE id = " . $gid;
			*/
			$query = $db->getQuery(true);
			$query->update($db->qn('#__usergroups'));
			$query->set("`parent_id` = '" . $gr_parent . "'");
			$query->set("`title` = '" . $gr_name . "'");
			$query->where("`id` = '" . $gid . "'");

			$db->setQuery($query);
			$db->query();

			// Gruppe aus Datenbank lesen

			/*
			$query = "SELECT * "
				. "FROM `#__usergroups` "
				. "WHERE id = " . $gid;
			*/
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->qn('#__usergroups'));
			$query->where("`id` = '" . $gid . "'");

			$db->setQuery($query);
			$jgroup = $db->loadObject();

			// Elterngruppe aus Datenbank lesen

			/*
			$query = "SELECT * "
				. "FROM `#__usergroups` "
				. "WHERE id = " . $gr_parent;
			*/
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->qn('#__usergroups'));
			$query->where("`id` = '" . $gr_parent . "'");

			$db->setQuery($query);
			$parent = $db->loadObject();

			// Gruppe einsortieren

			/*
			$query = "SELECT * "
				. "FROM `#__usergroups` "
				. "WHERE parent_id = " . $gr_parent . " "
				. "ORDER BY title";
			*/
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->qn('#__usergroups'));
			$query->where("`parent_id` = '" . $gr_parent . "'");
			$query->order('`title`');

			$db->setQuery($query);
			$jsortgrps = $db->loadObjectlist();

			// Finde neuen linken Index
			$leftneighbor = null;
			foreach ($jsortgrps as $grp)
			{
				if ($grp->id == $gid)
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
				$new_lft = $parent->lft + 1;
			}
			else
			{
				$new_lft = $leftneighbor->rgt + 1;
			}
			$jgrouprange = $jgroup->rgt - $jgroup->lft + 1;

			// Platz schaffen
			// Rechten Index aktualisieren

			/*
			$query = "UPDATE `#__usergroups` "
				. "SET rgt = rgt + " . $jgrouprange . " "
				. "WHERE rgt >= " . $new_lft;
			*/
			$query = $db->getQuery(true);
			$query->update("#__usergroups");
			$query->set("`rgt` = 'rgt + " . $jgrouprange . "'");
			$query->where("`rgt` >= '" . $new_lft . "'");

			$db->setQuery($query);
			$db->query();

			// Linken Index aktualisieren

			/*
			$query = "UPDATE `#__usergroups` "
				. "SET lft = lft + " . $jgrouprange . " "
				. "WHERE lft >= " . $new_lft;
			*/
			$query = $db->getQuery(true);
			$query->update("#__usergroups");
			$query->set("`lft` = 'lft + " . $jgrouprange . "'");
			$query->where("`lft` >= '" . $new_lft . "'");

			$db->setQuery($query);
			$db->query();

			// Gruppe neu aus Datenbank lesen

			/*
			$query = "SELECT * "
				. "FROM `#__usergroups` "
				. "WHERE id = " . $gid;
				*/
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->qn('#__usergroups'));
			$query->where("`id` = '" . $gid . "'");

			$db->setQuery($query);
			$jgroup = $db->loadObject();

			// Daten zwischenspeichern
			$old_lft = $jgroup->lft;
			$old_rgt = $jgroup->rgt;
			$jgroupspan = $new_lft - $old_lft;

			// Gruppe verschieben

			/*
			$query = "UPDATE `#__usergroups` "
				. "SET rgt = rgt + " . $jgroupspan . " "
				. "WHERE rgt >= " . $old_lft . " AND rgt <= " . $old_rgt;
			*/
			$query = $db->getQuery(true);
			$query->update("#__usergroups");
			$query->set("`rgt` = 'rgt + " . $jgroupspan . "'");
			$query->where("`rgt` >= '" . $old_lft . "'");
			$query->where("`rgt` <= '" . $old_rgt . "'");

			$db->setQuery($query);
			$db->query();
			/*
			$query = "UPDATE `#__usergroups` "
				. "SET lft = lft + " . $jgroupspan . " "
				. "WHERE lft >= " . $old_lft . " AND lft <= " . $old_rgt;
			*/
			$query = $db->getQuery(true);
			$query->update("#__usergroups");
			$query->set("`lft` = 'lft + " . $jgroupspan . "'");
			$query->where("`lft` >= '" . $old_lft . "'");
			$query->where("`lft` <= '" . $old_rgt . "'");

			$db->setQuery($query);
			$db->query();

			/*
			$query = "UPDATE `#__usergroups` "
				. "SET rgt = rgt - " . $jgrouprange . " "
				. "WHERE rgt >= " . $old_lft;
				*/
			$query = $db->getQuery(true);
			$query->update("#__usergroups");
			$query->set("`rgt` = 'rgt - " . $jgrouprange . "'");
			$query->where("`rgt` >= '" . $old_lft . "'");

			$db->setQuery($query);
			$db->query();

			/*
			$query = "UPDATE `#__usergroups` "
				. "SET lft = lft - " . $jgrouprange . " "
				. "WHERE lft >= " . $old_lft;
			*/
			$query = $db->getQuery(true);
			$query->update("#__usergroups");
			$query->set("`lft` = 'lft - " . $jgrouprange . "'");
			$query->where("`lft` >= '" . $old_lft . "'");

			$db->setQuery($query);
			$db->query();
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
	 * @param   String  $picField  Pictureadress
	 * 
	 * @return	boolean	True on success
	 */
	public function updatePic($gid, $picField)
	{
		require_once JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "helper" . DS . "thm_groups_pictransform.php";
		try
		{
			$pt = new PicTransform($_FILES[$picField]);
			$compath = "com_thm_groups";
			$pt->safeSpecial(JPATH_ROOT . DS . "components" . DS . $compath . DS . "img" . DS . "portraits" . DS, "g" . $gid, 200, 200, "JPG");
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
		/*
		$query = "UPDATE #__thm_groups_groups SET picture='g" . $gid . ".jpg' WHERE id = $gid ";
		*/
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_groups'));
		$query->set("`picture` = 'g" . $gid . ".jpg'");
		$query->where("`id` = '" . $gid . "'");
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
	 * Method to delete a picture
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function delPic()
	{
		$db =& JFactory::getDBO();
		$gid = JRequest::getVar('gid');

		/*
		$query = "UPDATE #__thm_groups_groups SET picture='anonym.jpg' WHERE id = $gid ";
		*/
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_groups'));
		$query->set("`picture` = 'anonym.jpg'");
		$query->where("`id` = '" . $gid . "'");
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
	 * Method to get all groups
	 *
	 * @access	public
	 * @return	Object
	 */
	public function getAllGroups()
	{
		$db =& JFactory::getDBO();
		/*
		$query = "SELECT * FROM #__usergroups ORDER BY lft";
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__usergroups'));
		$query->order("`lft`");

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to get parent id
	 *
	 * @access	public
	 * @return	parent id
	 */
	public function getParentId()
	{
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		JArrayHelper::toInteger($cid, array(0));
		$db =& JFactory::getDBO();
		/*
		$query = "SELECT parent_id FROM #__usergroups WHERE id=" . $cid[0];
		*/
		$query = $db->getQuery(true);
		$query->select('parent_id');
		$query->from($db->qn('#__usergroups'));
		$query->where("`id` = '" . $cid[0] . "'");

		$db->setQuery($query);
		return $db->loadObject()->parent_id;
	}
}
