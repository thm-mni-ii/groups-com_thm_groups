<?php
/**
 * @version     v3.2.7
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
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modelform');
jimport('thm_groups.data.lib_thm_groups_quickpages');
jimport('thm_core.edit.model');
jimport('thm_groups.data.lib_thm_groups_user');
jimport('joomla.filesystem.file');

/**
 * THMGroupsModeledit class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
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
     * @param   Integer  $userId  User ID
     *
     * @return  mixed
     */
    public function getContent()
    {
        $app = JFactory::getApplication()->input;
        $userId = intval($app->get('gsuid'));
        $gsgid = intval($app->get('gsgid'));
        $profilId = THMLibThmGroupsUser::getGroupsProfile($gsgid);
        $myprofile = JFactory::getUser()->id == $userId;

        if($myprofile){
            $data = THMLibThmGroupsUser::getAllUserAttributesByUserID($userId);
        }
        else{
            $data = THMLibThmGroupsUser::getAllUserProfilData($userId, $profilId,false);
        }

        return $data;
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
            ->where('id=' . $attrID);
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
        $app = JFactory::getApplication()->input;
        $formData = $app->post->get('jform', array(), 'array');
        $content = $this->getContent();
        $userID = $formData['gsuid'];


        // Dimensions for thumbnails
        $sizes = array('300x300', '64x64', '250x125');


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
            $path = JPATH_ROOT . $pathAttr . "cropped_" . $filename;
            $success = jFile::upload($file['tmp_name'], $path, false);

            if ($success)
            {
                $image  = new JImage($path);
                $image->createThumbs($sizes, JImage::SCALE_INSIDE, JPATH_ROOT . $pathAttr . 'thumbs\\');

                $path = str_replace('\\', '/', $path);
                $position = strpos($path, 'images/');
                $convertedPath = substr($path, $position);
                //TODO FIXARRAYKEY changed something here
                $prev = "<img  src='" . JURI::root() . $convertedPath . "' style='display: block;"
                    . "max-width:500px; max-height:240px; width: auto; height: auto;'/>";
                $prev .= "<input type='hidden' name='jform[" . THMLibThmGroupsUser::getExtra($attrID)->name. "][file]'
                            value='" . "cropped_" . $filename . "'>";

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
        catch(Exception $e)
        {
            return $e;
        }
    }

    /**
     * @param $formData
     * @param $content
     * @param $userID
     */
    private function saveValues($formData, $content, $userID){
        $dbo = JFactory::getDbo();
        // Save new values in #__thm_groups_users_attribute
        foreach ($formData as $attr)
        {

                try
                {
                    $query = $dbo->getQuery(true);

                    $query->update($dbo->qn('#__thm_groups_users_attribute'));

                    // Set new value when it's different from value in database, set JSON when array (TABLE,MULTISELECT)
                    if(strcmp($attr['type'], 'TABLE') == 0 || strcmp($attr['type'], 'MULTISELECT') == 0 ){
                        $jsonString = json_encode($attr['value']);
                        $query->set($dbo->qn('value') . ' = ' . $dbo->quote($jsonString));
                    }
                    elseif(strcmp($attr['type'], 'PICTURE') == 0 && isset($attr['file'])){
                        $query->set($dbo->qn('value') . ' = ' . $dbo->quote($attr['file']));
                    }
                    else
                    {
                        $query->set($dbo->qn('value') . ' = ' . $dbo->quote($attr['value']));
                    }

                    if (isset($attr['published']))
                    {
                        $published = $attr['published'];

                        if ( $published === 'on')
                        {
                            $query->set($dbo->qn('published') . ' = ' . 1);
                        }

                    }
                    else
                    {
                        $query->set($dbo->qn('published') . ' = ' . 0);
                    }


                    $query->where(
                        $dbo->qn('usersID') . ' = ' . intval($userID) . ' AND '
                        . $dbo->qn('attributeID') . ' = ' . intval($attr['strucid']) . ''
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
        foreach ($formData as $key=>$value)
        {
            $sKey = (string) $key;
            $vals = explode(' ',$sKey);
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
        foreach ($final as $key=>$column)
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
                if(sizeof($final)!= 1)
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
                unlink(JPATH_ROOT . $attrPath . $attribute->value);

                // Delete fullRes
                $oriFileName = $this->after('cropped_', $attribute->value);
                unlink(JPATH_ROOT . $attrPath . 'fullRes\\' . $oriFileName);

                // Delete thumbs
                foreach ( scandir(JPATH_ROOT . $attrPath . 'thumbs\\') as $folderPic)
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
                            unlink(JPATH_ROOT . $attrPath . 'thumbs\\' . $folderPic);
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

                    $path = JPATH_ROOT . $attrPath . 'fullRes\\' . $value['name'];
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
