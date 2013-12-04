<?php

/**
 * @version     v3.4.4
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Articles list controller class.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsControllerArticles extends JControllerAdmin
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see		JController
     */
    public function __construct($config = array())
    {
        // Articles default form can come from the articles or featured view.
        // Adjust the redirect view on the value of 'view' in the request.
        if (JRequest::getCmd('view') == 'featured')
        {
            $this->view_list = 'featured';
        }
        parent::__construct($config);

        $this->registerTask('saveorder', 'saveorder');
        $this->registerTask('orderup', 'reorder');
        $this->registerTask('orderdown', 'reorder');

        /* $this->registerTask('unfeatured',	'featured'); */
        /* $this->registerTask('untrash',	'trash'); */
    }

    /**
     * Method to publish a list of items
     *
     * @return  void
     *
     * @since   11.1
     */
    public function publish()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JRequest::getVar('cid', array(), '', 'array');
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            JError::raiseWarning(500, JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'));
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);

            // Publish the items.
            if (!$model->publish($cid, $value))
            {
                JError::raiseWarning(500, $model->getError());
            }
            else
            {
                if ($value == 1)
                {
                    $ntext = 'COM_THM_GROUPS_N_ITEMS_PUBLISHED';
                }
                elseif ($value == 0)
                {
                    $ntext = 'COM_THM_GROUPS_N_ITEMS_UNPUBLISHED';
                }
                elseif ($value == 2)
                {
                    $ntext = 'COM_THM_GROUPS_N_ITEMS_ARCHIVED';
                }
                else
                {
                    $ntext = 'COM_THM_GROUPS_N_ITEMS_TRASHED';
                    $model->deleteArticleId($cid[0]);
                }
                $this->setMessage(JText::plural($ntext, count($cid)));
            }
        }
        $extension = JRequest::getCmd('extension');
        $extensionURL = ($extension) ? '&extension=' . JRequest::getCmd('extension') : '';
        $this->setRedirect(JRoute::_('index.php?option=' . 'com_thm_groups' . '&view=' . $this->view_list . $extensionURL, false));
    }

    /**
     * Removes an item.
     *
     * @return  void
     *
     * @since   11.1
     */
    public function delete()
    {
        var_dump("bla");
        exit;

        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = JRequest::getVar('cid', array(), '', 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            JError::raiseWarning(500, JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'));
        }
        else
        {

            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(JText::plural('COM_THM_GROUPS_N_ITEMS_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

    /**
     * Changes the order of one or more records.
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function reorder()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $ids = JRequest::getVar('cid', null, 'post', 'array');
        $inc = ($this->getTask() == 'orderup') ? -1 : +1;

        $model = $this->getModel();
        $return = $model->reorder($ids, $inc);
        if ($return === false)
        {
            // Reorder failed.
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=' . $this->view_list, false), $message, 'error');
            return false;
        }
        else
        {
            // Reorder succeeded.
            $message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
            $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=' . $this->view_list, false), $message);
            return true;
        }
    }

    /**
     * Method to save the submitted ordering values for records.
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function saveorder()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the input
        $pks = JRequest::getVar('cid', null, 'post', 'array');
        $order = JRequest::getVar('order', null, 'post', 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return === false)
        {
            // Reorder failed
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=' . $this->view_list, false), $message, 'error');
            return false;
        }
        else
        {
            // Reorder succeeded.
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
            $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=' . $this->view_list, false));
            return true;
        }
    }

    /**
     * Check in of one or more records.
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function checkin()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $ids = JRequest::getVar('cid', null, 'post', 'array');

        $model = $this->getModel();
        $return = $model->checkin($ids);
        if ($return === false)
        {
            // Checkin failed.
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=' . $this->view_list, false), $message, 'error');
            return false;
        }
        else
        {
            // Checkin succeeded.
            $message = JText::_("COM_THM_GROUPS_CHECKED");
            $this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=' . $this->view_list, false), $message);
            return true;
        }
    }


    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  The prefix for the PHP class name.
     * @param   array   $config  Configuration
     *
     * @return	JModel
     */
    public function getModel($name = 'Article', $prefix = 'THMGroupsModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * (Un)Features articles
     *
     * @return void
     */
    public function featureArticle()
    {

        // Get the model.
        $model = $this->getModel();

        // Get items to remove from the request.
        $a_id = JRequest::getVar('a_id', null, '', 'int');

        if ($model->featureArticle($a_id))
        {
            JFactory::getApplication()->enqueueMessage('Erfolgreich featured!', 'Message');
        }
        else
        {
            JFactory::getApplication()->enqueueMessage('Erfolgreich unfeatured!', 'Message');
        }

        $extension = JRequest::getCmd('extension');
        $extensionURL = ($extension) ? '&extension=' . JRequest::getCmd('extension') : '';
        $this->setRedirect(JRoute::_('index.php?option=' . 'com_thm_groups' . '&view=' . $this->view_list . $extensionURL, false));
    }
}

