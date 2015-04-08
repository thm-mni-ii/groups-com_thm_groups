<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewEditgroup
 * @description THMGroupsViewEditgroup file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewEditgroup class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewEditgroup extends JViewLegacy
{

    protected $form;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        // $model =& $this->getModel();
        $item =& $this->get('Data');
        $this->assignRef('item', $item);

        $groups =& $this->get('AllGroups');
        $this->assignRef('groups', $groups);

        $parent_id =& $this->get('ParentId');
        $this->assignRef('item_parent_id', $parent_id);

        $this->form = $this->get('Form');
        $info = array();
        $info['groupinfo'] = $item[0]->info;

        if (!empty($info))
        {
            $this->form->bind($info);
        }

        /* ZURÜCK BUTTON */
        $option_back = JRequest::getVar('option_back');
        $layout_back = JRequest::getVar('layout_back');
        $view_back = JRequest::getVar('view_back');

        $this->assignRef('option_back', $option_back);
        $this->assignRef('layout_back', $layout_back);
        $this->assignRef('view_back', $view_back);
        /* ###########   */

        parent::display($tpl);
    }
}
