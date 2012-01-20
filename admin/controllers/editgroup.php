<?php

/**
* This file contains the data type class Image.
*
* PHP version 5
*
* @category Web Programming Weeks SS / WS 2011: THM Gießen
* @package  com_thm_groups
* @author   Markus Kaiser <markus.kaiser@mni.thm.de>
* @author   Daniel Bellof <daniel.bellof@mni.thm.de>
* @author   Jacek Sokalla <jacek.sokalla@mni.thm.de>
* @author   Peter May <peter.may@mni.thm.de>
* @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* @link     http://www.mni.thm.de
**/

require_once(JPATH_COMPONENT.DS.'classes'.DS.'confdb.php');
 jimport('joomla.application.component.controllerform');
class THMGroupsControllerEditgroup extends JControllerForm {

/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
		$this->registerTask('apply', 'apply');
		$this->registerTask('save2new', 'save2new');
	}

	/**
  	 * display the edit form
 	 * @return void
 	 */

	function edit(){
    	JRequest::setVar( 'view', 'editgroup' );
    	JRequest::setVar( 'layout', 'default' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}
	
    function apply(){
    	$model = $this->getModel('editgroup');
		$id = JRequest::getVar('gid');
    	
		if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}
    	$this->setRedirect( 'index.php?option=com_thm_groups&task=editgroup.edit&cid[]='.$id,$msg );
    }
	/**
 	 * save a record (and redirect to view=structure)
 	 * @return void
 	 */
	function save() {
    	$model = $this->getModel('editgroup');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&view=groupmanager',$msg );
	}
	
	function save2new() {
		$model = $this->getModel('editgroup');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&view=addgroup',$msg );
	}


	/**
 	 * cancel editing a record
 	 * @return void
 	 */
	function cancel(){
	    $msg =   JText::_( 'CANCEL' );
	    $this->setRedirect(   'index.php?option=com_thm_groups&view=groupmanager', $msg );
	}
	
	public function delPic() {
		
		$model = $this->getModel('editgroup');
    	$id = JRequest::getVar('gid');

    	if ($model->delPic()) {
    	    $msg = JText::_( 'Bild entfernt' );
    	} else {
    	    $msg = JText::_( 'Bild konnte nicht entfernt werden' );
    	}
		//$this->apply();
    	$this->setRedirect( 'index.php?option=com_thm_groups&task=editgroup.edit&cid[]='.$id,$msg );
	}
}
?>