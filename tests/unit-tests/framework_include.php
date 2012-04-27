<?php
if(isset($user)) die('Restricted access');
define('_JEXEC', 1);
define('JPATH_BASE','../../../');
define('DS', DIRECTORY_SEPARATOR);
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
$user = JFactory::getUser();
?>