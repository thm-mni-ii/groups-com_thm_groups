<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.path');
// Include database class
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SQLAbstractionLayer.php');

class THMGroupsViewmembermanager extends JView {

	protected $state;

	function display($tpl = null) {
		$SQLAL = new SQLAbstractionLayer;
		$document   = & JFactory::getDocument();
		$document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");

		//ToDo: letzte zwei boolean-Parameter anpassen, wenn die Seite steht
		//http://www.joomla-tipps.net/joomla1.5/dd/d6e/classJToolBarHelper.html#e2f503bbb227354b6d0ed3d2d8547cc5

		JToolBarHelper::title( JText::_( 'COM_THM_GROUPS_MEMBERMANAGER_TITLE' ), 'membermanager.png', JPATH_COMPONENT.DS.'img'.DS.'membermanager.png' );

		JToolBarHelper::custom( 'membermanager.setGroupsAndRoles', 'moderate.png',   JPATH_COMPONENT.DS.'img'.DS.'moderate.png',   'COM_THM_GROUPS_MEMBERMANAGER_ADD', true, true );
		JToolBarHelper::custom( 'membermanager.delGroupsAndRoles', 'unmoderate.png', JPATH_COMPONENT.DS.'img'.DS.'unmoderate.png', 'COM_THM_GROUPS_MEMBERMANAGER_DELETE',  true, true );
		JToolBarHelper::deleteList('Wirklich l&ouml;schen?','membermanager.delete', 'JTOOLBAR_DELETE');
		JToolBarHelper::publishList('membermanager.publish', 'COM_THM_GROUPS_MEMBERMANAGER_PUBLISH');
		JToolBarHelper::unpublishList('membermanager.unpublish', 'COM_THM_GROUPS_MEMBERMANAGER_DISABLE');
		JToolBarHelper::cancel('membermanager.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::editListX('membermanager.edit', 'COM_THM_GROUPS_MEMBERMANAGER_EDIT');
		JToolBarHelper::back('JTOOLBAR_BACK');


		/* Joomla 1.5
		//global $mainframe, $option;
		*/

 		// begin Joomla 1.6
 		$mainframe = Jfactory::getApplication('Administrator');
 		// end Joomla 1.6
		$db  		= & JFactory::getDBO();

		$this->state		= $this->get('State');

		$search 			= $mainframe->getUserStateFromRequest( "com_thm_groups.search", 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$model =& $this->getModel();
		//$model->sync();

		// Get data from the model
		$items = $this->get( 'Items');
		$pagination = $this->get('Pagination');
		$groupOptions = $model->getGroupSelectOptions();

		$groups = $SQLAL->getGroups();
		$roles = $SQLAL->getRoles();

		//search filter
		$filters = array();
		$filters[] = JHTML::_('select.option', '1', JText::_( 'Nachname' ) );
		$filters[] = JHTML::_('select.option', '2', JText::_( 'Vorname' ) );
		$filters[] = JHTML::_('select.option', '3', JText::_( 'Benutzername' ) );
		if(isset($lists['filter']))
			$lists['filter'] = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $_POST['filter'] );

		if (!isset($_POST['groupFilters'])) {
			$_POST['groupFilters'] = null;
		}
		if (!isset($_POST['rolesFilters'])) {
			$_POST['rolesFilters'] = null;
		}

		//group filter
		$groupFilters = array();
		$groupFilters[] = JHTML::_('select.option', 0, JText::_( 'Alle' ) );

		foreach($groupOptions as $option){
			$groupFilters[] = $option;
		}
		$lists['groups'] = JHTML::_('select.genericlist', $groupFilters, 'groupFilters', 'size="1" class="inputbox"', 'value', 'text', $_POST['groupFilters'] );


		//roles filter
		$rolesFilters = array();
		$rolesFilters[] = JHTML::_('select.option', 0, JText::_( 'Alle' ) );
		foreach($roles as $role){
			$rolesFilters[] = JHTML::_('select.option', $role->id, $role->name  );
		}
		$lists['roles'] = JHTML::_('select.genericlist', $rolesFilters, 'rolesFilters', 'size="1" class="inputbox"', 'value', 'text', $_POST['rolesFilters'] );
		$checked = "checked='checked'";
		$grcheck = 1;

		if((JRequest::getVar('grcheck') != 'on') && (JRequest::getVar('grchecked') == 'off')){
			$checked = "";
			$grcheck = 0;
		}


		$lists['groupsrolesoption'] = "<input type='checkbox' name='grcheck' $checked title='Nur ausgew&auml;hlte Gruppe/Rolle anzeigen'/>";


		// search filter
		$lists['search']= $search;

		//assign data to template
		$this->assignRef( 'items', $items );
		$this->assignRef('pagination', $pagination);
		$this->assignRef('lists', $lists);
		$this->assignRef('groups',$groups);
		$this->assignRef('groupOptions',$groupOptions);
		$this->assignRef('roles',$roles);
		$this->assignRef('rolesFilters', $_POST['rolesFilters']);
		$this->assignRef('groupFilters', $_POST['groupFilters']);
		$this->assignRef('grcheckon', $grcheck);

		parent::display($tpl);
	}
}
?>
