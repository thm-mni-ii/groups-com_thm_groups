<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsControllerEditGroup
 * @description THMGroups component site edit group controller
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
jimport('joomla.application.component.controller');

/**
 * Site edit group controller class for component com_thm_groups
 *
 * EditGroup controller for the site section of the component
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.1
 */
class THM_GroupsControllerEditGroup extends JControllerLegacy
{
    /**
     *  Constructor (registers additional tasks to methods)
     *@since  Method available since Release 2.1
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('save', '');
        $this->registerTask('delPic', '');
        $this->registerTask('backToRefUrl', '');
    }

    /**
     *  Method to save a record
     *@since  Method available since Release 2.1
     *
     *@return   string  link.
     */
    public function save()
    {
        $model = $this->getModel('editgroup');

        $itemid     = JRequest::getInt('Itemid');

        /* $option     = JRequest::getVar('option');
        * $view       = JRequest::getVar('view');
        $layout     = JRequest::getVar('layout'); */
        $gsgid      = JRequest::getInt('gsgid');
        $layout_back = JRequest::getVar('layout_back', /*0*/'LLLL');
        $view_back   = JRequest::getVar('view_back', /*0*/'VVVV');

        $link = JRoute::_("index.php?option=com_thm_groups"
                            . "&view=editgroup"
                            . "&layout=default&Itemid=" . $itemid
                            . "&gsgid=" . $gsgid
                            . "&layout_back=" . $layout_back
                            . "&view_back=" . $view_back
                        );

        if ($model->store())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
        }
        $this->setRedirect($link, $msg);
    }

    /**
     *  Method to delete a picture
     *@since  Method available since Release 2.1
     *
     *@return   void
     */
    public function delPic()
    {
        $model = $this->getModel('editgroup');

        $itemid     = JRequest::getInt('Itemid');

        /* $option     = JRequest::getVar('option');
        * $view       = JRequest::getVar('view');
        $layout     = JRequest::getVar('layout'); */
        $gsgid      = JRequest::getInt('gsgid');
        $layout_back = JRequest::getVar('layout_back', /*0*/'LLLL');
        $view_back   = JRequest::getVar('view_back', /*0*/'VVVV');

        $link = JRoute::_("index.php?option=com_thm_groups"
                            . "&view=editgroup"
                            . "&layout=default&Itemid=" . $itemid
                            . "&gsgid=" . $gsgid
                            . "&layout_back=" . $layout_back
                            . "&view_back=" . $view_back
                        );

        if ($model->delPic())
        {
            $msg = JText::_('COM_THM_GROUPS_PICTURE_REMOVED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_REMOVE_PICTURE_ERROR');
        }
        $this->setRedirect($link, $msg);
    }

    /**
     *  Method, which sets the redirect for the 'back' button.
     *@since  Method available since Release 2.1
     *
     *@return   void
     */
    public function backToRefUrl()
    {
        $gsgid      = JRequest::getInt('gsgid', 1);
        $option_back = JRequest::getVar('option_back');
        $layout_back = JRequest::getVar('layout_back', 0);
        $view_back   = JRequest::getVar('view_back', 0);
        $itemid_back = JRequest::getInt('Itemid', 0);

        $msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
        $link = JRoute::_('index.php'
                            . '?option=' . $option_back
                            . '&view=' . $view_back
                            . '&layout=' . $layout_back
                            . '&Itemid=' . $itemid_back
                            . '&gsgid=' . $gsgid
                        );
        $this->setRedirect($link, $msg);
    }
}
