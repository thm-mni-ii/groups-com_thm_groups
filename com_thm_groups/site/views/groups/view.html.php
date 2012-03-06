<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/

jimport( 'joomla.application.component.view');

class THMGroupsViewGroups extends JView {
	function display($tpl = null) {
        $model =& $this->getModel();
        $groups = $model->getGroups();
        $itemid = JRequest :: getVar('Itemid', 0);
        $this->assignRef( 'groups', $groups );
        $this->assignRef( 'itemid',  $itemid);
		$this->assignRef( 'canEdit',  $model->canEdit());
        parent::display($tpl);
	}
}
?>