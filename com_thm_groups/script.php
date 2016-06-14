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


/**
 * ThmGroupsInstaller
 *
 * @category    Joomla.Component.General
 * @package     thm_groups
 * @subpackage  com_thm_groups
 */
class Com_THM_GroupsInstallerScript
{
	/**
	 * Checks if extension is installed
	 *
	 * @param   $name  is element in database of extension
	 *
	 * @return  mixed
	 */
	public function checkExtension($name)
	{
		$dbo = JFactory::getDbo();
		$dbo->setQuery('SELECT enabled FROM #__extensions WHERE element ="' . $name . '"');
		$result = $dbo->loadObject();

		if ($result != null AND property_exists($result, 'enabled'))
		{
			return $result->enabled;
		}

		return null;
	}

	/**
	 * Copies the default picture from media/com_thm_groups/profile/anonym.jpg to
	 * images/com_thm_groups/profile
	 *
	 * @return True on success
	 * @throws Exception
	 */
	public function copyDefaultPictureToImagesFolder()
	{
		$source      = JPATH_ROOT . '/media/com_thm_groups/images/profile/anonym.jpg';
		$destination = JPATH_ROOT . '/images/com_thm_groups/profile/anonym.jpg';

		if (!file_exists($source))
		{
			JFactory::getApplication()->enqueueMessage("File $source does not exist", 'error');

			return false;
		}
		elseif (!copy($source, $destination))
		{
			JFactory::getApplication()->enqueueMessage("Failed to copy $source", 'error');

			return false;
		}

		return true;
	}

	/**
	 * Creates dynamic type Email
	 *
	 * @return  mixed  integer on success, false otherwise
	 */
	private function createEmailDynamicType()
	{
		$dbo    = JFactory::getDbo();
		$query  = $dbo->getQuery(true);
		$values = array($dbo->quote('Email'), 1);
		$query
			->insert('#__thm_groups_dynamic_type')
			->columns(array('name', 'static_typeID',))
			->values(implode(',', $values));
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

		return $dbo->insertid();
	}

	/**
	 * Creates a folder com_thm_groups/profile
	 *
	 * @return True on success
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
	 * Deletes recursively files
	 *
	 * @param   String $path path of admin and site part of component
	 *
	 * @return void
	 */
	private function deleteDir($path)
	{
		$it    = new RecursiveDirectoryIterator($path);
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

	/**
	 * Searches for dynamic type with title 'email'
	 *
	 * @param   array $dynTypes Array with dynamic type objects
	 *
	 * @return  object on success, null otherwise
	 */
	private function findEmailDynamicType($dynTypes)
	{
		$emailType = null;
		foreach ($dynTypes as $type)
		{
			if ('email' == strtolower($type->name))
			{
				$emailType = $type;
			}
		}

		return $emailType;
	}

	/**
	 * Returns all dynamic types
	 *
	 * @return  mixed  array on success, false otherwise
	 */
	private function getDynamicTypes()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query
			->select('id, name')
			->from('#__thm_groups_dynamic_type');
		$dbo->setQuery($query);

		try
		{
			$dynTypes = $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return $dynTypes;
	}

	/**
	 * Get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param   String $name param what you need, for example version
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
	 * @return  if install returns false, Joomla will abort the install and undo everything already done.
	 */
	public function install($parent)
	{
		if ($this->createImageFolder() && $this->copyDefaultPictureToImagesFolder())
		{
			return true;
		}

		return false;
	}

	/**
	 * Postflight is run after the extension is registered in the database.
	 *
	 * @param   $parent  is the class calling this method.
	 * @param   $type    is the type of change (install, update or discover_install, not uninstall).
	 *
	 */
	public function postflight($type, $parent)
	{
		if ($type == 'update' || $type == 'install')
		{
			Jloader::import("THM_GroupsHelperChangeLog", JPATH_ROOT . "/media/com_thm_groups/helpers/");
			$uri = JURI::root(true) . '/media/com_thm_groups/css/THMChangelogColoriser.css';

			echo "<link rel='stylesheet' type='text/css' href='{$uri}' />";
			echo THM_GroupsHelperChangeLog::colorise(dirname(__FILE__) . '/admin/CHANGELOG.php', true);
			echo '<hr>';
		}
	}

	/**
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
		$this->release   = $parent->get("manifest")->version;
		$this->component = $parent->get("manifest")->name;

		if ($type == 'update' && $this->component == "COM_THM_GROUPS")
		{
			require_once 'admin/update.php';
			$oldRelease = $this->getParam('version');
			$rel        = $oldRelease . ' &rArr; ' . $this->release;

			// For old versions before 3.5.0, deletes some unused files
			if (version_compare($oldRelease, '3.5.0', 'le'))
			{
				$admin = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_thm_groups';
				$site  = JPATH_ROOT . DS . 'components' . DS . 'com_thm_groups';

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

	/**
	 * Rewrites path option by all attributes of the static type PICTURE
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function rewriteDefaultImagePathByAttributes()
	{
		$imagesPath = '/images/com_thm_groups/profile/';
		$imageName  = 'anonym.jpg';
		$dbo        = JFactory::getDbo();
		$query      = $dbo->getQuery(true);

		$query
			->select('attr.id, attr.options')
			->from('#__thm_groups_attribute AS attr')
			->innerJoin('#__thm_groups_dynamic_type AS dt ON dt.id = attr.dynamic_typeID')
			->innerJoin('#__thm_groups_static_type AS st ON st.id = dt.static_typeID')
			->where('st.name = "PICTURE"');

		$dbo->setQuery($query);

		$attrObjects = $dbo->loadObjectList();

		if (!empty($attrObjects))
		{
			foreach ($attrObjects as $attrObject)
			{
				$optObject           = new stdClass();
				$optObject->path     = $imagesPath;
				$optObject->filename = $imageName;
				$optObject->required = false;

				$query = $dbo->getQuery(true);
				$query
					->update('#__thm_groups_attribute')
					->set("`options` = '" . json_encode($optObject) . "'")
					->where('id = ' . $attrObject->id);

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
		}

		return true;
	}

	/**
	 * Rewrites path option by all dynamic types of the static type PICTURE
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function rewriteDefaultImagePathByDynamicTypes()
	{
		$imagesPath = '/images/com_thm_groups/profile/';
		$imageName  = 'anonym.jpg';
		$dbo        = JFactory::getDbo();
		$query      = $dbo->getQuery(true);

		$query
			->select('dt.id, dt.options')
			->from('#__thm_groups_dynamic_type AS dt')
			->innerJoin('#__thm_groups_static_type AS st ON st.id = dt.static_typeID')
			->where('st.name = "PICTURE"');

		$dbo->setQuery($query);

		$dynTypeObjects = $dbo->loadObjectList();

		if (!empty($dynTypeObjects))
		{
			foreach ($dynTypeObjects as $dynTypeObject)
			{
				$optObject           = new stdClass();
				$optObject->path     = $imagesPath;
				$optObject->filename = $imageName;

				$query = $dbo->getQuery(true);
				$query
					->update('#__thm_groups_dynamic_type')
					->set("`options` = '" . json_encode($optObject) . "'")
					->where("id = $dynTypeObject->id");

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
		}

		return true;
	}

	/**
	 * Uninstall runs before any other action is taken (file removal or database processing).
	 *
	 * @param   $parent  is the class calling this method
	 */
	public function uninstall($parent)
	{
		//echo '<p>' . JText::sprintf('COM_THM_GROUPS_DEINSTALL', $this->release) . '</p>';
	}

	/**
	 * Update runs after the database scripts are executed. If the extension exists, then the update method is run.
	 *
	 * @param   $parent  is the class calling this method.
	 *
	 * @return  if this returns false, Joomla will abort the update and undo everything already done.
	 */
	public function update($parent)
	{
		$oldRelease = $this->getParam('version');

		// Make global update only for versions less than 3.5.0
		if (version_compare($oldRelease, '3.5.0', 'lt'))
		{
			if (THM_Groups_Update_Script::update())
			{
				return true;
			}

			JFactory::getApplication()->enqueueMessage('update script', 'error');

			return false;
		}

		$isImageFolderCreated            = $this->createImageFolder();
		$isDefPictureCopied              = $this->copyDefaultPictureToImagesFolder();
		$isDefImgPathByDynTypesRewritten = $this->rewriteDefaultImagePathByDynamicTypes();
		$isDefImgPathByAttrsRewritten    = $this->rewriteDefaultImagePathByAttributes();
		$isAttributeEmailUpdated         = $this->updateEmailAttribute();

		$isOk = ($isImageFolderCreated AND $isDefPictureCopied AND $isDefImgPathByDynTypesRewritten AND $isDefImgPathByAttrsRewritten AND $isAttributeEmailUpdated);

		if ($isOk)
		{
			return true;
		}

		return false;
	}

	/**
	 * Changes dynamic type of the attribute Email
	 *
	 * @return  bool, true on success, otherwise false
	 *
	 * @throws Exception
	 */
	private function updateEmailAttribute()
	{
		$dbo = JFactory::getDbo();
		$dbo->transactionStart();

		$dynTypes = $this->getDynamicTypes();
		if (empty($dynTypes))
		{
			$dbo->transactionRollback();

			return false;
		}

		$emailType = $this->findEmailDynamicType($dynTypes);
		$typeID    = empty($emailType) ? $this->createEmailDynamicType() : $emailType->id;
		if (empty($typeID))
		{
			$dbo->transactionRollback();

			return false;
		}

		$success = $this->updateEmailDynamicType($typeID);
		if (!$success)
		{
			$dbo->transactionRollback();

			return false;
		}

		$dbo->transactionCommit();

		return true;
	}

	/**
	 * Updates dynamic type of the attribute Email
	 *
	 * @param   int $id ID of a dynamic type with the title Emails
	 *
	 * @return  boolean  true on success, false otherwise
	 */
	private function updateEmailDynamicType($id)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query
			->update('#__thm_groups_attribute')
			->set("dynamic_TypeID = $id")
			->where('id = 4');
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

		return true;
	}
}