<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';
jimport('joomla.filesystem.file');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelProfile_Edit_Model extends THM_GroupsModelEdit
{
    /**
     * Fixes file path resolution problems stemming from incorrect DSs.
     *
     * @param   string &$path
     *
     * @return  string  the corrected path
     */
    protected function correctPathDS($path)
    {
        if (DIRECTORY_SEPARATOR == '/')
        {
            return str_replace('\\', '/', $path);
        }
        elseif (DIRECTORY_SEPARATOR == '\\')
        {
            return str_replace('/', '\\', $path);
        }

        // Is there a third option?
        return $path;
    }

    /**
     * Creates a JSON string for table from given form data.
     *
     * @param   string $attr     The attribute
     * @param   array  $formData The formdata
     *
     * @return  string
     */
    protected function createJSON($attr, $formData)
    {
        // Temp array
        $output = array();

        // Array for final structure
        $final = array();

        // Convert structure to fit in json
        $index = null;

        foreach ($formData as $key => $value)
        {
            $sKey    = (string) $key;
            $vals    = explode(' ', $sKey);
            $vals[0] = (int) $vals[0];

            if ($index === null)
            {
                $index            = $vals[0];
                $output[$vals[1]] = $value;
            }
            elseif ($index != $vals[0])
            {
                $index = $vals[0];
                array_push($final, $output);
                $output[$vals[1]] = $value;
            }
            else
            {
                $output[$vals[1]] = $value;
            }
        }

        array_push($final, $output);

        // Check for empty columns and delete them
        foreach ($final as $key => $column)
        {
            $size  = sizeof($column);
            $empty = 0;

            foreach ($column as $row)
            {
                if ($row == '')
                {
                    $empty++;
                }
            }

            if ($size == $empty)
            {
                // Don't delete when it's the last element / prevents empty json crash
                if (sizeof($final) != 1)
                {
                    unset($final[$key]);
                }
            }
        }

        $json = json_encode($final);

        return $json;
    }

    /**
     * Deletes old Pictures
     *
     * @param   array  $attributes attributes assigned to the user
     * @param   string $key        attribute id
     *
     * @return  string
     */
    protected function deleteOldPictures($attributes, $key)
    {
        $filePath = $this->getPicturePath($key);

        $query = $this->_db->getQuery(true);
        $query->select('options');
        $query->from('#__thm_groups_attribute');
        $query->where("id = '" . (int) $key . "'");
        $this->_db->setQuery((string) $query);

        try
        {
            $optionsString = $this->_db->loadResult();
        }
        catch (Exception $exc)
        {
            JErrorPage::render($exc);
        }

        // TODO: There should be a check for an empty return here.

        $optionsObject = json_decode($optionsString);
        $defaultName   = $optionsObject->filename;

        // Delete old file
        foreach ($attributes as $attribute)
        {
            $fileName      = $attribute['value'];
            $uninteresting = $attribute['structid'] != $key;
            $relevant      = (($fileName != $defaultName) AND !empty($attribute['value']));
            $irrelevant    = ($uninteresting OR !$relevant);
            if ($irrelevant)
            {
                continue;
            }

            // Delete cropped
            if (file_exists(realpath(JPATH_ROOT . $filePath . $fileName)))
            {
                unlink(realpath(JPATH_ROOT . $filePath . $fileName));
            }

            if (file_exists(realpath(JPATH_ROOT . $filePath . 'fullRes' . DIRECTORY_SEPARATOR . $fileName)))
            {
                unlink(realpath(JPATH_ROOT . $filePath . 'fullRes' . DIRECTORY_SEPARATOR . $fileName));
            }
        }

        return;
    }

    /**
     * Resets picture value to the default.
     *
     * @param   string $attributeID The attribute id
     * @param   string $userID      The user id
     *
     * @return mixed
     */
    public function deletePicture($attributeID, $userID)
    {
        $content = THM_GroupsHelperProfile::getProfileData($userID);

        $attributeDefault = $this->deleteOldPictures($content, $attributeID);

        // Update new picture filename
        $query = $this->_db->getQuery(true);

        // Update the database with new picture information
        $query->update('#__thm_groups_users_attribute');
        $query->set('value = ' . $this->_db->quote($attributeDefault));
        $query->where('usersID = ' . (int) $userID);
        $query->where('attributeID = ' . (int) $attributeID . '');
        $this->_db->setQuery($query);

        try
        {
            $this->_db->execute();
        }
        catch (Exception $exc)
        {
            JErrorPage::render($exc);
        }

        return $attributeDefault;
    }

    /**
     * Fixes form keys when they where generated from div id's (id's don't accept ' ' like 'Student Picture',
     * will be like 'Student_Picture' -> this function converts '_' to ' ')
     *
     * @param   array &$array the array to be processed
     *
     * @TODO: Find a more elegant solution to this problem before it becomes one.
     *
     * @return  void
     */
    protected function fixArrayKey(&$array)
    {
        $array = array_combine(
            array_map(
                function ($str)
                {
                    return str_replace("_", " ", $str);
                },
                array_keys($array)
            ),
            array_values($array)
        );

        foreach ($array as $key => $val)
        {
            if (is_array($val))
            {
                $this->fixArrayKey($array[$key]);
            }
        }
    }

    /**
     * Returns all user attributes for the user edit form
     *
     * @param   int $userID the user id
     *
     * @return  array  array of arrays containing profile information
     */
    public function getAttributes($userID = 0)
    {
        $input  = JFactory::getApplication()->input;
        $userID = empty($userID) ? $input->getInt('userID', 0) : $userID;
        if (empty($userID))
        {
            return array();
        }

        return THM_GroupsHelperProfile::getProfileData($userID);
    }

    /**
     * Returns json for a table attribute
     *
     * @param   string $uid    The user id
     * @param   string $attrid The attribute id
     *
     * @return mixed
     */
    protected function getJsonTable($uid, $attrid)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query
            ->select('*')
            ->from('#__thm_groups_users_attribute AS ust')
            ->where("ust.usersID = ( $uid) and ust.attributeID = ( $attrid)");
        $dbo->setQuery($query);

        return $dbo->loadObjectList();
    }

    /**
     * Gets the local path that is needed to save the picture to the filesystem.
     *
     * @param   int $pictureID the attribute id of the picture
     *
     * @return  mixed
     *
     * @throws Exception
     */
    protected function getPicturePath($pictureID)
    {
        $query = $this->_db->getQuery(true);
        $query->select('options');
        $query->from('#__thm_groups_attribute');
        $query->where("id = '$pictureID'");
        $this->_db->setQuery((string) $query);

        try
        {
            $result = $this->_db->loadResult();
        }
        catch (Exception $exc)
        {
            JErrorPage::render($exc);
        }


        $configuredPath = json_decode($result)->path;

        // Convert / to \:
        $position  = strpos($configuredPath, '/images/');
        $localPath = substr($configuredPath, $position);

        return $this->correctPathDS($localPath);
    }

    /**
     * Method to load the form data
     *
     * @return  mixed  Object on success, false on failure.
     */
    protected function loadFormData()
    {
        $input       = JFactory::getApplication()->input;
        $selectedIDs = $input->get('cid', array(), 'array');
        $id          = (empty($selectedIDs)) ? $input->getInt('id', 0) : $selectedIDs[0];

        return $this->getItem($id);
    }

    /**
     * Saves user profile information
     *
     * @TODO  Add handling of failures
     *
     * @return  int  $userID  User ID
     */
    public function save($data = null)
    {
        $formData = JFactory::getApplication()->input->get('jform', array(), 'array');

        // Change '_' in array into ' '
        $this->fixArrayKey($formData);
        $this->saveValues($formData, THM_GroupsHelperProfile::getAllAttributes());

        $this->saveFullSizePictures($formData['userID']);

        return $formData['userID'];
    }

    /**
     * Saves the cropped image that was uploaded via ajax in the profile_edit.view
     *
     * @param   int    $attrID   Attribute ID
     * @param   File   $file     Uploaded file
     * @param   string $filename Uploaded filename
     * @param   string $userID   The user id
     *
     * @return  bool|mixed|string
     */
    public function saveCropped($attrID, $file, $filename, $userID)
    {
        if (empty($file))
        {
            return false;
        }

        // TODO: Make these configurable (Defined together here because of this comment.)
        $tooBig            = $file['size'] > 10000000;
        $sizes             = array('100x75', '140x105');
        $allowedExtensions = array('bmp', 'gif', 'jpg', 'jpeg', 'png', 'BMP', 'GIF', 'JPG', 'JPEG', 'PNG');

        if ($tooBig)
        {
            return false;
        }

        $pathAttr = $this->getPicturePath($attrID);

        $requestedExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $badExtension       = !in_array($requestedExtension, $allowedExtensions);
        if ($badExtension)
        {
            return false;
        }

        $newFileName = $userID . "_" . $attrID . "." . pathinfo($filename, PATHINFO_EXTENSION);
        $path        = $this->correctPathDS(JPATH_ROOT . $pathAttr . $newFileName);

        $frame = THM_GroupsHelperProfile::getProfileData($userID);

        /*
         * All image names are the same now - id_attrid.extension
         * delete old files to avoid dead files on server because of
         * file-extension differences
         */
        $this->deleteOldPictures($frame, $attrID);

        // Upload new cropped image
        $uploaded = JFile::upload($file['tmp_name'], $path, false);

        // Create thumbs and send back prev image to the form
        if ($uploaded)
        {
            $image = new JImage($path);
            $image->createThumbs($sizes, JImage::SCALE_INSIDE, JPATH_ROOT . $pathAttr . 'thumbs' . DIRECTORY_SEPARATOR);
            $position      = strpos($path, 'images' . DIRECTORY_SEPARATOR);
            $convertedPath = substr($path, $position);

            // TODO: Move style declarations to a stylesheet...
            // TODO: Build HTML in the view.html file
            $previousImage = "<img  src='" . JURI::root() . $convertedPath . "?" . DATE_ATOM . "' style='display: block;"
                . "max-width:500px; max-height:240px; width: auto; height: auto;'/>";

            return $previousImage;
        }
    }

    /**
     * Saves uploaded picture in full size and stores new value of image in the specific user-attribute.
     * Filedata array has to be named : 'Picture', containing array elements are named by its attribute id.
     *
     * Note: If the 'upload_max_filesize' variable in PHP.ini is set to a low value, upload operations for
     * pictures will fail!.
     *
     * @param   string $userID The user id
     *
     * @return void
     */
    protected function saveFullSizePictures($userID)
    {
        $formFile = JFactory::getApplication()->input->files->get('jform1');
        if (empty($formFile))
        {
            return;
        }

        // Upload Images $key is attributeID
        foreach ($formFile['Picture'] as $attributeID => $value)
        {
            if (empty($value['size']))
            {
                continue;
            }

            $picturePath = $this->getPicturePath($attributeID);
            $newFileName = $userID . "_" . $attributeID . "." . pathinfo($value['name'], PATHINFO_EXTENSION);
            $path        = $this->correctPathDS(JPATH_ROOT . $picturePath . 'fullRes/' . $newFileName);

            $pictureUploaded = JFile::upload($value['tmp_name'], $path, false);
            $croppedExists   = JFile::exists(realpath(JPATH_ROOT . $picturePath . $newFileName));
            $updateTable     = ($croppedExists AND $pictureUploaded);
            if ($updateTable)
            {
                $query = $this->_db->getQuery(true);
                $query->update('#__thm_groups_users_attribute');
                $query->set('value= ' . $this->_db->quote($newFileName));
                $query->where('usersID = ' . (int) $userID);
                $query->where('attributeID = ' . (int) $attributeID);

                $this->_db->setQuery($query);
                try
                {
                    $this->_db->execute();
                }
                catch (Exception $exc)
                {
                    JErrorPage::render($exc);
                }
            }
            else
            {
                // TODO:  Make an expressive language constant for what actually went wrong.
                echo "one pic was empty or something went wrong";
            }
        }
    }

    /**
     * Saves the given values from the profile_edit form if the new values are different from the existing ones.
     *
     * @param   array $formData the submitted form data
     *
     * @return  void
     */
    protected function saveValues($formData)
    {
        $userID = $formData['userID'];
        foreach ($formData as $fieldName => $values)
        {
            if (is_string($values))
            {
                continue;
            }

            $published = (empty($values['published'])) ? 0 : 1;

            $query = $this->_db->getQuery(true);
            $query->update('#__thm_groups_users_attribute');
            $query->set("value = " . $this->_db->q($values['value']));
            $query->set("published = '$published'");
            $query->where("usersID = '" . intval($userID) . "'");
            $query->where("attributeID = '" . intval($values['attributeID']) . "'");

            $this->_db->setQuery((string) $query);

            try
            {
                $this->_db->execute();
            }
            catch (Exception $exc)
            {
                JErrorPage::render($exc);
            }
        }
    }
}
