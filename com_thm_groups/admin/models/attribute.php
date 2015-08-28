<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        attribute model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once JPATH_COMPONENT . '/assets/helpers/static_type_options_helper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelAttribute extends JModelLegacy
{

    /**
     * Creates empty database entries for all users for the new created attribute
     *
     * @param   int  $attributeID  An id of a new created attribute
     *
     * @return bool
     *
     * @throws Exception
     */
    public function createEmptyRowsForAllUsers($attributeID)
    {
        $dbo = JFactory::getDbo();
        $ids = $this->getUserIDs();
        $usersWithAttribute = $this->getUserIDsByAttributeID($attributeID);
        $ids = $this->filterIDs($ids, $usersWithAttribute);

        /*
         * Create database entry for created attribute with empty value for all users
         * It will be used in user_edit view
         * If you find a better solution, you replace it
         */
        foreach ($ids as $id)
        {
            $query = $dbo->getQuery(true);
            $columns = array('usersID', 'attributeID', 'published');

            // $id->id this is stupid...
            $values = array($id, $attributeID, 0);
            $query
                ->insert($dbo->qn('#__thm_groups_users_attribute'))
                ->columns($dbo->qn($columns))
                ->values(implode(',', $values));
            $dbo->setQuery($query);

            try
            {
                $dbo->execute();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Filters user IDs and exclude IDs, which already have
     * an attribute
     *
     * @param   array  $ids     An array with all user IDs
     * @param   array  $badIDs  An array with user IDs, which have an attribute
     *
     * @return array
     */
    public function filterIDs($ids, $badIDs)
    {
        $idsToSave = array();
        $idsNotToSave = array();

        // Prepare ids for search
        foreach ($ids as $id)
        {
            array_push($idsToSave, $id->id);
        }

        // Prepare ids for search
        foreach ($badIDs as $id)
        {
            array_push($idsNotToSave, $id->usersID);
        }

        // Search ids and if founded then delete
        foreach ($idsToSave as $key => $id)
        {
            if (array_search($id, $idsNotToSave) !== false)
            {
                unset($idsToSave[$key]);
            }
        }

        return $idsToSave;
    }

    /**
     * Returns all user IDs which have an attribute with
     * the $attributeID
     *
     * @param   int  $attributeID  An attribute id
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function getUserIDsByAttributeID($attributeID)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('usersID')
            ->from('#__thm_groups_users_attribute')
            ->where("attributeID = $attributeID");
        $dbo->setQuery($query);

        try
        {
            $result = $dbo->loadObjectList();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        return $result;
    }

    /**
     * Returns user IDs from THM Groups component
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function getUserIDs()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('id')
            ->from($dbo->qn('#__thm_groups_users'));

        $dbo->setQuery($query);

        try
        {
            $ids = $dbo->loadObjectList();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        return $ids;
    }

    /**
     * Saves the attribute
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $options = THM_GroupsHelperOptions::getOptions();

        $dbo = JFactory::getDbo();
        $app = JFactory::getApplication();
        $data = $app->input->post->get('jform', array(), 'array');

        $required = (isset($data['required']) ? 'true' : 'false');

        // DynamicType usw. is a name of select field
        $data['dynamic_typeID'] = (int) $app->input->post->get('dynamicType');
        $data['static_typeID'] = (int) $app->input->post->get('sType');
        $data['description'] = $dbo->escape($data['description']);

        // Get options from input made in form
        switch ($data['static_typeID'])
        {
            case "1":
                $options['1'] = '{ "length" : "' . $app->input->getHtml('TEXT_length') . '", "required" : "' . $required . '" }';
                break;
            case "2":
                $options['2'] = '{ "length" : "' . $app->input->getHtml('TEXTFIELD_length') . '", "required" : "' . $required . '" }';
                break;
            case "3":
                $options['3'] = '{ "required" : "' . $required . '" }';
            case "4":
                $attrID = $data['id'];
                $inputPath   = $app->input->getHtml('PICTURE_path');
                $inputName   = $app->input->getHtml('PICTURE_name');

                // Move pictures to new path when different to path in database
                if ((($attrID) && ($attrID != "")) && ($attrID != "0"))
                {
                    $pictureType = $this->getPictureItem($attrID);

                    // Get old path
                    $path = json_decode($pictureType->options)->path;

                    if ($path != $inputPath)
                    {
                        $this->movePictures($inputPath, $path, $attrID, $data['dynamic_typeID']);
                    }
                }

                $options['4'] = '{ "filename" : "' . $inputName . '", "path" : "' . $inputPath . '", "required" : "' . $required . '" }';
                break;
            case "5":
                $options['5'] = '{ "options" : "' . $app->input->getHtml('MULTISELECT_options') . '", "required" : "' . $required . '" }';
                break;
            case "6":
                $options['6'] = '{ "columns" : "' . $app->input->getHtml('TABLE_columns') . '", "required" : "' . $required . '" }';
                break;
            case "7":
                $options['7'] = '{ "required" : "' . $required . '" }';
        }

        $data['options'] = $options[$data['static_typeID']];
        $dbo->transactionStart();

        $attribute = $this->getTable();

        $success = $attribute->save($data);

        // $success = false;

        if (!$success)
        {
            $dbo->transactionRollback();

            return false;
        }
        else
        {
            $dbo->transactionCommit();

            return $attribute->id;
        }
    }

    /**
     * Delete item
     *
     * @return mixed
     */
    public function delete()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');

        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);

        $conditions = array(
            $dbo->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
        );

        $query->delete($dbo->quoteName('#__thm_groups_attribute'));
        $query->where($conditions);

        $dbo->setQuery($query);

        return $dbo->execute();
    }

    /**
     * Checks, if directory exists
     *
     * @param   String  $inputPath  directory path from DB
     *
     * @return boolean
     */
    private function dirExists($inputPath)
    {
        if (file_exists($inputPath))
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
     * @param   String  $inputPath  new directory path
     *
     * @return nothing
     */
    private function makeNewDir($inputPath)
    {
        JFolder::create($inputPath, 0755);
    }

    /**
     * Returns one single pictureItem from database
     *
     * @param   Integer  $atrId  ID of selected attribute
     *
     * @return null|$result
     */
    private function getPictureItem($atrId)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('*')
            ->from($dbo->qn('#__thm_groups_attribute'))
            ->where('id = ' . (int) $atrId);

        $dbo->setQuery($query);
        $result = $dbo->loadObject();

        if ($result != null)
        {
            return $result;
        }
        else
        {
            return null;
        }
    }

    /**
     * Copies all pictures from old directory to new directory
     *
     * @param   String  $oldPath  old directory path
     * @param   String  $newPath  new directory path
     * @param   Int     $attrID   attribute id
     *
     * @return   boolean
     */
    private function copyPictures($oldPath, $newPath, $attrID)
    {
        $pictures = self::getPictures($attrID);

        foreach (scandir($oldPath) as $folderPic)
        {
            foreach ($pictures as $pic)
            {
                $picName = $pic->value;

                if ($folderPic == $picName)
                {
                    // Copy the cropped picture
                    copy($oldPath . $folderPic, $newPath . $folderPic);
                    unlink($oldPath . $folderPic);

                    // Copy the picture in full resolution
                    $oriFileName = $this->after('cropped_', $folderPic);

                    if (!self::dirExists($newPath . 'fullRes/'))
                    {
                        self::makeNewDir($newPath . 'fullRes/');
                    }

                    copy($oldPath . 'fullRes/' . $oriFileName, $newPath . 'fullRes/' . $oriFileName);
                    unlink($oldPath . 'fullRes/' . $oriFileName);

                    // Copy the thumbnails for the picture
                    foreach ( scandir($oldPath . 'thumbs/') as $thumbnail)
                    {
                        if ( $thumbnail === '.' || $thumbnail === '..')
                        {
                            continue;
                        }
                        else
                        {
                            /**
                             * Get the filename till the '_width-height.extension' part
                             * and check if its part of the saved filename in database.
                             *
                             * When a pos was found it will be dropped from the folder.
                             */
                            $extPos = strrpos($thumbnail, '_');
                            $length = strlen($thumbnail);
                            $thumbFileName = substr($thumbnail, 0, -($length - $extPos));

                            $pos = strpos($folderPic, $thumbFileName);

                            if ($pos === 0)
                            {
                                if (!self::dirExists($newPath . 'thumbs/'))
                                {
                                    self::makeNewDir($newPath . 'thumbs/');
                                }

                                copy($oldPath . 'thumbs/' . $thumbnail, $newPath . 'thumbs/' . $thumbnail);
                                unlink($oldPath . 'thumbs/' . $thumbnail);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Returns substring after $part
     *
     * @param   String  $part      Substring
     * @param   String  $inString  Search string
     *
     * @return  String
     */
    private function after ($part, $inString)
    {
        if (!is_bool(strpos($inString, $part)))
        {
            return substr($inString, strpos($inString, $part) + strlen($part));
        }
    }

    /**
     * Gets the pictures by searching for the attribute id in
     * the #__thm_groups_users_attribute-table.
     *
     * @param   Integer  $attrID  The selected attribute id
     *
     * @return   Array
     */
    private function  getPictures($attrID)
    {
        // Get all Pictures from attribute
        $dbo = JFactory::getDbo();

        $usersAttributeQuery = $dbo->getQuery(true);
        $usersAttributeQuery->select($dbo->qn(array('ID', 'value', 'attributeID')))
            ->from($dbo->qn('#__thm_groups_users_attribute'))
            ->where($dbo->qn('attributeID') . ' = ' . $attrID . '');
        $dbo->setQuery($usersAttributeQuery);
        $result = $dbo->loadObjectList();

        return $result;
    }

    /**
     * Moves pictures from old to new directory, deletes the old files.
     * The path for #__thm_groups_attribute will be set in calling function.
     *
     * @param   Integer  $inputPath    Typed in path from user-interface
     * @param   String   $oldPath      Old path from database
     * @param   Integer  $attrID       ID from selected attribute
     * @param   Integer  $dynamicType  DynamicTypeID from form
     *
     * @return  boolean
     */
    private function movePictures($inputPath, $oldPath, $attrID, $dynamicType)
    {
        $serverPath = self::getSeverPath();
        $inputPath = $serverPath . $inputPath;
        $oldPath = $serverPath . $oldPath;

        if (!self::dirExists($inputPath))
        {
            self::makeNewDir($inputPath);
        }

        if (!self::copyPictures($oldPath, $inputPath, $attrID, $dynamicType))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Gets the server path
     *
     * @return mixed
     */
    private function getSeverPath()
    {
        return str_replace('\\', '/', JPATH_ROOT);
    }
}
