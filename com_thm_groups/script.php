<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.general
 * @name        Script
 * @description Script file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
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

jimport("thm_core.log.THMChangelogColoriser");

/**
 * ThmGroupsInstaller
 *
 * @category    Joomla.Component.General
 * @package     thm_groups
 * @subpackage  com_thm_groups
 */
class Com_THM_GroupsInstallerScript
{

    /*
     * Preflight runs before anything else and while the extracted files are in the uploaded temp folder.
     *
     * @param   $parent  is the class calling this method.
     * @param   $type    is the type of change (install, update or discover_install, not uninstall).
     *
     * @return  if preflight returns false, Joomla will abort the update and undo everything already done.
     */
    public function preflight($type, $parent)
    {
        echo '<hr>';

        // Installing component manifest file version
        $this->release = $parent->get("manifest")->version;
        $this->component = $parent->get("manifest")->name;

        if ($type == 'update' && $this->component == "COM_THM_GROUPS")
        {
            require_once 'admin/update.php';
            $oldRelease = $this->getParam('version');
            $rel = $oldRelease . ' &rArr; ' . $this->release;

            // For old versions before 3.5.0, deletes some unused files
            if (version_compare($oldRelease, '3.5.0', 'le'))
            {
                $admin = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_thm_groups';
                $site = JPATH_ROOT . DS . 'components' . DS . 'com_thm_groups';

                if (is_dir($admin) && is_dir($site))
                {
                    self::deleteDir($admin);
                    self::deleteDir($site);
                }
            }

            // Abort if the component being installed is not newer than the currently installed version
            if (version_compare($this->release, $oldRelease, 'le'))
            {
                JError::raiseNotice(null, 'You want to install an old version of THM Groups: ' . $rel . ' -
                this will not effect your installed version of THM Groups');
            }
        }
        else
        {
            $rel = $this->release;
        }

        if ($type == 'install')
        {
            require_once 'admin/install.php';
        }

        echo '<h1 align="center"><strong>THM Groups ' . strtoupper($type) . '<br/>' . $rel . '</strong></h1>';
    }

    /*
     * Install runs after the database scripts are executed.
     * If the extension is new, the install method is run.
     *
     * @param   $parent  is the class calling this method.
     *
     * @return  if install returns false, Joomla will abort the install and undo everything already done.
     */
    public function install($parent)
    {
        // you can find the usage of old function in /models/db_data_manager
    }

    /*
     * Update runs after the database scripts are executed.
     * If the extension exists, then the update method is run.
     *
     * @param   $parent  is the class calling this method.
     *
     * @return  if this returns false, Joomla will abort the update and undo everything already done.
     */
    public function update($parent)
    {
        $oldRelease = $this->getParam('version');

        // Make global update only for versions less than 3.5.5
        if (version_compare($oldRelease, '3.5.0', 'lt'))
        {
            if (THM_Groups_Update_Script::update())
            {
                return true;
            }

            JFactory::getApplication()->enqueueMessage('update script', 'error');
            return false;
        }
    }

    /*
     * Uninstall runs before any other action is taken (file removal or database processing).
     *
     * @param   $parent  is the class calling this method
     *
     * @return  nothing
     */
    public function uninstall($parent)
    {
        //echo '<p>' . JText::sprintf('COM_THM_GROUPS_DEINSTALL', $this->release) . '</p>';
    }

    /*
     * Postflight is run after the extension is registered in the database.
     *
     * @param   $parent  is the class calling this method.
     * @param   $type    is the type of change (install, update or discover_install, not uninstall).
     *
     */
    public function postflight($type,$parent)
    {
        if ($type == 'update' || $type == 'install')
        {
            $isLibraryEnabled = $this->checkExtension('lib_thm_core');
            if ($isLibraryEnabled != null && $isLibraryEnabled == 1)
            {
                $uri = JURI::root(true) . '/libraries/thm_core/log/THMChangelogColoriser.css';
                echo "<link rel='stylesheet' type='text/css' href='{$uri}' />";
                echo THMChangelogColoriser::colorise(dirname(__FILE__) . '/admin/CHANGELOG.php', true);
            }
            else
            {
                echo 'Changelog will not be shown, because the library lib_thm_core is not installed.';
            }
            echo '<hr>';
        }
    }

    /**
     * Checks if extension is installed
     *
     * @param   $name  is element in database of extension
     *
     * @return  mixed
     */
    public function checkExtension($name)
    {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT enabled FROM #__extensions WHERE element ="' . $name . '"');
        $result = $db->loadObject();
        if ($result != null)
        {
            if (property_exists($result, 'enabled'))
            {
                return $result->enabled;
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * Get a variable from the manifest file (actually, from the manifest cache).
     *
     * @param   String  $name  param what you need, for example version
    */
    public function getParam($name)
    {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_thm_groups"');
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }

    /**
     * Deletes recursively files
     *
     * @param   String  $path  path of admin and site part of component
     *
     * @return void
     */
    private function deleteDir($path)
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