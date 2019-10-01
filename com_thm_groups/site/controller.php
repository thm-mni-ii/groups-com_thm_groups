<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
require_once HELPERS . 'profiles.php';

/**
 * Site controller class for component com_thm_groups
 *
 * Main controller for the site section of the component
 * @link        www.thm.de
 */
class THM_GroupsController extends JControllerLegacy
{
    private $profileID;

    private $resource = '';

    /**
     * Class constructor
     *
     * @param array $config An optional associative array of configuration settings.
     *
     * @throws Exception
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $task           = JFactory::getApplication()->input->get('task', '');
        $taskParts      = explode('.', $task);
        $this->resource = $taskParts[0];
    }

    /**
     * Saves changes to the profile and returns to the edit view
     *
     * @return  void
     * @throws Exception
     */
    public function apply()
    {
        $this->preProcess();

        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
        $model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
        $success = $model->save();

        $app = JFactory::getApplication();

        if ($success) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
        } else {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
        }

        $params = ['profileID' => $this->profileID, 'view' => 'profile_edit'];
        $url = THM_GroupsHelperRouter::build($params);
        $app->redirect($url);
    }

    /**
     * Saves changes to the profile and returns to the edit view
     *
     * @return  void
     * @throws Exception
     */
    public function cancel()
    {
        $this->preProcess();

        $params = ['profileID' => $this->profileID, 'view' => 'profile'];
        $url = THM_GroupsHelperRouter::build($params);
        JFactory::getApplication()->redirect($url);
    }

    /**
     * Checks in content
     *
     * @return void
     * @throws Exception
     */
    public function checkin()
    {
        $app               = JFactory::getApplication();
        $model             = $this->getModel($this->resource);
        $functionAvailable = (method_exists($model, 'checkin'));

        if ($functionAvailable) {
            $success = $this->getModel($this->resource)->checkin();

            if ($success) {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
            } else {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
            }
        } else {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE'), 'error');
        }

        $referrer = $app->input->server->getString('HTTP_REFERER');
        $app->redirect($referrer);
    }

    /**
     * Calls delete function for picture in the model
     *
     * @return  void outputs a blank string on success, otherwise affects no change
     * @throws Exception
     */
    public function deletePicture()
    {
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
        $model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
        $success = $model->deletePicture();

        echo empty($success) ? 'error' : '';

        JFactory::getApplication()->close();
    }

    /**
     * Sets object variables and checks access rights. Redirects on insufficient access.
     *
     * @return  void
     * @throws Exception
     */
    private function preProcess()
    {
        $input = JFactory::getApplication()->input;
        $data  = $input->get('jform', [], 'array');

        $this->profileID = $data['profileID'];

        if (!THM_GroupsHelperProfiles::canEdit($this->profileID)) {
            JFactory::getApplication()->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');
            $isPublished = THM_GroupsHelperProfiles::isPublished($this->profileID);
            $profileAlias = THM_GroupsHelperProfiles::getAlias($this->profileID);
            if ($isPublished and $profileAlias) {
                $url = THM_GroupsHelperRouter::build(['profileID' => $this->profileID]);
                JFactory::getApplication()->redirect($url);
            }
            JFactory::getApplication()->redirect();
        }

        return;
    }

    /**
     * Publishes the resource
     *
     * @return void
     * @throws Exception
     */
    public function publish()
    {
        $app               = JFactory::getApplication();
        $model             = $this->getModel($this->resource);
        $functionAvailable = (method_exists($model, 'publish'));

        if ($functionAvailable) {
            $success = $model->publish();

            if ($success) {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
            } else {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
            }
        } else {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE'), 'error');
        }

        $referrer = $app->input->server->getString('HTTP_REFERER');
        $app->redirect($referrer);
    }

    /**
     * Saves changes to the profile and redirects to the profile on success
     *
     * @return  void
     * @throws Exception
     */
    public function save()
    {
        $this->preProcess();

        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
        $model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
        $success = $model->save();

        $app = JFactory::getApplication();
        $params = ['profileID' => $this->profileID];

        if ($success) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
            $params['view'] = 'profile';
        } else {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
            $params['view'] = 'profile_edit';
        }

        $url = THM_GroupsHelperRouter::build($params);
        $app->redirect($url);
    }

    /**
     * Saves the cropped image and outputs the saved image on success.
     *
     * @return  void outputs the saved image on success, otherwise affects no change
     * @throws Exception
     */
    public function saveCropped()
    {
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
        $model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
        $success = $model->saveCropped();

        if ($success != false) {
            echo $success;
        }

        JFactory::getApplication()->close();
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @throws Exception
     */
    public function saveOrderAjax()
    {
        $model             = $this->getModel($this->resource);
        $functionAvailable = (method_exists($model, 'saveorder'));

        if ($functionAvailable) {
            // Get the input
            $pks   = THM_GroupsHelperComponent::cleanIntCollection($this->input->get('cid', [], 'array'));
            $order = array_keys($pks);

            if ($model->saveorder($pks, $order)) {
                echo "1";
            }
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    /**
     * Toggles binary resource properties and redirects back to the list view
     *
     * @return void
     * @throws Exception
     */
    public function toggle()
    {
        $app               = JFactory::getApplication();
        $model             = $this->getModel($this->resource);
        $functionAvailable = (method_exists($model, 'toggle'));

        if ($functionAvailable) {
            $success = $this->getModel($this->resource)->toggle();

            if ($success) {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
            } else {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
            }
        } else {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE'), 'error');
        }

        $referrer = $app->input->server->getString('HTTP_REFERER');
        $app->redirect($referrer);
    }

    /**
     * Hides display of personal content
     *
     * @return void
     * @throws Exception
     */
    public function unpublish()
    {
        $app               = JFactory::getApplication();
        $model             = $this->getModel($this->resource);
        $functionAvailable = (method_exists($model, 'unpublish'));

        if ($functionAvailable) {
            $success = $model->unpublish();

            if ($success) {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
            } else {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
            }
        } else {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE'), 'error');
        }

        $referrer = $app->input->server->getString('HTTP_REFERER');
        $app->redirect($referrer);
    }
}
