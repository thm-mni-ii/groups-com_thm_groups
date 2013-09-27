<?php
/**
 * @version     v3.4.3
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
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
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
     * @param   String  $newType  contain the new Type of Structure
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
        $db->setQuery((string) $query);
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
        $id = JRequest::getVar('cid');

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__thm_groups_' . strtolower($relation) . '_extra'));
        $query->where('structid = ' . $id);
        $db->setQuery((string) $query);
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
        $structID = intval($idarr[0]);
        $name = JRequest::getVar('name');
        $relation = JRequest::getVar('relation');

        $extra = JRequest::getVar(strtolower($relation) . '_extra');
        $newPicPath = JRequest::getVar(strtolower($relation) . '_extra_path');
        $structure = $this->getItem();
        $err = false;
        $db = JFactory::getDBO();

        // If the Type not same, aber changeable. Tha Data will be copy
        if ($this->canTypechange($structure->type, $relation) == true)
        {
            $changeQuery = $db->getQuery(true);

            $changeQuery->select('*')
            ->from('#__thm_groups_' . strtolower($structure->type))
            ->where('structid =' . $structID);

            $db->setQuery((string) $changeQuery);

            $toChangevalue = $db->loadObjectList();

            $targetTable = '#__thm_groups_' . strtolower($relation) . "(`userid` , `structid`, `value`, `publish` , `group`)";

            if (isset($toChangevalue))
            {
                $addquery = $db->getQuery(true);
                $deletequery = $db->getQuery(true);

                foreach ($toChangevalue as $changeItem)
                {
                    $addquery
                    ->insert($targetTable)
                    ->values(
                            $changeItem->userid . " , " .
                            $changeItem->structid . " , " . "'$changeItem->value' , '$changeItem->publish' , '$changeItem->group'"
                    );

                    $db->setQuery((string) $addquery);
                    try
                    {
                        $db->query();
                    }
                    catch (Exception $exception)
                    {
                        JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
                        return false;
                    }
                }

                $deletequery->delete('#__thm_groups_' . strtolower($structure->type))
                ->where('structid =' . $structID);

                $db->setQuery((string) $deletequery);
                try
                {
                    $db->query();
                }
                catch (Exception $exception)
                {
                    JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
                    return false;
                }
            }
        }

        $updateQuery = $db->getQuery(true);
        $updateQuery->update('#__thm_groups_structure')
        ->set("`field` = '" . $name . "'")
        ->set("`type` = '" . $relation . "'")
        ->where("`id` = '" . $structID . "'");

        $db->setQuery((string) $updateQuery);

        try
        {
            $db->query();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        if (isset($extra) == true || isset($newPicPath) == true)
        {
            self::movePictures($relation, $structID, $extra, $newPicPath);
        }
    }

    /**
     * Moves pictures from one dorectory to another
     *
     * @param   String  $relation    PICTURE
     * @param   Int     $structID    structure id
     * @param   String  $extra       stadndard picture name(anonym.jpg)
     * @param   String  $newPicPath  new directory path
     *
     * @return  true on success, false on fail
     */
    public function movePictures($relation, $structID, $extra, $newPicPath)
    {
        $oldPicPath = self::getCurrentPicturePath($relation, $structID);

        if (!self::isDirExists($newPicPath))
        {
            self::makeNewDir($newPicPath);
        }

        self::copyPictures($oldPicPath->path, $newPicPath, $structID);
        self::deletePictures($oldPicPath->path, $structID);

        self::saveNewPicturePath($relation, $structID, $extra, $newPicPath);
    }

    /**
     * Returns current picture path
     *
     * @param   String  $relation  PICTURE
     * @param   Int     $structID  structure id
     *
     * @return  an object with current path
     */
    public function getCurrentPicturePath($relation, $structID)
    {
        $db = JFactory::getDbo();

        // Get current path of pictures
        $getFolderPathQuery = $db->getQuery(true);
        $getFolderPathQuery->select('path');
        $getFolderPathQuery->from('#__thm_groups_' . strtolower($relation) . '_extra');
        $getFolderPathQuery->where('structid = ' . $structID);
        $db->setQuery($getFolderPathQuery);

        return $db->loadObject();
    }

    /**
     * Checks, if directory exists
     *
     * @param   String  $path  directory path from DB
     *
     * @return boolean
     */
    public function isDirExists($path)
    {
        $dirPath = JPATH_ROOT . DS . $path;
        if (file_exists($dirPath))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Makes a new directory
     *
     * @param   String  $newPicPath  new directory path
     *
     * @return nothing
     */
    public function makeNewDir($newPicPath)
    {
        mkdir(JPATH_ROOT . DS . $newPicPath, 0777, true);
    }

    /**
     * Copies all pictures from old directory to new directory
     *
     * @param   String  $oldPath   old directory path
     * @param   String  $newPath   new directory path
     * @param   Int     $structID  structure id
     *
     * @return nothing
     */
    public function copyPictures($oldPath, $newPath, $structID)
    {
        // Name of all pictures
        $fileNames = self::getPictureNames($structID);

        $from = JPATH_ROOT . DS . $oldPath;
        $to = JPATH_ROOT . DS . $newPath;

        foreach (scandir($from) as $pic)
        {
            if (in_array($pic, $fileNames) != false)
            {
                copy($from . DS . $pic, $to . DS . $pic);
            }
        }
    }

    /**
     * Deletes pictures from old directory
     *
     * @param   String  $path      old directory path
     * @param   Int     $structID  structure id
     *
     * @return nothing
     */
    public function deletePictures($path, $structID)
    {
        // Name of all pictures
        $fileNames = self::getPictureNames($structID);

        $dir = JPATH_ROOT . DS . $path;

        foreach (scandir($dir) as $pic)
        {
            if (in_array($pic, $fileNames) != false)
            {
                // Delete picture from folder
                unlink($dir . DS . $pic);
            }
        }
    }

    /**
     * Returns picture names
     *
     * @param   Int  $structID  structure id
     *
     * @return Array with picture names
     */
    public function getPictureNames($structID)
    {
        $db = JFactory::getDbo();
        /*
         $getFileNamesQuery = "SELECT value FROM #__thm_groups_picture WHERE structid = $structID"
        */
        $getFileNamesQuery = $db->getQuery(true);
        $getFileNamesQuery->select('value')
        ->from('#__thm_groups_picture')
        ->where("structid = '" . $structID . "'");
        $db->setQuery((string) $getFileNamesQuery);

        return $db->loadResultArray();
    }

    /**
     * Saves a new picture path in DB
     *
     * @param   String  $relation    PICTURE
     * @param   Int     $structID    structure id
     * @param   String  $extra       stadndard picture name(anonym.jpg)
     * @param   String  $newPicPath  new directory path
     *
     * @return true if transaction ok and false if nope
     */
    public function saveNewPicturePath($relation, $structID, $extra, $newPicPath)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->update("`#__thm_groups_" . strtolower($relation) . "_extra`");
        $query->set("`value` = '" . $extra . "'");
        if (isset($newPicPath))
        {
            $query->set("`path` = '" . $newPicPath . "'");
        }
        $query->where('structid = ' . $structID);

        $db->setQuery((string) $query);

        try
        {
            $db->query();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }
    }
}