<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_thm_groups
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rêne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();
require_once(JPATH_COMPONENT.DS.'classes'.DS.'confdb.php');
 jimport('joomla.application.component.controllerform');
class THMGroupsControllerGroupmanager extends JControllerForm {

	/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
	}

	/**
  	 * display the groupedit form
 	 * @return void
 	 */
	function edit(){
    	JRequest::setVar( 'view', 'editgroup' );
    	JRequest::setVar( 'layout', 'default' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}

	function cancel(){
	    $msg =   JText::_( 'Operation Cancelled' );
	    $this->setRedirect(   'index.php?option=com_thm_groups', $msg );
	}

	function addGroup(){
		JRequest::setVar( 'view', 'addgroup' );
    	JRequest::setVar( 'layout', 'default' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}
	//If-Else-Verzweigung Abfrage nach aktiven Usern noch wichtig.
	function remove(){
	    $db =& JFactory::getDBO();
    	$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );

    	$model = $this->getModel();

    	$freeGroups=$model->getfreeGroups();

    	$deleted = 0;
    	foreach($cid as $toDel){
    		foreach($freeGroups as $canDel){
    			if($toDel==$canDel->id && $canDel->injoomla==0) {
    				$model->delGroup($toDel);
    				$deleted++;
    			}
    		}
    	}

    	$delCount = count($cid);
    	switch ($delCount) {
    		case 0:
    			$answer = "";
    			break;
    		case 1:
    			if ($deleted == 1) {
    				$answer = "Gruppe erfolgreich entfernt";
    			} else {
    				$answer = "Gruppe konnte nicht entfernt werden";
    			}
    			break;
    		default:
    			if ($deleted == 0) {
    				$answer = "Keine Gruppe konnte entfernt werden";
    			} else if ($deleted == $delCount) {
    				$answer = "Alle Gruppen wurden erfolgreich entfernt";
    			} else {
    				$answer = "Nicht alle Gruppen konnten entfernt werden";
    			}
    			break;
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&view=groupmanager', $answer);
	}
}
?>