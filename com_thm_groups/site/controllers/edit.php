<?php
/**
 * @version     v3.2.7
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsControllerEdit
 * @description THMGroups component site edit controller
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

/**
 * Site edit controller class for component com_thm_groups
 *
 * Edit controller for the site section of the component
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerEdit extends JController
{
    /**
     * UserID
     *
     * @var    integer
     * @since  1.0
     */
    public $uid = null;

    /**
     * UserName
     *
     * @var    string
     * @since  1.0
     */
    public $uname = null;

    /**
     *  Constructor (registers additional tasks to methods)
     *@since  Method available since Release 2.0
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('delPic', '');
        $this->registerTask('backToRefUrl', '');
        $this->registerTask('apply', '');
        $this->registerTask('addTableRow', '');
        $this->registerTask('save', '');
    }

    /**
     *  Method to get the link, where the redirect has to go
     *@since  Method available since Release 2.0
     *
     *@return   string  link.
     */
    public function getLink()
    {
        $model = $this->getModel('edit');
        return $model->getLink();
    }

    /**
     *  Method to save a record
     *@since  Method available since Release 1.0
     *
     *@return   string  link.
     */
    public function save()
    {
        $this->uid   = JRequest::getVar('userid', 0);
        $this->uname = JRequest::getVar('name', 0);
        $gsgid 		 = JRequest::getVar('gsgid', 1);
        $option_back  = JRequest::getVar('option_back', 0);
        $layout_back  = JRequest::getVar('layout_back', 0);
        $view_back    = JRequest::getVar('view_back', 0);
        $itemid_back  = JRequest::getVar('item_id', 0);

        /*$this->assignRef('userid', $cid);
        $this->assignRef('structure', $structure);
        $this->assignRef('gsgid', $gsgid);*/

        $model = $this->getModel('edit');
        $msg = JText::_('COM_THM_GROUPS_PROFILE_SAVED');
        $link =& JURI::getInstance('index.php'
                . '?option=' . $option_back
                . '&view=' . $view_back
                . '&layout=' . $layout_back
                . '&Itemid=' . $itemid_back
                . '&gsuid=' . $this->uid
                . '&name=' . $this->uname
                . '&gsgid=' . $gsgid
        );
        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }
        $this->setRedirect(JRoute::_($link, false), $msg);
    }

    /**
     *  Method to delete a picture
     *@since  Method available since Release 2.0
     *
     *@return   void
     */
    public function delPic()
    {
        $model = $this->getModel('edit');

        if ($model->delPic())
        {
            $msg = JText::_('COM_THM_GROUPS_PICTURE_REMOVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_REMOVE_PICTURE_ERROR');
        }
        $this->apply();
    }

    /**
     *  Method to add a table row
     *@since  Method available since Release 2.0
     *
     *@return   void
     */
    public function addTableRow()
    {
        $model = $this->getModel('edit');
        if ($model->addTableRow())
        {
            $msg = JText::_('COM_THM_GROUPS_ROW_TO_TABLE');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_ROW_TO_TABLE_ERROR');
        }
        $this->apply();
    }

    /**
     *  Method to delete a table row
     *@since  Method available since Release 2.0
     *
     *@return   void
     */
    public function delTableRow()
    {
        $model = $this->getModel('edit');

        if ($model->delTableRow())
        {
            $msg = JText::_('COM_THM_GROUPS_DEL_TABLE_ROW');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DEL_TABLE_ROW_ERROR');
        }
        $this->apply();
    }

    /**
     *  Method to edit a table row
     *@since  Method available since Release 2.0
     *
     *@return   void
     */
    public function editTableRow()
    {
        $model = $this->getModel('edit');

        if ($model->editTableRow())
        {
            $msg = JText::_('COM_THM_GROUPS_EDIT_TABLE_ROW');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_EDIT_TABLE_ROW_ERROR');
        }
        $this->apply();
    }

    /**
     *  Method, which sets the redirect for the 'back' button.
     *@since  Method available since Release 2.1
     *
     *@return   void
     */
    public function backToRefUrl()
    {
        $this->uid   = JRequest::getVar('userid', 0);
        $this->uname = JRequest::getVar('name', 0);
        $gsgid 		 = JRequest::getVar('gsgid', 1);
        $option_back  = JRequest::getVar('option_back', 0);
        $layout_back  = JRequest::getVar('layout_back', 0);
        $view_back    = JRequest::getVar('view_back', 0);
        $itemid_back  = JRequest::getVar('item_id', 0);

        $msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
        $link =& JURI::getInstance('index.php'
            . '?option=' . $option_back
            . '&view=' . $view_back
            . '&layout=' . $layout_back
            . '&Itemid=' . $itemid_back
            . '&gsuid=' . $this->uid
            . '&name=' . $this->uname
            . '&gsgid=' . $gsgid
        );

        $backRef = JRequest::getVar('backRef', 0);
        $uri =& JURI::getInstance(html_entity_decode($backRef));
        $backRef = $uri->toString();
        $this->setRedirect(JRoute::_($link->toString(), false), $msg);
    }

    /**
     *  Method, which sets the redirect for the 'back' button.
     *@since  Method available since Release 2.1
     *
     *@return   void
     */
    public function apply()
    {
        $this->uid 	 = JRequest::getVar('userid', 0);
        $this->uname = JRequest::getVar('name', 0);
        $gsgid 		 = JRequest::getVar('gsgid', 1, 'get', 'INTEGER');
        $layout_back  = JRequest::getVar('layout_back', 0);
        $view_back 	 = JRequest::getVar('view_back', 0);
        $option_back  = JRequest::getVar('option_back', 0);


        $model = $this->getModel('edit');
        $itemid = JRequest::getVar('item_id', 0);
        $link =& JURI::getInstance("index.php?option=com_thm_groups"
            . "&view=edit"
            . "&layout=default"
            . "&Itemid=" . $itemid
            . "&gsuid=" . $this->uid
            . "&name=" . $this->uname
            . "&gsgid=" . $gsgid
            . "&layout_back=" . $layout_back
            . "&view_back=" . $view_back
            . "&option_back=" . $option_back
        );
        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }
        $this->setRedirect(JRoute::_($link->toString(), false), $msg);
    }
}
