<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('thm_core.edit.model');
jimport('thm_groups.data.lib_thm_groups_user');
jimport('joomla.filesystem.file');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelUser_Edit extends THM_CoreModelEdit
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        $ids = $app->input->get('cid', array(), 'array');

        // Input->get because id is in url
        $id = (empty($ids)) ? $app->input->get->get('id') : $ids[0];
        $item = $this->getItem($id);

        /* $content = $this->getContent($id);
         // add data
         foreach ($content as $field)
         {
             $attrName = $field->attribute;
             $item->$attrName = $field->value;
         }*/

        return $item;
    }

    /**
     * Returns all user attributes for the user edit form
     *
     * @return  mixed
     */
    public function getContent()
    {
        $userId = JFactory::getApplication()->input->get('id');

        if ($userId == null)
        {
            $formData = JFactory::getApplication()->input->post->get('jform', array(), 'array');
            $userId = $formData['userID'];
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('ust.usersID, ust.attributeID, st.options, dyn.regex, st.name as attribute, ust.value, ust.published, static.name')
            //->select('st.name as attribute, ust.value')
            ->from('#__thm_groups_users_attribute AS ust')
            ->innerJoin('#__thm_groups_attribute AS st ON ust.attributeID = st.id')
            ->innerJoin('#__thm_groups_dynamic_type AS dyn ON st.dynamic_typeID = dyn.id')
            ->innerJoin('#__thm_groups_static_type AS static ON dyn.static_typeID = static.id')
            ->where("ust.usersID IN ( $userId )")
            ->order('ust.attributeID');
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
     * Returns the id and options (includes path) for given picture-attribute.
     * Options is JSON
     *
     * @param   Integer  $attrID  Attribute ID
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function  getPicturePath($attrID)
    {
        $dbo = JFactory::getDBO();

        $query = $dbo->getQuery(true);
        $query->select('id, options')
            ->from('#__thm_groups_attribute')
            ->where('id =' . $attrID);
        try
        {
            $dbo->setQuery($query);
            $res = $dbo->loadObject();
        }
        catch (RuntimeException $e)
        {
            throw new Exception($e->getMessage());
        }

        return $res;
    }

    /**
     * Checks what attributes the user has and compares the input data with
     * the data in the database. Changed values will be saved in #__thm_groups_users_attribute.
     * Uploaded pictures will be saved in full resolution in the folder: '../images/'attributePath'/fullRes/..'
     * If new picture was uploaded, the cropped picture has been saved in saveCropped() function.
     *
     * @param   null  $data  Joomla standard
     *
     * @return  Integer  $userID  User ID
     */
    public function save($data = null)
    {
        $app = JFactory::getApplication();
        $formData = $app->input->post->get('jform', array(), 'array');
        $content = $this->getContent();
        $userID = $formData['userID'];

        // Dimensions for thumbnails
        $sizes = array('300x300', '64x64', '250x125');

        // Change '_' in array into ' '
        $this->fixArrayKey($formData);

        $this->saveValues($formData, $content, $userID);
        $this->saveFullSizePictures($formData, $content, $userID);

        return $userID;
    }

    /**
     * Saves the cropped image that was uploaded via ajax in the user_edit.view
     *
     * @param   Integer  $attrID    Attribute ID
     * @param   File     $file      Uploaded file
     * @param   String   $filename  Uploaded filename
     *
     * @return bool|mixed|string
     */
    public function saveCropped($attrID, $file, $filename)
    {
        $pathAttr = $this->getLocalPath($attrID);
        $sizes = array('64x64', '250x125');

        if ($file != null)
        {
            $path = JPATH_ROOT . "\\" . $pathAttr . "\cropped_" . $filename;
            var_dump($path);
            $success = jFile::upload($file['tmp_name'], $path, false);

            if ($success)
            {
                $image  = new JImage($path);
                $image->createThumbs($sizes, JImage::SCALE_INSIDE, JPATH_ROOT . "\\" . $pathAttr . 'thumbs\\');

                $path = str_replace('\\', '/', $path);
                $position = strpos($path, 'images/');
                $convertedPath = substr($path, $position);
                $prev = "<img  src='" . JURI::root() . $convertedPath . "' style='display: block;"
                    . "max-width:500px; max-height:240px; width: auto; height: auto;'/>";

                return $prev;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Deletes picture from path
     *
     * @param $userID
     * @param $attributeID
     *
     * @return mixed
     */
    public function deletePicture($attributeID)
    {
        $content = $this->getContent();
        try
        {
            $this->deleteOldPictures($content, $attributeID);

            // TODO return default pic?
            return "true";
        }
        catch (Exception $e)
        {
            return $e;
        }
    }

    private function getJsonTable($uid, $attrid)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query
            ->select('*')
            ->from('#__thm_groups_users_attribute AS ust')
            ->where("ust.usersID = ( $uid) and ust.attributeID = ( $attrid)");
        $dbo->setQuery($query);

        return $dbo->loadObjectList();
    }

    /**
     * @param $formData
     * @param $content
     * @param $userID
     */
    private function saveValues($formData, $content, $userID)
    {
        $dbo = JFactory::getDbo();

        // Save new values in #__thm_groups_users_attribute
        foreach ($content as $attr)
        {
            var_dump($attr);
            if (array_key_exists($attr->attribute, $formData))
            {
                var_dump($attr->attribute);
                try
                {
                    $newValue = false;
                    $query = $dbo->getQuery(true);

                    $query->update($dbo->qn('#__thm_groups_users_attribute'));

                    // Set new value when it's different from value in database, set JSON when array (TABLE,MULTISELECT)
                    if (is_array($formData[$attr->attribute]))
                    {
                        $jsonString = $this->createJSON($attr, $formData[$attr->attribute]);
                        $query->set($dbo->qn('value') . ' = ' . $dbo->quote($jsonString));
                        $newValue = true;
                    }
                    elseif ($formData[$attr->attribute] != $attr->value)
                    {
                        $query->set($dbo->qn('value') . ' = ' . $dbo->quote($formData[$attr->attribute]));
                        $newValue = true;
                    }

                    if (array_key_exists($attr->attribute . " published", $formData))
                    {
                        $published = $formData[$attr->attribute . " published"];

                        if (($published === 'on') && ($attr->published === '0'))
                        {
                            $query->set($dbo->qn('published') . ' = ' . 1);
                        }
                        elseif (!$newValue)
                        {
                            continue;
                        }
                    }
                    elseif ($attr->published === '1')
                    {
                        $query->set($dbo->qn('published') . ' = ' . 0);
                    }
                    elseif (!$newValue)
                    {
                        continue;
                    }

                    $query->where(
                        $dbo->qn('usersID') . ' = ' . (int) $attr->usersID . ' AND '
                        . $dbo->qn('attributeID') . ' = ' . (int) $attr->attributeID . ''
                    );

                    $dbo->setQuery($query);

                    $result = $dbo->execute();
                }
                catch (Exception $e)
                {
                    echo "Very demotivating error occurs";
                    echo $e->getMessage();
                }
            }
        }
    }

    /**
     * Creates a JSON String for table from given form data.
     *
     * @param $attr
     * @param $formData
     * @return string
     */
    private function createJSON($attr, $formData)
    {
        // Temp array
        $output = array();

        // Array for final structure
        $final = array();

        // Convert structure to fit in json
        $index = null;
        foreach ($formData as $key => $value)
        {
            $sKey = (string) $key;
            $vals = explode(' ', $sKey);
            $vals[0] = (int) $vals[0];

            if ($index === null)
            {
                $index = $vals[0];
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
            $size = sizeof($column);
            $empty = 0;
            foreach ($column as $row)
            {
                if ($row == '')
                {
                    $empty ++;
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
     * @param $content
     * @param $key
     */
    private function deleteOldPictures($content, $key)
    {
        // Get local path
        $attrPath = $this->getLocalPath($key);

        // Delete old file
        foreach ($content as $attribute)
        {
            if ($attribute->attributeID == $key)
            {
                // Delete cropped
                unlink(JPATH_ROOT . "\\" . $attrPath . "\\" . $attribute->value);

                // Delete fullRes
                $oriFileName = $this->after('cropped_', $attribute->value);
                unlink(JPATH_ROOT . "\\" . $attrPath . 'fullRes\\' . $oriFileName);

                // Delete thumbs
                foreach ( scandir(JPATH_ROOT . "\\" . $attrPath . "\\" . 'thumbs\\') as $folderPic)
                {
                    if ( $folderPic === '.' || $folderPic === '..')
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
                        $extPos = strrpos($folderPic, '_');
                        $length = strlen($folderPic);
                        $thumbFileName = substr($folderPic, 0, -($length - $extPos));

                        $pos = strpos($attribute->value, $thumbFileName);

                        if ($pos === 0)
                        {
                            unlink(JPATH_ROOT . "\\" . $attrPath . "\\" . 'thumbs\\' . $folderPic);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $formData
     * @param $content
     * @param $userID
     */
    private function saveFullSizePictures($formData, $content, $userID)
    {
        $dbo = JFactory::getDbo();

        // Upload given full size file(s)
        $filedata = JFactory::getApplication()->input->files->get('jform1');

        if ($filedata != null)
        {
            // Upload Images $key is attributeID
            foreach ($filedata['Picture'] as $key => $value)
            {
                if ($value['name'] === '')
                {
                    continue;
                }
                else
                {
                    // Get local path
                    $attrPath = $this->getLocalPath($key);

                    $path = JPATH_ROOT . "\\" . $attrPath . 'fullRes\\' . $value['name'];
                    $success = jFile::upload($value['tmp_name'], $path, false);

                    if (JFile::exists(JPATH_ROOT . $attrPath . 'cropped_' . $value['name']) && $success)
                    {
                        try
                        {
                            // Delete old files
                            $this->deleteOldPictures($content, $key);

                            // Update new picture filename
                            $query = $dbo->getQuery(true);

                            $query->update($dbo->qn('#__thm_groups_users_attribute'))
                                ->set($dbo->qn('value') . ' = ' . $dbo->quote('cropped_' . $value['name']))
                                ->where(
                                    $dbo->qn('usersID') . ' = ' . (int) $userID . ' AND '
                                    . $dbo->qn('attributeID') . ' = ' . (int) $key . '');

                            $dbo->setQuery($query);
                            $result = $dbo->execute();
                        }
                        catch (Exception $e)
                        {
                            echo $e->getMessage();
                        }
                    }
                    else
                    {
                        echo "one pic was empty or something went wrong";
                    }
                }
            }
        }
    }

    /**
     * Fixes form keys when they where generated from div id's (id's don't accept ' ' like 'Student Picture',
     * will be like 'Student_Picture' -> this function converts '_' to ' ')
     *
     * @param   Pointer  &$arr  Pointer to array
     *
     * @return null
     */
    private function fixArrayKey(&$arr)
    {
        $arr=array_combine(array_map(function($str){return str_replace("_"," ",$str);},array_keys($arr)),
            array_values($arr));

        foreach($arr as $key=>$val)
        {
            if(is_array($val)) $this->fixArrayKey($arr[$key]);
        }
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
     * Gets the local path that is needed to save the picture to the filesystem.
     *
     * @param   Integer  $attrID  Attribute ID
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function getLocalPath($attrID)
    {
        $attrPath = json_decode($this->getPicturePath($attrID)->options);

        // Convert / to \:
        $position = strpos($attrPath->path, '/images/');
        $path = substr($attrPath->path, $position);

        return $path = str_replace('/', '\\', $path);
    }
}
