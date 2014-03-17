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
            $oldRelease = $this->getParam('version');
            $rel = $oldRelease . ' &rArr; ' . $this->release;

            // For old versions before 3.4.3, deletes some unused files
            if(version_compare($oldRelease, '3.4.3', 'le'))
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
                Jerror::raiseWarning(null, JText::_('COM_THM_GROUPS_UPDATE_ERROR_VERSION') . $rel);
                return false;
            }
        }
        else
        {
            $rel = $this->release;
        }

        echo '<h1 align="center"><strong>' . JText::_('COM_THM_GROUPS_PREFLIGHT_' . strtoupper($type)) . '<br/>' . $rel . '</strong></h1>';
        echo '<br/><h3 align="center"><a href="http://www.google.de">Release notes</a></h3>';
        echo '<hr>';
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

    }

    /*
     * Get a variable from the manifest file (actually, from the manifest cache).
     *
     * @param   String  $name  param what you need, for example version
    */
    function getParam($name)
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
    public function deleteDir($path)
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