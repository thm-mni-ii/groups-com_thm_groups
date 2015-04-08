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


/**
 * THMGroupsViewExtensions class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
*/
class THM_GroupsViewList extends JViewLegacy
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
        $app = JFactory::getApplication()->input;
        $task = $app->get('task');
        $this->$task();
    }


    /**
     * Search a Groups of User for one Letter
     *
     * @return String $result a groups of user for one Letter
     */
    public function getUserAlphabet()
    {
        $app = JFactory::getApplication()->input;
        $gid = $app->get('gid');
        $letter = $app->getString('letter');
        $column = $app->getString('column');
        $paramLinkTarget = $app->getString('paramLinkTarget');
        $orderAttr = $app->getString('orderAttr');
        $showstructure = $app->getString('showStructure');
        $arrshowstructure = explode(",", $showstructure);
        $linkElement = explode(",", $app->getString('linkElement'));
        $oldattribut = $app->getString('oldattribut');


        echo THMLibThmListview::getUserForLetter($gid, $column, $letter, $paramLinkTarget, $orderAttr, $arrshowstructure, $linkElement, $oldattribut);
    }
}
