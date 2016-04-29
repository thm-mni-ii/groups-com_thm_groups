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
     * If THM Groups will be updated the script copies old data from old tables
     *
     * @return  bool   true on success, false otherwise
     */
    public static function update()
    {
        $pictureOptionsMigrated = self::migratePictureOptions();
        $tableOptionsMigrated = self::migrateTableOptions();
        $textOptionsMigrated = self::migrateTextOptions();
        $textFieldOptionsMigrated = self::migrateTextFieldOptions();

        if ($pictureOptionsMigrated AND $tableOptionsMigrated AND $textOptionsMigrated AND $textFieldOptionsMigrated)
        {
            return true;
        }

        return false;
    }

    /**
     * Copies picture information from the old table structure
     *
     * @return  bool   true on success, false otherwise
     */
    private static function migratePictureOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value AS filename, path')
            ->from('#__thm_groups_picture_extra');

        $dbo->setQuery($query);

        try
        {
            $listOptions = $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        if (empty($listOptions))
        {
            return true;
        }

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $temp = clone($option);
            unset($temp->structid);
            $temp->path = '/images/com_thm_groups/profile/';
            $temp->filename = 'anonym.jpg';
            $newJsonOptions[$option->structid] = json_encode($temp);
        }

        $optionsMigrated = self::updateStructureItemOptions($newJsonOptions);
        if ($optionsMigrated)
        {
            return true;
        }

        return false;
    }

    /**
     * Copies table values from the old table structure
     *
     * @return  bool  true on success, false otherwise
     */
    private static function migrateTableOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value')
            ->from('#__thm_groups_table_extra');

        $dbo->setQuery($query);

        try
        {
            $listOptions = $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        if (empty($listOptions))
        {
            return true;
        }

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $temp = clone($option);
            unset($temp->structid);
            $newJsonOptions[$option->structid] = json_encode($temp);
        }

        $optionsMigrated = self::updateStructureItemOptions($newJsonOptions);
        if ($optionsMigrated)
        {
            return true;
        }

        return false;
    }

    /**
     * Copies text values from the old table structure
     *
     * @return  bool   true on success, false otherwise
     */
    private static function migrateTextOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value AS length')
            ->from('#__thm_groups_text_extra');

        $dbo->setQuery($query);

        try
        {
            $listOptions = $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        if (empty($listOptions))
        {
            return true;
        }

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $temp = clone($option);
            unset($temp->structid);
            $temp->required = "false";
            $newJsonOptions[$option->structid] = json_encode($temp);
        }

        $optionsMigrated = self::updateStructureItemOptions($newJsonOptions);
        if ($optionsMigrated)
        {
            return true;
        }

        return false;
    }

    /**
     * Copies text-field values from the old table structure
     *
     * @return  bool   true on success, false otherwise
     */
    private static function migrateTextFieldOptions()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('structid, value AS length')
            ->from('#__thm_groups_textfield_extra');

        $dbo->setQuery($query);

        try
        {
            $listOptions = $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
            return false;
        }

        if (empty($listOptions))
        {
            return true;
        }

        $newJsonOptions = array();

        foreach ($listOptions as $option)
        {
            $test = clone($option);
            unset($test->structid);
            $test->required = "false";
            $newJsonOptions[$option->structid] = json_encode($test);
        }

        $optionsMigrated = self::updateStructureItemOptions($newJsonOptions);
        if ($optionsMigrated)
        {
            return true;
        }

        return false;
    }

    /**
     * Generic function which saves data from old tables into the new tables
     *
     * @param   array  $options  An array with options to save
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
    private static function updateStructureItemOptions($options)
    {
        // No options to migrate, it's not an error, just continue
        if (empty($options))
        {
            return true;
        }

        $dbo = JFactory::getDbo();

        foreach ($options as $key => $option)
        {
            $query = $dbo->getQuery(true);
            $query
                ->update('#__thm_groups_attribute')
                ->set("`options` = '" . $option . "'")
                ->where("`id` = '" . $key . "'");
            $dbo->setQuery($query);

            try
            {
                $dbo->execute();
            }
            catch (Exception $exception)
            {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
                return false;
            }
        }

        return true;
    }
}