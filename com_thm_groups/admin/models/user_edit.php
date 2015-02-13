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
     * Returns all user attributes for the user edit form
     *
     * @param   Integer  $userId  User ID
     *
     * @return  mixed
     */
    public function getContent($userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('ust.usersID, ust.attributeID, st.options, dyn.regex, st.name as attribute, ust.value, ust.published, static.name')
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
            if(is_array($val)) fixArrayKey($arr[$key]);
        }
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
        $dbo = JFactory::getDbo();
        $app = JFactory::getApplication();

        $formData = $app->input->post->get('jform', array(), 'array');
        $content = $this->getContent($formData['id']);
        $userID = $formData['id'];

        // Dimensions for thumbnails
        $sizes = array('300x300', '64x64', '250x125');

        // Change _ in array into ' '
        $this->fixArrayKey($formData);

        // Save new values in #__thm_groups_users_attribute
        foreach ($content as $attr)
        {
            if (array_key_exists($attr->attribute, $formData))
            {
                try
                {
                    $newValue = false;
                    $query = $dbo->getQuery(true);

                    $query->update($dbo->qn('#__thm_groups_users_attribute'));

                    if ($formData[$attr->attribute] != $attr->value)
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
                    echo $e->getMessage();
                }
            }
        }

        // Upload given full size file(s)
        $filedata = $app->input->files->get('jform1');

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
        return $userID;
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

    /**
     * Saves the cropped image that was uploaded via ajax in the user_edit.view
     *
     * @param   Integer  $userId    User ID
     * @param   Integer  $attrID    Attribute ID
     * @param   String   $element   Modal element
     * @param   File     $file      Uploaded file
     * @param   String   $filename  Uploaded filename
     *
     * @return bool|mixed|string
     */
    public function saveCropped($userId, $attrID, $element, $file, $filename)
    {
        // TODO avoid zombie pictures when user aborts the edit after he uploaded

        $pathAttr = $this->getLocalPath($attrID);
        $sizes = array('300x300', '64x64', '250x125');

        if ($file != null)
        {
            // TODO check file extension
            $path = JPATH_ROOT . $pathAttr . "cropped_" . $filename;
            $success = jFile::upload($file['tmp_name'], $path, false);

            if ($success)
            {
                $image  = new JImage($path);
                $image->createThumbs($sizes, JImage::SCALE_INSIDE, JPATH_ROOT . $pathAttr . 'thumbs\\');
                return $path;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns all groups of specific user
     *
     * @param   Integer  $userId  User ID
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getGroups($userId)
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*')
              ->from('#__thm_groups_mappings AS maps')
              ->where('maps.usersID = ' . $userId);
        try
        {
            $db->setQuery($query);
            $res = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            throw new Exception($e->getMessage());
        }

        return $res;
    }

    /**
     * Returns all groups and roles of specific user identified by given $userId
     *
     * @param   Integer  $userId  User ID
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getGroupsAndRoles($userId)
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('groups.title AS groupname, groups.id AS groupid, roles.name AS rolename, roles.id AS roleid');
        $query->from("#__usergroups AS groups");
        $query->leftJoin("#__thm_groups_mappings AS maps ON groups.id = maps.usergroupsID");
        $query->leftJoin("#__thm_groups_roles AS roles ON maps.rolesID = roles.id");
        $query->where("maps.usersID = " . $userId);

        try
        {
            $db->setQuery($query);
            $res = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            throw new Exception($e->getMessage());
        }

        return $res;

    }

    /**
     * Returns amount of all roles
     *
     * @return   Integer  $rows  Amount of roles
     *
     * @throws Exception
     */
    public function countRoles()
    {
      $roles = $this->getAllRoles();
      $rows = sizeof($roles);

      return $rows;
    }

    /**
     * Returns all Roles
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getAllRoles()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__thm_groups_roles');
        try
        {
            $db->setQuery($query);
            $res = $db->loadAssocList();
        }
        catch (RuntimeException $e)
        {
            throw new Exception($e->getMessage());
        }

        return $res;
    }

    /**
     * Returns all groups
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function  getAllGroups()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__usergroups');
        try
        {
            $db->setQuery($query);
            $res = $db->loadAssocList();
        }
        catch (RuntimeException $e)
        {
            throw new Exception($e->getMessage());
        }

        return $res;
    }

    /**
     * Saves new group and role for user
     *
     * @param   Integer  $groupId  Group ID
     * @param   Integer  $userId   User ID
     * @param   Integer  $roleId   Role ID
     *
     * @return null
     *
     * @throws Exception
     */
    public function saveGroupAndRole($groupId, $userId, $roleId)
    {
        $mapping = new stdClass;
        $mapping->usersID = $userId;
        $mapping->usergroupsId = $groupId;
        $mapping->rolesId = $roleId;

        try
        {
            JFactory::getDBO()->insertObject('#__thm_groups_mappings', $mapping);
            return "true";
        }
        catch (RuntimeException $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Add a new role
     *
     * @param   Integer  $groupId          Group ID
     * @param   Integer  $userId           User ID
     * @param   String   $groupName        GroupName
     * @param   String   $btnId            Div element id of button
     * @param   String   $roleContainerId  Div element id that contains role
     *
     * @return null
     */
    public function addRole($groupId, $userId, $groupName, $btnId, $roleContainerId)
    {
        // TODO implement stuff
    }


    /**
     * Returns all roles for user in group
     *
     * @param   Integer  $groupId  Group ID
     * @param   Integer  $userId   User ID
     *
     * @return array
     *
     * @throws Exception
     */
    public function checkRoles($groupId, $userId)
    {
        $GroupsAndRoles = $this->getGroupsAndRoles($userId);
        $roles = array();

        foreach ($GroupsAndRoles as $gnr)
        {
            if ($gnr->groupid == $groupId)
            {
                array_push($roles, $gnr->roleid);
            }
        }

        return $roles;
    }

    /**
     * Returns a new select field for roles. Returns reduced field set when
     * user already has several roles.
     *
     * @param   String   $groupname  Name of group
     * @param   String   $val        Selected value
     * @param   Integer  $groupId    ID of group
     * @param   mixed    $roles      All roles
     * @param   Integer  $counter    Amount of roles
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getRolesSelectField($groupname, $val, $groupId , $roles, $counter)
    {
        $options = array();
        $selectedKey = null;
        $groups = $this->getAllRoles();
        $selected = $val;

        if ($roles == null)
        {
            // Convert array to options
            foreach ($groups as $key => $value) :
                $options[] = JHTML::_('select.option', $value['id'], $value['name']);
                if ($selected == $value['name'])
                {
                    $selectedKey = $value['id'];
                }
            endforeach;
        }
        else
        {
            // Convert array to options
            foreach ($groups as $key => $value)
            {
                foreach ($roles as $role)
                {
                    if ($role != $value['id'])
                    {
                        $options[] = JHTML::_('select.option', $value['id'], $value['name']);
                        if ($selected == $value['name'])
                        {
                            $selectedKey = $value['id'];
                        }
                    }
                }

            }
        }

        if ($selectedKey == null)
        {
            $selectedKey = 1;
        }

        return $this->generateList($groupname, $selected, $options, $selectedKey, $groupId, $counter);
    }

    /**
     * Returns a selectField of groups
     *
     * @param   String   $name  Name of field
     * @param   String   $val   Selected value
     *
     * @return  mixed
     *
     * @throws Exception
     */
    public function getGroupsSelectField($name, $val)
    {
        $options = array();
        $selectedKey = null;
        $groups = $this->getAllGroups();
        $selected = $val;

        // Convert array to options
        foreach ($groups as $key => $value) :
            $options[] = JHTML::_('select.option', $value['id'], $value['title']);
            if ($selected == $value['title'])
            {
                $selectedKey = $value['id'];
            }
        endforeach;

        if ($selectedKey == null)
        {
            $selectedKey = 1;
        }

        return $this->generateList($name, $selected, $options, $selectedKey, null, null);
    }

    /**
     * Generates a selectField
     *
     * @param   String   $name         Name of field
     * @param   String   $selected     Selected value
     * @param   mixed    $options      Options
     * @param   Integer  $selectedKey  Selected key
     * @param   Integer  $groupId      ID of group
     * @param   Integer  $counter      Amount of roles
     *
     * @return mixed
     */
    public function generateList($name, $selected, $options, $selectedKey, $groupId, $counter)
    {
        $count = null;
        if ($counter == null)
        {
            $count = "";
        }
        else
        {
            $count = $counter;
        }

        // Todo: make id unique
        $settings = array(
            'id'            => 'jform_' . str_replace(' ', '', $name) . $selected,
            'option.key'    => 'value',
            'option.value'  => 'text',
            'onchange'      => '',
            'data'          => $groupId,
            'style'         => 'width: 180px!important;',
            'list.select'   => $selectedKey
        );

        // Generate selectfield:
        $selectField = JHtmlSelect::genericlist(
            $options,
            'jform[' . str_replace(' ', '', $name) . $selected . ']',
            $settings,
            'value',
            'text',
            $selectedKey
        );

        return $selectField;
    }

}