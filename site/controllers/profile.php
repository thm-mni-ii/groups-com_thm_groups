<?php
/**
 * PHP version 5
 *
 * @package  com_thm_groups
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class THMGroupsControllerProfile extends JController {

	var $uid = null;
	var $uname = null;
	/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
		$this->registerTask('backToRefUrl', '');
	}

	function getLink() {
		$itemid = $itemid = JRequest :: getVar('Itemid', 0);
		$id = JRequest :: getVar('id',0);
		$userInfo['lastName'] = JRequest :: getVar('lastName',0);
		$letter=strtoupper(substr($userInfo['lastName'], 0, 1));
		$db =& JFactory::getDBO();
		$query = "SELECT link FROM `#__menu` where id= $itemid";
		$db->setQuery( $query );
		$item = $db->loadObject();
		$link = substr($item->link . "&Itemid=" . $itemid, 0, strlen($item->link . "&Itemid=" . $itemid));
		return $link . "&/$id-". $userInfo['lastName'] ."&letter=$letter";
	}

	
	public function backToRefUrl(){

		
		$option_old = JRequest :: getVar('option_old', 0);
		$layout_old = JRequest :: getVar('layout_old', 0);
		$view_old = JRequest :: getVar('view_old', 0);
		$itemid_old = JRequest :: getVar('item_id',0);
		
		
    	$link = JRoute::_('index.php?option='.$option_old.'&view='.$view_old.'&layout='.$layout_old.'&Itemid='.$itemid_old);
  		
  		$this->setRedirect($link);
	}	
}

?>
