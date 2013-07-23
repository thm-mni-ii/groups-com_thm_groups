<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelEditStructure
 * @description THMGroupsModelEditStructure file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

/**
 * THMGroupsModelEditStructure class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelEditStructure extends JModel
{

	/**
	 * Method to buil query
	 *
	 * @return query
	 */
	public function _buildQuery()
	{
		/*
			$query = "SELECT * "
			. "FROM #__thm_groups_relationtable";
		*/
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_relationtable'));
		return $query->__toString();
	}
	
	/**
	 * Method to get, ob the Type can to change
	 * 
	 * @param   String  $altType  contain the old type of Structure
	 * @param	String  $newType  contain the new Type of Structure
	 *
	 * @return	boolean   
	 */
	public function canTypechange($altType, $newType)
	{
		if (strcasecmp($altType, "text") == 0 && strcasecmp($newType, "textfield") == 0)
		{
			return true;
		}
			
		return false;
	}

	/**
	 * Method to get data
	 *
	 * @return	data
	 */
	public function getData()
	{
		$query = $this->_buildQuery();
		$this->_data = $this->_getList($query);
		return $this->_data;
	}

	/**
	 * Method to get item
	 *
	 * @return	object
	 */
	public function getItem()
	{
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid');
		/*
			$query = "SELECT * FROM #__thm_groups_structure WHERE id=$id[0]";
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_structure'));
		$query->where('id = ' . $id[0]);
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to get extra
	 *
	 * @param   Strig  $relation  Relation
	 * 
	 * @return	object
	 */
	public function getExtra($relation)
	{
		$db = JFactory::getDBO();
		$id = JRequest::getVar('sid');
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_' . strtolower($relation) . '_extra'));
		$query->where('structid = ' . $id);
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store()
	{
		
		$idarr = JRequest::getVar('cid');
		$id = intval($idarr[0]);
		$name = JRequest::getVar('name');
		$relation = JRequest::getVar('relation');
		
		
		$extra = JRequest::getVar(strtolower($relation) . '_extra');
		$picpath = JRequest::getVar(strtolower($relation) . '_extra_path');
		$structure = $this->getItem();
		$err = false;
		$db = JFactory::getDBO();
		
		// If the Type not same, aber changeable. Tha Data will be copy
		
		if ($this->canTypechange($structure->type, $relation) == true)
		{
			
			$changeQuery = $db->getQuery(true);
			
			$changeQuery->select('*')->from('#__thm_groups_' . strtolower($structure->type))->where('structid =' . $id);
			$db->setQuery($changeQuery);
			$toChangevalue = $db->loadObjectList();
			$zielTable = '#__thm_groups_' . strtolower($relation) . "(`userid` , `structid`, `value`, `publish` , `group`)";
			if (isset($toChangevalue))
			{
				$addquery = $db->getQuery(true);
				$deletequery = $db->getQuery(true);
				foreach ($toChangevalue as $changeItem)
				{
					$addquery
					->insert($zielTable)
					->values(
							$changeItem->userid . " , " .
							$changeItem->structid . " , " . "'$changeItem->value' , '$changeItem->publish' , '$changeItem->group'"
					);
				  $sd = $db->setQuery($addquery);
				if (!$db->query())
				{
				
					return false;
				}
				}
				
				$deletequery->delete('#__thm_groups_' . strtolower($structure->type))->where('structid =' . $id);
				$db->setQuery($deletequery);
			if (!$db->query())
			{
					return false;
			}
				
			}
			
		}

		
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_structure'));
		$query->set("`field` = '" . $name . "'");
		$query->set("`type` = '" . $relation . "'");
		$query->where("`id` = '" . $id . "'");
		$db->setQuery($query);
		if (!$db->query())
		{
			$err = true;
			
		}
		else
		{
		}
		
		if (isset($extra) == true || isset($picpath) == true)
		{
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->qn('#__thm_groups_' . strtolower($relation) . '_extra'));
			$query->where('structid = ' . $id);
			$db->setQuery($query);
			$db->query();
			$element = $db->loadObjectList();
			$altPicpath = $element[0];
			
			
			if (!isset($altPicpath))
			{
				$query = $db->getQuery(true);
				if (isset($picpath))
				{
					$query->insert("`#__thm_groups_" . strtolower($relation) . "_extra` (`structid`, `value`, `path`)");
					$query->values("'" . $id . "', '" . $extra . "', '" . $picpath . "'");
				}
				else
				{
					$query->insert("`#__thm_groups_" . strtolower($relation) . "_extra` (`structid`, `value`)");
					$query->values("'" . $id . "', '" . $extra . "'");
				}

				$db->setQuery($query);
				if (!$db->query())
				{
					$err = true;
				
				}
			}
			else
			{
				$dir = is_dir(JPATH_ROOT . DS . $altPicpath->path);
				
				
				if ($dir == false)
				{
					$err = !JFile::copy(JPATH_ROOT . DS . 'components/com_thm_groups/index.html', JPATH_ROOT . DS . $picpath);
				}
				else 
				{
					
					$err = !JFile::move(JPATH_ROOT . DS . $altPicpath->path, JPATH_ROOT . DS . $picpath);
					
					if (!$err)
					{
						unlink(JPATH_ROOT . DS . $altPicpath->path);
					}
				}				
				$query = $db->getQuery(true);
				$query->update("`#__thm_groups_" . strtolower($relation) . "_extra`");
				$query->set("`value` = '" . $extra . "'");
				if (isset($picpath)) 
				{
					$query->set("`path` = '" . $picpath . "'");
				}
				$query->where('structid = ' . $id);
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = true;
					
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
}
