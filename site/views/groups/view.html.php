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