<?php
/**
 * @version     v3.0.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewGroups
 * @description THMGroupsViewGroups file from com_thm_groups
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.view');
jimport('thm_groups.view.lib_thm_groups_listview');

JHTML::_('behavior.mootools');
JHTML::_('behavior.framework', true);

/**
 * THMGroupsViewExtensions class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
*/
class THMGroupsViewList extends JView
{

    /**
     * Method to get extra
     *
     * @param   String  $tpl  template
     *
     * @return void
     *
     * @see JView::display()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function display($tpl = null)
    {
        $task = JRequest::getVar('task');
        $this->$task();
    }


    /**
     * Search a Groups of User for one Letter
     *
     * @return String $result a groups of user for one Letter
     */
    public function getUserAlphabet()
    {
        $gid = JRequest::getInt('gid');
        $letter = JRequest::getVar('letter');
        $column = JRequest::getVar('column');
        $paramLinkTarget = JRequest::getVar('paramLinkTarget');
        $orderAttr = JRequest::getVar('orderAttr');
        $showstructure = JRequest::getVar('showStructure');
        $arrshowstructure = explode(",", $showstructure);
        $linkElement = explode(",", JRequest::getVar('linkElement'));
        $oldattribut = JRequest::getVar('oldattribut');

        echo THMLibThmListview::getUserForLetter($gid, $column, $letter, $paramLinkTarget, $orderAttr, $arrshowstructure, $linkElement, $oldattribut);
    }
}
