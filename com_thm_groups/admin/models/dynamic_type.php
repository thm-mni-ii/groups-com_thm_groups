<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.model');
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
class THM_GroupsModelDynamic_Type extends JModelLegacy
{
    /**
     * Save element of dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $options = THM_GroupsHelperOptions::getOptions();

        $dbo = JFactory::getDbo();
        $app = JFactory::getApplication();
        $data = $app->input->post->get('jform', array(), 'array');

        // Selected item
        $data['static_typeID'] = $app->input->post->get('staticType');

        // Cast to int, because the type in DB is int
        $data['static_typeID'] = (int) $data['static_typeID'];
        $data['description'] = $dbo->escape($data['description']);

        // Get Options from input
        switch ($data['static_typeID'])
        {
            case "1":
                /**
                 * $app->input->getHtml() => could use JRequest(deprecated) or ...->input->get->post(..)
                 * but chars like ": ; ..." would be lost
                 */
                $options['1'] = '{ "length" : "' . $app->input->getHtml('TEXT_length') . '" }';
                $data['regex'] = $app->input->getHtml('jform_regex_select');
                break;
            case "2":
                $options['2'] = '{ "length" : "' . $app->input->getHtml('TEXTFIELD_length') . '" }';
                break;
            case "3":
                $data['regex'] = $app->input->getHtml('jform_regex_select');
                break;
            case "4":
                $dynamicType = $app->input->get('dynID');
                $inputPath   = $app->input->getHtml('PICTURE_path');
                $inputName   = $app->input->getHtml('PICTURE_name');

                // Move pictures to new path when different to path in database
                if ((($dynamicType != null) || ($dynamicType != "")) || ($dynamicType != "0"))
                {
                    $pictureType = $this->getPictureItem($dynamicType);

                    // Get old path
                    $path = json_decode($pictureType->options)->path;

                    if ($path != $inputPath)
                    {
                        $this->movePictures($inputPath, $path, $dynamicType);
                    }
                }
                $options['4'] = '{ "filename" : "' . $inputName . '", "path" : "'
                    . $inputPath . '" }';
                break;
            case "5":
                $options['5'] = '{ "options" : "' . $app->input->getHtml('MULTISELECT_options') . '" }';
                break;
            case "6":
                $options['6'] = '{ "columns" : "' . $app->input->getHtml('TABLE_columns') . '" }';
                break;
        }
        $data['options'] = $options[$data['static_typeID']];

        $dbo->transactionStart();

        $dynamicType = $this->getTable();

        $success = $dynamicType->save($data);


        if (!$success)
        {
            $dbo->transactionRollback();
            return false;
        }
        else
        {
            $dbo->transactionCommit();
            return $dynamicType->id;
        }
    }

    /**
     * Delete element from list
     *
     * @return bool|mixed
     */
    public function delete()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);

        $conditions = array(
            $dbo->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
        );

        $query->delete($dbo->quoteName('#__thm_groups_dynamic_type'));
        $query->where($conditions);

        $dbo->setQuery($query);
        $result = $dbo->execute();

        // Joomla 3.x Error handling style
        if ($dbo->getErrorNum())
        {
            JFactory::getApplication()->enqueueMessage($dbo->getErrorMsg(), 'error');

            return false;
        }

        return $result;
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
     * Creates a new directory
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
     * @param   Integer  $id  ID of selected dynamicType
     *
     * @return null|$result
     */
    private function getPictureItem($id)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('*')
              ->from($dbo->qn('#__thm_groups_dynamic_type'))
              ->where('id = ' . (int) $id);

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
     * Checks if a different path has been set in attribute
     *
     * @param   Integer  $attributeID  ID of given attribute
     * @param   String   $oldPath      Old path of dynamic type
     *
     * @return  boolean
     */
    private function attributePathSet($attributeID, $oldPath)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('options')
            ->from($dbo->qn('#__thm_groups_attribute'))
            ->where('id = ' . (int) $attributeID);

        $dbo->setQuery($query);
        $result = $dbo->loadObject();
        $options = json_decode($result->options);

        if ($options->path == $oldPath)
        {

            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Copies all pictures with given dynamic type from old directory to
     * new directory, when the path in the related attribute-table-entry
     * is equal to the path in dynamic type.
     *
     * @param   String  $oldPath      old directory path
     * @param   String  $newPath      new directory path
     * @param   Int     $dynamicType  structure id
     *
     * @return   boolean
     */
    private function copyPictures($oldPath, $newPath, $dynamicType)
    {
        $pictures = self::getPictures($dynamicType);
        $dbo = JFactory::getDbo();
        $save = array();

        /*echo "<hr/>";
        var_dump($pictures);
        echo "<hr/>";*/

        // Check every single pic in selected folder
        foreach (scandir($oldPath) as $folderPic)
        {
            // Check if picture in folder is affected by path change
            foreach ($pictures as $pic)
            {
                for ($i = 0; $i < count($pic); ++$i)
                {
                    $picName = $pic[$i]->value;
                    if ($folderPic == $picName)
                    {
                        if (!$this->attributePathSet($pic[$i]->attributeID, $oldPath))
                        {
                            copy($oldPath . $folderPic, $newPath . $folderPic);
                            unlink($oldPath . $folderPic);

                            // Save elements the database has to be updated for
                            array_push($save, $pic[$i]);
                        }
                        else
                        {
                            // Remove element when it's path has set in attribute table
                            unset($pic[$i]);
                        }
                    }
                }
            }
        }
        try
        {
            foreach ($save as $pic)
            {
                $query = $dbo->getQuery(true);
                $query->update($dbo->qn('#__thm_groups_attribute'))
                    ->set($dbo->qn('options') . ' = ' . $dbo->quote('{ "filename" : "anonym.jpg", "path" : "' . $newPath . '" }'))
                    ->where($dbo->qn('id') . ' = ' . (int) $pic->attributeID . '');
                $dbo->setQuery($query);

                $dbo->execute();
            }
        }
        catch (JDatabaseException $exception2)
        {
                JFactory::getApplication()->enqueueMessage($exception2->getMessage(), 'error');
                return false;
        }

        return true;
    }

    /**
     * Gets the pictures by searching for the dynTypeID in
     * the attribute-table and then getting all entries from
     * thm_groups_users_attribute-table
     *
     * @param   Integer  $dynamicType  The dynamicType id
     *
     * @return   Array
     */
    private function  getPictures($dynamicType)
    {
        $items = array();
        $dbo = JFactory::getDbo();
        $attributeQuery = $dbo->getQuery(true);

        $attributeQuery->select($dbo->qn('id'))
                       ->from($dbo->qn('#__thm_groups_attribute'))
                       ->where($dbo->qn('dynamic_typeID') . ' = ' . $dynamicType . '');

        $dbo->setQuery($attributeQuery);

        // All attributes of dynamicType
        $attributeEntries = $dbo->loadObjectList();

        // Get all usages of attribute from users_attribute
        foreach ( $attributeEntries as $attEntr )
        {
            $usersAttributeQuery = $dbo->getQuery(true);
            $usersAttributeQuery->select($dbo->qn(array('ID', 'value', 'attributeID')))
                                ->from($dbo->qn('#__thm_groups_users_attribute'))
                                ->where($dbo->qn('attributeID') . ' = ' . $attEntr->id . '');
            $dbo->setQuery($usersAttributeQuery);
            $result = $dbo->loadObjectList();

            if ( $result != null )
            {
                array_push($items, $result);
            }
        }

        return $items;
    }

    /**
     * Moves pictures from old to new directory, deletes the old files, writes
     * new paths into database table #__thm_groups_attribute if no special path
     * has been set in attribute table.
     * The path for #__thm_groups_dynamicType will be set in
     * calling function.
     *
     * @param   Integer  $inputPath    Typed in path from user-interface
     * @param   String   $path         Old path from database
     * @param   Integer  $dynamicType  DynamicTypeID from form
     *
     * @return  boolean
     */
    private function movePictures($inputPath, $path, $dynamicType)
    {
        if (!self::dirExists($inputPath))
        {
            self::makeNewDir($inputPath);
        }
        if (!self::copyPictures($path, $inputPath, $dynamicType))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}