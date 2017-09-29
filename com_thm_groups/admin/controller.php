<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsController
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THMGroupsController class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsController extends JControllerLegacy
{
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
	 * Adding
	 *
	 * @return  void redirects to the edit view for a new resource entry
	 */
	public function add()
	{
		$app = JFactory::getApplication();
		$app->input->set('view', "{$this->resource}_edit");
		$app->input->set('id', 0);
		parent::display();
	}

	/**
	 * Saves changes to the resource and redirects back to the edit view of the same resource.
	 *
	 * @return void
	 */
	public function apply()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'save'));

		if ($functionAvailable)
		{
			$resourceID = $model->save();

			if (!empty($resourceID))
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
			$resourceID = $app->input->get('id', 0);
			$msg        = JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE');
			$type       = 'error';
		}

		$app->enqueueMessage($msg, $type);
		$app->input->set('view', "{$this->resource}_edit");
		$app->input->set('id', $resourceID);
		parent::display();
	}

	/**
	 * Saves changes to multiple resources and redirects back to resource list view.
	 *
	 * @return void
	 */
	public function batch()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'batch'));

		if ($functionAvailable)
		{
			$success = $model->batch();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_BATCH_SUCCESS');
				$type = 'message';
			}
			else
			{
				$msg  = JText::_('COM_THM_GROUPS_BATCH_FAIL');
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
	 * Cancels the edit of the resource and redirects to the list view
	 *
	 * @return void
	 */
	public function cancel()
	{
		JFactory::getApplication()->input->set('view', "{$this->resource}_manager");
		parent::display();
	}

	/**
	 * Deletes selected resource entries
	 *
	 * @return void
	 */
	public function delete()
	{
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'delete'));

		if ($functionAvailable)
		{
			$success = $model->delete();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_DELETE_SUCCESS');
				$type = 'message';
			}
			else
			{
				$msg  = JText::_('COM_THM_GROUPS_DELETE_FAIL');
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
	 * Calls delete function for picture in the model
	 *
	 * @return  void outputs a blank string on success, otherwise affects no change
	 */
	public function deletePicture()
	{
		$model   = $this->getModel('profile');
		$success = $model->deletePicture();

		if ($success)
		{
			echo '';
		}

		JFactory::getApplication()->close();
	}

	/**
	 * Deletes all roles in a group by user ID
	 *
	 * @return void
	 */
	public function deleteGroupAssociation()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'deleteGroupAssociation'));

		if ($functionAvailable)
		{
			$success = $model->deleteGroupAssociation();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_DELETE_SUCCESS');
				$type = 'message';
			}
			else
			{
				$msg  = JText::_('COM_THM_GROUPS_DELETE_FAIL');
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
	 * Deletes a role of a user by user id
	 *
	 * @return void
	 */
	public function deleteRoleAssociation()
	{
		$model   = $this->getModel('profile');
		$success = $model->deleteRoleAssociation();
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
		$this->setRedirect("index.php?option=com_thm_groups&view=profile_manager", $msg, $type);
	}

	/**
	 * Redirects to the edit view for the resource
	 *
	 * @return void
	 */
	public function edit()
	{
		$app = JFactory::getApplication();
		$app->input->set('view', "{$this->resource}_edit");

		$requestedIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', array(), 'array'));
		$requestedID  = (empty($requestedIDs) OR empty($requestedIDs[0])) ?
			$app->input->getInt('id', 0) : $requestedIDs[0];

		$app->input->set('id', $requestedID);
		$app->input->set('hidemainmenu', 1);
		parent::display();
	}

	/**
	 * Featured content is offered in profile menus
	 *
	 * @return  void
	 */
	public function feature()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'feature'));

		if ($functionAvailable)
		{
			$success = $model->feature();

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
	 * Removes the role's association with the given group
	 *
	 * @return void
	 */
	public function removeRole()
	{
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'removeRole'));

		if ($functionAvailable)
		{
			$success = $model->removeRole();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_DELETED');
				$type = 'message';
			}
			else
			{
				$this->setMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'warning');
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
	 * Removes the profile's association with the given group
	 *
	 * @return void
	 */
	public function removeTemplate()
	{
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'removeTemplate'));

		if ($functionAvailable)
		{
			$success = $model->removeTemplate();

			if ($success)
			{
				$msg  = JText::_('COM_THM_GROUPS_DELETED');
				$type = 'message';
			}
			else
			{
				$this->setMessage(JText::_('COM_THM_GROUPS_SAVE_FAIL'), 'warning');
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
	 * Saves changes to the resource and redirects to the list view
	 *
	 * @return void
	 */
	public function save()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'save'));

		if ($functionAvailable)
		{
			$success = $model->save();

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
	 * Saves changes to the resource and redirects to the list view
	 *
	 * @return void
	 */
	public function save2copy()
	{
		$app               = JFactory::getApplication();
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'save'));
		$formData          = $app->input->get('jform', array(), 'array');

		if ($functionAvailable AND !empty($formData['id']))
		{
			$existingID     = $formData['id'];
			$formData['id'] = 0;
			$app->input->set('jform', $formData);

			$newID = $model->save();

			if ($newID)
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
				$type = 'message';
				$app->input->set('id', $newID);
			}
			else
			{
				$msg  = JText::_('COM_THM_GROUPS_SAVE_FAIL');
				$type = 'error';
				$app->input->set('id', $existingID);
			}
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_ACTION_UNAVAILABLE');
			$type = 'error';
		}

		$app->enqueueMessage($msg, $type);
		$app->input->set('view', "{$this->resource}_edit");
		parent::display();
	}

	/**
	 * Saves the selected attribute and redirects to a new page
	 * to create a new attribute
	 *
	 * @return void
	 */
	public function save2new()
	{
		$model             = $this->getModel($this->resource);
		$functionAvailable = (method_exists($model, 'save'));

		if ($functionAvailable)
		{
			$success = $model->save();

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
		$app->input->set('view', "{$this->resource}_edit");
		$app->input->set('id', 0);
		parent::display();
	}

	/**
	 * Saves the crop of the selected image. As this function called via ajax it does not have the structure typical to
	 * the rest of the functions of this class.
	 *
	 * @return  void outputs the saved image on success, otherwise affects no change
	 */
	public function saveCropped()
	{
		$model   = $this->getModel('profile');
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
	 * Hides the public display of the resource
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
