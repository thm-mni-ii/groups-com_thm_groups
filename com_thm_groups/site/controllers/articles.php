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
class THM_GroupsControllerArticles extends JControllerLegacy
{

    protected $text_prefix = 'COM_THM_GROUPS';

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
        $this->registerTask('unpublish', 'publish');

        // Value = 2
        $this->registerTask('archive', 'publish');

        // Value = -2
        $this->registerTask('trash', 'publish');

        // Value = -3
        $this->registerTask('report', 'publish');
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

        $this->setRedirect(JRoute::_('index.php'), $msg, $type);
    }

    /**
     * Method to publish a list of items
     *
     * @return  void
     *
     * @since   12.2
     */
    public function publish()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
        }
        else
        {
            // Get the model.
            $model = $this->getModel('articles');

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);

            // Publish the items.
            try
            {
                $model->publish($cid, $value);

                if ($value == 1)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
                }
                elseif ($value == 0)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
                }
                elseif ($value == 2)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
                }
                else
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
                }

                $this->setMessage(JText::plural($ntext, count($cid)));
            }
            catch (Exception $e)
            {
                $this->setMessage($e->getMessage(), 'error');
            }
        }

        $this->setRedirect(JRoute::_('index.php'));
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        // Get the input
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel('articles');

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }
}

