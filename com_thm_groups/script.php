<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.general
 * @name        Script
 * @description Script file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
if (!defined('_JEXEC'))
{
    define('_JEXEC', 1);
}
if (!defined('JPATH_BASE'))
{
    define('JPATH_BASE', '../../../');
}
if (!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

require_once JPATH_BASE . DS . 'includes' . DS . 'defines.php';
require_once JPATH_BASE . DS . 'includes' . DS . 'framework.php';

require JPATH_ROOT . DS . "administrator" . DS . "components" . DS . "com_installer" . DS . "models" . DS . "manage.php";


$mainframe = JFactory::getApplication('site');
$mainframe->initialise();

/**
 * ThmGroupsInstaller
 *
 * @category    Joomla.Component.General
 * @package     thm_groups
 * @subpackage  com_thm_groups
 * @since       v3.2.2
 */
class Com_THM_GroupsInstallerScript
{

    /**
     * Deletes all files of component before install
     *
     * @param   Object  $type
     * @param   Object  $parent
     */
    function preflight($type, $parent)
    {
        $admin = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_thm_groups';
        $site = JPATH_ROOT . DS . 'components' . DS . 'com_thm_groups';

        if(is_dir($admin) && is_dir($site))
        {
            self::deleteDir($admin);
            self::deleteDir($site);
        }
    }

    /**
      * com_thm_groups install function
      *
      * @param   Object  $parent  JInstallerComponent
      *
      * @return void
      */
    public function install($parent)
    {
        ?>
        <h1 align="center">
            <strong>&nbsp;THM Groups Installer</strong>
        </h1>
        <?php

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__usergroups'));
        $db->setQuery($query);
        $db->query();
        $rows = $db->loadObjectlist();

        // Sync Groups to database
        foreach ($rows as $row)
        {
            $query = $db->getQuery(true);
            $query->insert("#__thm_groups_groups (id, name, info, picture, mode, injoomla)");
            $query->values("$row->id , '" . $row->title . "' , ' ' , ' ' , ' ' , 1");
            $db->setQuery($query);

            if ($db->query())
            {
                echo "
                <p align=\"left\">
                <strong>&nbsp;" . $row->title . " Group added to database!</strong>
                </p>";
            }
        }
        $this->release = $parent->get("manifest")->version;

        ?>
        <p align="center">
            <strong>&nbsp;Installation of version <?php echo $this->release; ?> successful!</strong>
        </p>
        <?php
    }

    /**
     * com_thm_groups update function
     *
     * @param   Object  $parent  JInstallerComponent
     *
     * @return void
     */
    public function update($parent)
    {
        /*
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select("extension_id");
        $query->from("#__extensions");
        $query->where("(element = 'plg_thm_groups_content_members') OR"
                . "(element = 'plg_thm_groups_content_wai') OR (element = 'plg_thm_groups_editors_xtd_members') OR"
                . "(element = 'plg_thm_groups_editors_xtd_wai')"
                    );

        $db->setQuery($query);
        $ids = $db->loadResultArray();

        if (count($ids))
        {
            $uninstall = new InstallerModelManage;
            $uninstall->remove($ids);
        }
        */

        ?>
        <h1 align="center">
            <strong>&nbsp;THM Groups Updater</strong>
        </h1>
        <?php

        $this->release = $parent->get("manifest")->version;
        ?>
        <p align="center">
            <strong>&nbsp;Installation of version <?php echo $this->release; ?> successful!</strong>
        </p>
        <?php
    }

    /**
     * Deletes recursively files
     *
     * @param   String  $path  path of admin and site part of component
     */
    function deleteDir($path)
    {
        $it = new RecursiveDirectoryIterator($path);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        $doNotDeleteDirs = array();
        array_push($doNotDeleteDirs, '.');
        array_push($doNotDeleteDirs, '..');

        foreach ($files as $file)
        {
            if (in_array($file->getFilename(), $doNotDeleteDirs))
            {
                continue;
            }
            if ($file->isDir())
            {
                if (strpos($file->getRealPath(), 'img') === false)
                {
                    rmdir($file->getRealPath());
                }
            }
            else
            {
                if (strpos($file->getRealPath(), 'img') === false)
                {
                    unlink($file->getRealPath());
                }
            }
        }
    }

}