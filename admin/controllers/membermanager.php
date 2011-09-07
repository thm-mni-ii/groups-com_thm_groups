<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_thm_groups
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @author	 Ali Kader Caliskan <ali.kader.caliskan@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();


// Database
require_once(JPATH_COMPONENT.DS.'classes'.DS.'confdb.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SQLAbstractionLayer.php');
 jimport('joomla.application.component.controller');

class THMGroupsControllermembermanager extends JController {


	/**
	 * Database object
	 * @var unknown_type
	 */
	private $SQLAL = null;


	/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
		$this->registerTask('add', 'edit');
		$this->registerTask('setGroupsAndRoles', '');
		$this->registerTask('delGroupsAndRoles', '');
		$this->registerTask('publish', '');
		$this->registerTask('unpublish', '');
		$this->registerTask('deleteList', '');
		$this->registerTask('uploadPic', '');
		$this->registerTask('delPic', '');
		$this->registerTask('delGrouproleByUser', '');
		$this->registerTask('delAllGrouprolesByUser', '');

		// Create database object
		$this->SQLAL = new SQLAbstractionLayer();
	}


	/**
  	 * display the edit form
 	 * @return void
 	 */
	function edit(){
    	JRequest::setVar( 'view', 'edit' );
    	JRequest::setVar( 'layout', 'forms' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}

    function apply(){
    	$model = $this->getModel('edit');
    	$id = JRequest::getVar('userid');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&task=membermanager.edit&cid[]='.$id,$msg );
    }
	/**
 	 * save a record (and redirect to view=membermanager)
 	 * @return void
 	 */
	function save() {
    	$model = $this->getModel('edit');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$link = 'index.php?option=com_thm_groups&view=membermanager';
    	$this->setRedirect($link, $msg);
	}


	/**
 	 * cancel editing a record
 	 * @return void
 	 */
	function cancel(){
	    $msg =   JText::_( 'Operation Cancelled' );
	    $this->setRedirect(   'index.php?option=com_thm_groups&view=membermanager', $msg );
	}
	
	public function delPic() {
		
		$model = $this->getModel('edit');
    	$id = JRequest::getVar('userid');

    	if ($model->delPic()) {
    	    $msg = JText::_( 'Bild entfernt' );
    	} else {
    	    $msg = JText::_( 'Bild konnte nicht entfernt werden' );
    	}
		$this->apply();
    	$this->setRedirect( 'index.php?option=com_thm_groups&task=membermanager.edit&cid[]='.$id,$msg );
	}
	
	public function addTableRow() {
		$model = $this->getModel('edit');
    	$id = JRequest::getVar('userid');
    	
    	if ($model->addTableRow()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_ROW_TO_TABLE' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_ROW_TO_TABLE_ERROR' );
    	}
		$this->apply();
	}
	
	public function delTableRow() {
		$model = $this->getModel('edit');
    	$id = JRequest::getVar('userid');
    	
    	if ($model->delTableRow()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_DEL_TABLE_ROW' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_DEL_TABLE_ROW_ERROR' );
    	}
		$this->apply();
	}
	
	public function editTableRow() {
		$model = $this->getModel('edit');
    	$id = JRequest::getVar('userid');
    	
    	if ($model->editTableRow()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_EDIT_TABLE_ROW' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_EDIT_TABLE_ROW_ERROR' );
    	}
		
    	$this->apply();	
	}
	
	/**
	 * Sets group and role parameters.
	 *
	 * This function gets group and role parameters from HTML-request/-view and calls
	 * the corresponding model function to set group and role parameters in the database.
	 *
	 * @access  public
	 */
	public function setGroupsAndRoles() {

		// Get group-id
    	$gid = JRequest::getVar( 'groups');

    	// Get role-id
    	$rids  = JRequest::getVar( 'roles');

    	// Get user ids
    	$uids = JRequest::getVar('cid', array(), 'post', 'array');

		foreach($rids as $rid)
    	// Add group and role relations and display result
    	if($this->SQLAL->setGroupsAndRoles($uids, $gid, $rid)) {
    		$msg = JText::_( 'Benutzer zu Gruppe/Rollen hinzugefuegt!');
    	} else {
    		$msg = JText::_( 'Benutzer zu Gruppe/Rollen hinzufuegen fehlgeschlagen!');
    	}

        $this->setRedirect( 'index.php?option=com_thm_groups&view=membermanager',$msg );
	}


	/**
	 * Deletes group and role parameters.
	 *
	 * This function gets group and role parameters from HTML-request/-view and calls
	 * the corresponding model function to delete group and role parameters in the database.
	 *
	 * @access  public
	 */
	public function delGroupsAndRoles() {

		// Get group-id
    	$gid = JRequest::getVar( 'groups');
    	$rids = JRequest::getVar( 'roles');
    	// Get user ids
    	$uids = JRequest::getVar('cid', array(), 'post', 'array');

		foreach($rids as $rid) {
			// Delete group and role relations and display result
			if(1 == $gid) {
	    		$msg = JText::_('Benutzer kann nicht aus Gruppe User entfernt werden!', true);
			} else {
				if($this->SQLAL->delGroupsAndRoles($uids, $gid, $rid)) {
	    			$msg = JText::_( 'Benutzer aus Gruppe entfernt!', true);
	    		} else {
	    			$msg = JText::_('Benutzer aus Gruppe/Rolle entfernen fehlgeschlagen!', true);
	    		}
			}
		}

        $this->setRedirect( 'index.php?option=com_thm_groups&view=membermanager',$msg );
	}


	/**
 	 * set user unpublished (and redirect to view=membermanager)
 	 * @return void
 	 */
	function publish() {
    	$db =& JFactory::getDBO();
    	$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );
    	JArrayHelper::toInteger( $cid );
    	$cids = implode( ',', $cid );

    	$query = 'UPDATE #__thm_groups_additional_userdata'
           . ' SET published = 1'
           . ' WHERE userid IN ( '. $cids .' )';

        $db->setQuery( $query );
        $db->query();

        $msg =   JText::_( 'Benutzer published');
        $this->setRedirect( 'index.php?option=com_thm_groups&view=membermanager',$msg );

	}

	/**
 	 * set user unpublished (and redirect to view=membermanager)
 	 * @return void
 	 */
	function unpublish() {
		$db =& JFactory::getDBO();
    	$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );
    	JArrayHelper::toInteger( $cid );
    	$cids = implode(   ',', $cid );

    	$query = 'UPDATE #__thm_groups_additional_userdata'
           . ' SET published = 0'
           . ' WHERE userid IN ( '. $cids .' )';

        $db->setQuery( $query );
        $db->query();

        $msg =   JText::_( 'Benutzer unpublished' );
        $this->setRedirect( 'index.php?option=com_thm_groups&view=membermanager',$msg );
	}


	function delete(){

	    $db =& JFactory::getDBO();
    	$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );
    	JArrayHelper::toInteger( $cid );
    	$cids = implode(   ',', $cid );
		
  		foreach($cid as $id) {
    		$query = 'SELECT injoomla FROM #__thm_groups_additional_userdata'
               		. ' WHERE userid= '. $id;
			
        	$db->setQuery( $query );
        	$erg = $db->loadObjectList();
//var_dump($erg);

    		if($erg[0]->injoomla == '0'){

		    	$query = 'DELETE FROM #__thm_groups_date'
		               . ' WHERE userid = '.$id.';';
		        $db->setQuery( $query );
		        $db->query();
		        
		        $query = 'DELETE FROM #__thm_groups_number'
		               . ' WHERE userid = '.$id.';';
		        $db->setQuery( $query );
		        $db->query();
		        
		        $query = 'DELETE FROM #__thm_groups_picture'
		               . ' WHERE userid = '.$id.';';
		        $db->setQuery( $query );
		        $db->query();
		        
		        $query = 'DELETE FROM #__thm_groups_table'
		               . ' WHERE userid = '.$id.';';
		        $db->setQuery( $query );
		        $db->query();
		        
		        $query = 'DELETE FROM #__thm_groups_text'
		               . ' WHERE userid = '.$id.';';
		        $db->setQuery( $query );
		        $db->query();
		        
		        $query = 'DELETE FROM #__thm_groups_textfield'
		               . ' WHERE userid = '.$id.';';
		        $db->setQuery( $query );
		        $db->query();

		        $query = 'DELETE FROM #__thm_groups_groups_map'
		               . ' WHERE uid = '.$id.';';

		        $db->setQuery( $query );
		        $db->query();

		        $msg =   JText::_( 'Benutzer geloescht' );
		        
  			}
  			if($erg[0]->injoomla == '1'){
	  			$msg =   JText::_( 'Eventuell konnten einige Benutzer nicht gel&ouml;scht werden, da sie noch im System registriert sind.' );
  			
  			}

  	  	}
  	  	$this->setRedirect( 'index.php?option=com_thm_groups&view=membermanager',$msg );
	}

	function delAllGrouprolesByUser() {
		$SQLAL = new SQLAbstractionLayer;
		
    	$gid = JRequest::getVar( 'g_id' );
    	$uid = array();
    	$uid[0] = JRequest::getVar( 'u_id' );
		$rids = $this->SQLAL->getGroupRolesByUser($uid[0], $gid);

		// Delete group and role relations and display result
		if(1 == $gid) {
    		$msg = JText::_('Benutzer kann nicht aus Gruppe User entfernt werden!', true);
		} else {
			foreach($rids as $rid) {
				if($this->SQLAL->delGroupsAndRoles($uid, $gid, $rid->rid)) {
	    			$msg = JText::_( 'Benutzer aus Gruppe entfernt!', true);
	    		} else {
	    			$msg = JText::_('Benutzer aus Gruppe/Rolle entfernen fehlgeschlagen!', true);
	    		}
			}
		}
		
        $this->setRedirect( 'index.php?option=com_thm_groups&view=membermanager',$msg );
	}
	
	function delGrouproleByUser() {
		
    	$gid = JRequest::getVar( 'g_id' );
    	$uid = array();
    	$uid[0] = JRequest::getVar( 'u_id' );
    	$rid = JRequest::getVar( 'r_id' );

		if(1 == $gid) {
    		$msg = JText::_('Benutzer kann nicht aus Gruppe User entfernt werden!', true);
		} else {
			if($this->SQLAL->delGroupsAndRoles($uid, $gid, $rid)) {
    			$msg = JText::_( 'Benutzerrolle aus Gruppe entfernt!', true);
    		} else {
    			$msg = JText::_('Benutzerrolle aus Gruppe entfernen fehlgeschlagen!', true);
    		}
		}
        $this->setRedirect( 'index.php?option=com_thm_groups&view=membermanager',$msg );
	}
}

?>