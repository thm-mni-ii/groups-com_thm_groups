<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.general
 * @name        Script
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * ThmGroupsInstaller
 *
 * @category    Joomla.Component.General
 * @package     THM_Groups
 * @subpackage  com_thm_groups
 */
class Com_THM_GroupsInstallerScript
{
	/**
	 * Creates a folder com_thm_groups/profile
	 *
	 * @return True on success
	 *
	 * @throws Exception
	 */
	public function createImageFolder()
	{
		$imagesPath  = JPATH_ROOT . '/images';
		$dirToCreate = $imagesPath . '/com_thm_groups/profile';

		if (!file_exists($dirToCreate) AND !mkdir($dirToCreate, 0755, true))
		{
			JFactory::getApplication()->enqueueMessage("Failed to create a new Folder $dirToCreate", 'error');

			return false;
		}

		return true;
	}

	/**
	 * Get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param   string $name param what you need, for example version
	 */
	public function getParam($name)
	{
		$dbo = JFactory::getDbo();
		$dbo->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_thm_groups"');
		$manifest = json_decode($dbo->loadResult(), true);

		return $manifest[$name];
	}

	/**
	 * Install runs after the database scripts are executed. If the extension is new, the install method is run.
	 *
	 * @param   $parent  is the class calling this method.
	 *
	 * @return  return if the installation succeeded, otherwise false.
	 */
	public function install($parent)
	{
		return $this->createImageFolder();
	}

	/**
	 * Preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 *
	 * @param   $parent  is the class calling this method.
	 * @param   $type    is the type of change (install, update or discover_install, not uninstall).
	 *
	 * @return  void removes previously saved files and outputs version information
	 */
	public function preflight($type, $parent)
	{
		echo '<hr>';

		// Installing component manifest file version
		$manifestVersion = $parent->get("manifest")->version;

		if ($type == 'update')
		{
			$rel = $this->getParam('version') . ' &rArr; ' . $manifestVersion;

			$adminFiles = JFolder::files(JPATH_ADMINISTRATOR . '/components/com_thm_groups');

			foreach ($adminFiles as $adminFile)
			{
				JFile::delete(JPATH_ADMINISTRATOR . '/components/com_thm_groups/' . $adminFile);
			}

			$adminFolders = JFolder::folders(JPATH_ADMINISTRATOR . '/components/com_thm_groups');

			foreach ($adminFolders as $adminFolder)
			{
				JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_thm_groups/' . $adminFolder);
			}

			$siteFiles = JFolder::files(JPATH_SITE . '/components/com_thm_groups');

			foreach ($siteFiles as $siteFile)
			{
				JFile::delete(JPATH_SITE . '/components/com_thm_groups/' . $siteFile);
			}

			$siteFolders = JFolder::folders(JPATH_SITE . '/components/com_thm_groups');

			foreach ($siteFolders as $siteFolder)
			{
				JFolder::delete(JPATH_SITE . '/components/com_thm_groups/' . $siteFolder);
			}

			$mediaFiles = JFolder::files(JPATH_SITE . '/media/com_thm_groups');

			foreach ($mediaFiles as $mediaFile)
			{
				JFile::delete(JPATH_SITE . '/media/com_thm_groups/' . $mediaFile);
			}

			$mediaFolders = JFolder::folders(JPATH_SITE . '/media/com_thm_groups');

			foreach ($mediaFolders as $mediaFolder)
			{
				JFolder::delete(JPATH_SITE . '/media/com_thm_groups/' . $mediaFolder);
			}
		}
		elseif ($type == 'install')
		{
			require_once 'admin/install.php';
			$rel = $manifestVersion;
		}

		echo '<h1 align="center"><strong>THM Groups ' . strtoupper($type) . '<br/>' . $rel . '</strong></h1>';
	}
}
