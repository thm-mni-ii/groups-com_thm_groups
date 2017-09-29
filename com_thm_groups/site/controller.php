<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsController
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * Site controller class for component com_thm_groups
 *
 * Main controller for the site section of the component
 *
 * @category    Joomla.Component.Site
 * @package     thm_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.thm.de
 */
class THM_GroupsController extends JControllerLegacy
{
	private $baseURL = 'index.php?option=com_thm_groups';

	private $profileID;

	private $groupID;

	private $menuID;

	private $surname;

	private $resource = '';

	/**
	 * Class constructor
	 *
	 * @param array $config An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
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
	 */
	public function apply()
	{
		$this->preProcess();

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
		$model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
		$success = $model->save();

		$app = JFactory::getApplication();

		if ($success)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
		}

		$URL = "{$this->baseURL}&view=profile_edit&profileID={$this->profileID}&groupID={$this->groupID}";
		$URL .= "&name={$this->surname}&Itemid={$this->menuID}";

		$app->redirect(JRoute::_($URL));
	}

	/**
	 * Calls delete function for picture in the model
	 *
	 * @return  void outputs a blank string on success, otherwise affects no change
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
	 * Publishes the resource
	 *
	 * @return void
	 */
	public function publish()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'publish'));

		if ($functionAvailable)
		{
			$success = $model->publish();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
				$type = 'message';
			}
			else
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_FAIL');
				$type = 'error';
			}
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE');
			$type = 'error';
		}

		$app->enqueueMessage($msg, $type);
		$app->input->set('view', "{$this->resource}_manager");
		parent::display();
	}

	/**
	 * Sets object variables and checks access rights. Redirects on insufficient access.
	 *
	 * @return  void
	 */
	private function preProcess()
	{
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		$this->profileID = $data['profileID'];
		$this->groupID   = $data['groupID'];
		$this->menuID    = $data['menuID'];
		$this->surname   = $data['name'];

		if (!THM_GroupsHelperComponent::canEditProfile($this->profileID))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');
			$this->redirectNoAccess();
		}

		return;
	}

	/**
	 * Sets display parameters and redirects
	 *
	 * @param string $view the view name to redirect to.
	 *
	 * @return void redirects to the next page
	 */
	public function redirectNoAccess()
	{
		$this->input->set('view', 'profile');
		$this->input->set('groupID', $this->groupID);
		$this->input->set('profileID', $this->profileID);
		$this->input->set('Itemid', $this->menuID);
		$this->input->set('name', $this->surname);

		parent::display();
	}

	/**
	 * Saves changes to the profile and redirects to the profile on success
	 *
	 * @return  void
	 */
	public function save2Profile()
	{
		$this->preProcess();

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
		$model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
		$success = $model->save();

		$app = JFactory::getApplication();

		if ($success)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_SUCCESS'));
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'error');
		}

		$URL = "{$this->baseURL}&view=profile&profileID={$this->profileID}&groupID={$this->groupID}";
		$URL .= "&name={$this->surname}&Itemid={$this->menuID}";

		$app->redirect(JRoute::_($URL));
	}

	/**
	 * Saves the cropped image and outputs the saved image on success.
	 *
	 * @return  void outputs the saved image on success, otherwise affects no change
	 */
	public function saveCropped()
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
		$model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
		$success = $model->saveCropped();

		if ($success != false)
		{
			echo $success;
		}

		JFactory::getApplication()->close();
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 */
	public function saveOrderAjax()
	{
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'saveorder'));

		if ($functionAvailable)
		{
			// Get the input
			$pks   = THM_GroupsHelperComponent::cleanIntCollection($this->input->get('cid', array(), 'array'));
			$order = array_keys($pks);

			if ($model->saveorder($pks, $order))
			{
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
	 */
	public function toggle()
	{
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'toggle'));

		if ($functionAvailable)
		{
			$success = $this->getModel($this->resource)->toggle();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
				$type = 'message';
			}
			else
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_FAIL');
				$type = 'error';
			}
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE');
			$type = 'error';
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage($msg, $type);
		$app->input->set('view', "{$this->resource}_manager");
		parent::display();
	}

	/**
	 * Hides display of personal content
	 *
	 * @return void
	 */
	public function unpublish()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'unpublish'));

		if ($functionAvailable)
		{
			$success = $model->unpublish();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
				$type = 'message';
			}
			else
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_FAIL');
				$type = 'error';
			}
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE');
			$type = 'error';
		}

		$app->enqueueMessage($msg, $type);
		$app->input->set('view', "{$this->resource}_manager");
		parent::display();
	}
}
