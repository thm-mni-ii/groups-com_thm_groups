<?php
/**
 * @version     v3.2.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsModelList
 * @description THMGroupsModelList file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

/**
 * THMGroupsModelList class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelList extends JModelLegacy
{
    // Wegen Nichtverwendung auskommentiert: private $_conf;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method to get view
     *
     * @return view
     */
    public function getView()
    {
        return $this->getHead() . $this->getList();
    }

    /**
     * Method to get view parameters
     *
     * @return params
     */
    public function getViewParams()
    {
        $mainframe = Jfactory::getApplication();
        return $mainframe->getParams();
    }

    /**
     * Method to get group number
     *
     * @return groupid
     */
    public function getGroupNumber()
    {
        $params = $this->getViewParams();
        return $params->get('selGroup');
    }

    /**
     * Method to get show mode
     *
     * @return showmode
     */
    public function getShowMode()
    {
        $params = $this->getViewParams();
        return $params->get('showAll');
    }

    /**
     * Method to get title
     *
     * @return String
     */
    public function getTitle()
    {
        $retString = '';
        $groupid   = $this->getGroupNumber();
        if ($this->getTitleState($groupid))
        {
            $retString .= $this->getTitleGroup($groupid);
        }
        return $retString;
    }
}
