<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  plg_thm_groups_sync
 *@name        PlgSyncTHM_Groups
 *@description PlgSyncTHM_Groups file from plg_thm_groups_sync
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
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

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();

/**
 * Function during installation
 * 
 * @return void
 */
function Com_install()
{

	?>
	<h1 align="center">
		<strong>&nbsp;THM Groups Installer!</strong>
	</h1>
	<?php

	$db =& JFactory::getDBO();

	$query = 'SELECT * FROM #__usergroups';
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
	}
	if ($db->query())
	{
		echo "
			<p align=\"left\">
				<strong>&nbsp;Groups added to database!</strong>
			</p>";
	}
	else
	{
		echo "
			<p align=\"left\">
			<strong>&nbsp;No groups added to database!</strong>
			</p>";
	}
	?>
		<p align="center">
			<strong>&nbsp;Installation successful!</strong>
		</p>
		<?php
}
