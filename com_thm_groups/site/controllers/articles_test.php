<?php

/**
 * @version     v3.4.5
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v1.0.0
 */
class THM_GroupsControllerArticles_Test extends JControllerLegacy
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Toggles category behaviour properties
     *
     * @return void
     */
    public function toggle()
    {
        $model = $this->getModel('article');
        $success = $model->toggle();
        if ($success)
        {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_SUCCESS');
            $type = 'message';
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_MESSAGE_SAVE_FAIL');
            $type = 'error';
        }
        $this->setRedirect("index.php?option=com_thm_groups&view=articles_test", $msg, $type);
    }
}

