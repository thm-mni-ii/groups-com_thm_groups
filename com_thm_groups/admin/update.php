<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.general
 * @name        Script
 * @description Script file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
if (!defined('_JEXEC'))
{
    define('_JEXEC', 1);
}
if (!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * THM_Groups_Update_Script
 *
 * @category    Joomla.Component.General
 * @package     thm_groups
 * @subpackage  com_thm_groups
 */
class THM_Groups_Update_Script
{

    /**
     * Update script for THM Groups
     *
     * If THM Groups will be updated the script
     * copies old data from old tables
     *
     * @return bool
     */
    public static function update()
    {
        if (self::migratePictureOptions()
         /* && self::migrateMultiselectOptions() */
         && self::migrateTableOptions()
         && self::migrateTextOptions()
         && self::migrateTextFieldOptions()
         /* && self::copyModuleToProfile() */
         /* && self::copyMenuToProfile()*/ )
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('update', 'error');
        return false;
    }

    /**
     * Copy Advanced Menu in Profile
     *
     * @return bool
     */
    private static function copyMenuToProfile()
    {
        try
        {
            $db = JFactory::getDbo();
            $searchAllQuery = $db->getQuery(true);
            $searchAllQuery->select('id as menuid, title as menutitle, params as menuparams')
                ->from('#__menu')
                ->where("link LIKE 'index.php?option=com_thm_groups&view=advanced'")
                ->where("type LIKE 'component'");

            $db->setQuery($searchAllQuery);
            $searchAllData = $db->loadObjectList();
            $allProfilData = [];

            if (!empty($searchAllData))
            {
                foreach ($searchAllData as $menuData)
                {
                    $profilItem = new stdClass;
                    $menuparams = json_decode($menuData->menuparams);
                    $menuGroupID = $menuparams->selGroup;
                    $profilItem->gid = $menuGroupID;
                    $menuAttrStruct = $menuparams->struct;
                    $profilItem->attrData = self::getOldAttrToProfileData($menuAttrStruct);
                    $profilItem->name = $menuparams->menutitle;
                    $allProfilData [] = $profilItem;
                }
                // Insert Profile in the Database
                $insertProfileQuery = $db->getQuery(true);
                $profileColumn = array("name", "order");
                $insertProfileQuery->insert('#__thm_groups_profile')
                    ->columns($profileColumn);
                $profileorder = 1;
                foreach ($allProfilData as $profilData)
                {
                    $insertProfileQuery->values($profilData->name, $profileorder);
                    $profileorder++;
                }
                $db->setQuery($insertProfileQuery);
                try
                {
                    $db->execute();
                }
                catch (Exception $exc)
                {
                    JFactory::getApplication()->enqueueMessage('copyMenuToProfile: ' . $exc->getMessage(), 'error');
                    return false;
                }
                $firstProfileID = $db->insertid() - count($allProfilData);

                // Insert Profile and Groups in the Database
                $insertProfileGroupQuery = $db->getQuery(true);
                $profileGroupColumn = array("profileID", "usergroupsID");
                $insertProfileGroupQuery->insert('#__thm_groups_profile_usergroups')
                    ->columns($profileGroupColumn);
                $profilIdcounter = $firstProfileID;
                foreach ($allProfilData as $profilData)
                {
                    $insertProfileGroupQuery->values($profilIdcounter, $profilData->gid);
                    $profilData->id = $profilIdcounter;
                    $profilIdcounter++;
                }
                $db->setQuery($insertProfileGroupQuery);
                $db->execute();

                // Insert Profile and Attribute in the Database
                $insertProfileAttrQuery = $db->getQuery(true);
                $profileAttrColumn = array("profileID", "attributeID", "order", "params");
                $insertProfileAttrQuery->insert('#__thm_groups_profile_attribute')
                    ->columns($profileAttrColumn);
                foreach ($allProfilData as $profilData)
                {
                    foreach ($profilData->attrData as $attritem)
                    {
                        $insertProfileAttrQuery->values($profilData->id, $attritem->attrID, $attritem->order, json_encode($attritem->params));
                    }
                }
                $db->setQuery($insertProfileAttrQuery);
                $db->execute();
            }
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage() . ' copyMenuToProfile', 'error');
            return false;
        }
        return true;
    }

    /**
     * Copy Thm Groups Module in Profile
     *
     * @return bool
     */
    private static function copyModuleToProfile()
    {
        if (self::copyModuleTypeToProfile('mod_thm_groups_smallview')
         && self::copyModuleTypeToProfile("mod_thm_groups_members"))
        {
            return true;
        }
        return false;
    }

    /**
     * Copy Thm Groups Module in Profile
     *
     * @param   String  $moduletype  Type of module
     *
     * @return bool
     */
    private static function copyModuleTypeToProfile($moduletype)
    {
        try
        {
            $db = JFactory::getDbo();
            $searchAllQuery = $db->getQuery(true);
            $searchAllQuery->select('id as modid, title as modtitle, params as modparams')
                ->from('#__modules')
                ->where("module like '$moduletype'");

            $db->setQuery($searchAllQuery);
            $searchAllData = $db->loadObjectList();
            $allProfilData = [];
            foreach ($searchAllData as $modData)
            {
                $profilItem = new stdClass;
                $modparams = json_decode($modData->menuparams);
                $modGroupID = $modparams->selGroup;
                $profilItem->gid = $modGroupID;
                if (strcmp($moduletype, 'mod_thm_groups_smallview') == 0)
                {
                    $modAttrStruct = $modparams->structid;
                }
                else
                {
                    $modAttrStruct = $modparams->struct;
                }

                $profilItem->attrData = self::getOldAttrToProfileData($modAttrStruct);
                $profilItem->name = 'mod_' . $modparams->menutitle;
                $allProfilData [] = $profilItem;
            }

            // Insert Profile in the Database
            if (!empty($allProfilData))
            {
                $insertProfileQuery = $db->getQuery(true);
                $profileColumn = array("name", "order");
                $insertProfileQuery->insert('#__thm_groups_profile')
                    ->columns($profileColumn);
                $profileorder = 1;

                foreach ($allProfilData as $profilData)
                {
                    $insertProfileQuery->values($profilData->name, $profileorder);
                    $profileorder++;
                }
                $db->setQuery($insertProfileQuery);
                $db->execute();
                $firstProfileID = $db->insertid() - count($allProfilData) + 1;

                // Insert Profile and Groups in the Database
                $insertProfileGroupQuery = $db->getQuery(true);
                $profileGroupColumn = array("profileID", "usergroupsID");
                $insertProfileGroupQuery->insert('#__thm_groups_profile_usergroups')
                    ->columns($profileGroupColumn);
                $profilIdcounter = $firstProfileID;
                foreach ($allProfilData as $profilData)
                {
                    $insertProfileGroupQuery->values($profilIdcounter, $profilData->gid);
                    $profilData->id = $profilIdcounter;
                    $profilIdcounter++;
                }
                $db->setQuery($insertProfileGroupQuery);
                $db->execute();

                // Insert Profile and Attribute in the Database
                $insertProfileAttrQuery = $db->getQuery(true);
                $profileAttrColumn = array("profileID", "attributeID", "order", "params");
                $insertProfileAttrQuery->insert('#__thm_groups_profile_attribute')
                    ->columns($profileAttrColumn);
                foreach ($allProfilData as $profilData)
                {
                    foreach ($profilData->attrData as $attritem)
                    {
                        $insertProfileAttrQuery->values($profilData->id, $attritem->attrID, $attritem->order, json_encode($attritem->params));
                    }
                }
                $db->setQuery($insertProfileAttrQuery);
                $db->execute();
            }
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage() . ' copyModuleTypeToProfile', 'error');
            return false;
        }
        return true;
    }


    /**
     * Transform the old Attribute Structure to the new
     *
     * @param   array  $oldAttrStruct  Jo, write please comments!
     *
     * @return  something->ask Jo
     */
    private static function  getOldAttrToProfileData($oldAttrStruct)
    {
        $result = [];
        $count = 1;
        foreach ($oldAttrStruct as $id => $oldItem)
        {
            $newItem = new stdClass;
            $stringFormOld = "" . $oldItem;
            $wrap = substr($stringFormOld, -1);
            $label = substr($stringFormOld, -2);
            $attrID = substr($stringFormOld, 0, -2);
            $newItem->attrID = $attrID;
            $newItem->order = $count;
            $newItem->params = [];
            $newItem->params['label'] = $label;
            $newItem->params['wrap'] = $wrap;
            $result [] = $newItem;
            $count++;
        }
        return $result;
    }

    /**
     * Copies picture information from
     * the old table structure
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function migratePictureOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value AS filename, path')
            ->from('#__thm_groups_picture_extra');

        $dbo->setQuery($query);

        $listOptions = $dbo->loadObjectList();

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $test = clone($option);
            unset($test->structid);
            $newJsonOptions[$option->structid] = json_encode($test);
        }

        if (self::updateStructureItemOptions($newJsonOptions))
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('migratePictureOptions', 'error');
        return false;
    }

    /**
     * Copies multiselect values from the
     * old table structure
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function migrateMultiselectOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value')
            ->from('#__thm_groups_multiselect_extra');

        $dbo->setQuery($query);

        $listOptions = $dbo->loadObjectList();

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $test = clone($option);
            unset($test->structid);
            $newJsonOptions[$option->structid] = json_encode($test);
        }

        if (self::updateStructureItemOptions($newJsonOptions))
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('migrateMultiselectOptions', 'error');
        return false;
    }

    // TODO deprecate this method and don't use tables in THM Groups at all
    /**
     * Copies table values from the
     * old table structure
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function migrateTableOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value')
            ->from('#__thm_groups_table_extra');

        $dbo->setQuery($query);

        $listOptions = $dbo->loadObjectList();

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $test = clone($option);
            unset($test->structid);
            $newJsonOptions[$option->structid] = json_encode($test);
        }

        if (self::updateStructureItemOptions($newJsonOptions))
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('migrateTableOptions', 'error');
        return false;
    }

    /**
     * Copies text values from the
     * old table structure
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function migrateTextOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value AS length')
            ->from('#__thm_groups_text_extra');

        $dbo->setQuery($query);

        $listOptions = $dbo->loadObjectList();

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $test = clone($option);
            unset($test->structid);
            $test->required = "false";
            $newJsonOptions[$option->structid] = json_encode($test);
        }

        if (self::updateStructureItemOptions($newJsonOptions))
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('migrateTextOptions', 'error');
        return false;
    }

    /**
     * Copies text-field values from the
     * old table structure
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function migrateTextFieldOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value AS length')
            ->from('#__thm_groups_textfield_extra');

        $dbo->setQuery($query);

        $listOptions = $dbo->loadObjectList();

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $test = clone($option);
            unset($test->structid);
            $test->required = "false";
            $newJsonOptions[$option->structid] = json_encode($test);
        }

        if (self::updateStructureItemOptions($newJsonOptions))
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('migrateTextFieldOptions', 'error');
        return false;
    }

    /**
     * Generic function which saves data from
     * old tables into the new tables
     *
     * @param   array  $options  An array with options to save
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function updateStructureItemOptions($options)
    {
        $dbo = JFactory::getDbo();

        foreach ($options as $key => $option)
        {

            $query = $dbo->getQuery(true);
            $query
                ->update($dbo->quoteName('#__thm_groups_attribute'))
                ->set("`options` = '" . $option . "'")
                ->where("`id` = '" . $key . "'");
            $dbo->setQuery($query);

            try
            {
                $dbo->execute();
            }
            catch (Exception $exc)
            {
                JFactory::getApplication()->enqueueMessage('updateStructureItemOptions: ' . $exc->getMessage(), 'error');
                return false;
            }
        }

        return true;
    }
}