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
