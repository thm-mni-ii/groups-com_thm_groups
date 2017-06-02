<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerQuickpage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * Class provides validity checks, data manipulation function calls, and redirection
 * for THM Groups associated content.
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsControllerQuickpage extends JControllerAdmin
{
	protected $text_prefix = 'COM_THM_GROUPS';

	/**
	 * Method to publish a single article
	 *
	 * @return  void
	 */
	public function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$articleIDs = JFactory::getApplication()->input->get('cid', array(), 'array');
		Joomla\Utilities\ArrayHelper::toInteger($articleIDs);

		$statuses = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task     = $this->getTask();
		$status   = Joomla\Utilities\ArrayHelper::getValue($statuses, $task, 0, 'int');
		$model    = $this->getModel('quickpage');

		foreach ($articleIDs as $articleID)
		{
			$model->publish($articleID, $status);
		}

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_manager");
	}

	/**
	 * Specific publishing of quickpages
	 *
	 * @return  void
	 */
	public function qpPublish()
	{
		$canEdit = THM_GroupsHelperComponent::canEdit();
		if (!$canEdit)
		{
			return;
		}

		$model   = $this->getModel('quickpage');
		$cid     = JFactory::getApplication()->input->get('cid', array(), 'array');
		$success = 0;

		try
		{
			$success = $model->activate($cid, 'published');
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($success === 1)
		{
			$this->setMessage(JText::_($this->text_prefix . '_ARTICLE_PUBLISHED'));
		}
		elseif ($success > 1)
		{
			$this->setMessage(JText::plural($this->text_prefix . '_N_ARTICLES_PUBLISHED', count($cid)));
		}
		else
		{
			$this->setMessage(JText::_('COM_THM_GROUPS_SAVE_ERROR'), 'error');
		}

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_manager");
	}

	/**
	 * Specific un-publishing of quickpages
	 *
	 * @return  void
	 */
	public function qpUnpublish()
	{
		$canEdit = THM_GroupsHelperComponent::canEdit();
		if (!$canEdit)
		{
			return;
		}

		$model   = $this->getModel('quickpage');
		$cid     = JFactory::getApplication()->input->get('cid', array(), 'array');
		$success = 0;

		try
		{
			$success = $model->deactivate($cid, 'published');
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($success === 1)
		{
			$this->setMessage(JText::_($this->text_prefix . '_ARTICLE_UNPUBLISHED'));
		}
		elseif ($success > 1)
		{
			$this->setMessage(JText::plural($this->text_prefix . '_N_ARTICLES_UNPUBLISHED', count($cid)));
		}
		else
		{
			$this->setMessage(JText::_('COM_THM_GROUPS_SAVE_ERROR'), 'error');
		}

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_manager");

	}

	/**
	 * Specific featuring of quickpages
	 *
	 * @return  void
	 */
	public function qpFeature()
	{
		$canEdit = THM_GroupsHelperComponent::canEdit();
		if (!$canEdit)
		{
			return;
		}

		$model   = $this->getModel('quickpage');
		$cid     = JFactory::getApplication()->input->get('cid', array(), 'array');
		$success = 0;

		try
		{
			$success = $model->activate($cid, 'featured');
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($success === 1)
		{
			$this->setMessage(JText::_($this->text_prefix . '_ARTICLE_FEATURED'));
		}
		elseif ($success > 1)
		{
			$this->setMessage(JText::plural($this->text_prefix . '_N_ARTICLES_FEATURED', count($cid)));
		}
		else
		{
			$this->setMessage(JText::_('COM_THM_GROUPS_SAVE_ERROR'), 'error');
		}

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_manager");
	}

	/**
	 * Specific un-featuring of quickpages
	 *
	 * @return  void
	 */
	public function qpUnfeature()
	{
		$canEdit = THM_GroupsHelperComponent::canEdit();
		if (!$canEdit)
		{
			return;
		}

		$model   = $this->getModel('quickpage');
		$cid     = JFactory::getApplication()->input->get('cid', array(), 'array');
		$success = 0;

		try
		{
			$success = $model->deactivate($cid, 'featured');
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($success === 1)
		{
			$this->setMessage(JText::_($this->text_prefix . '_ARTICLE_UNFEATURED'));
		}
		elseif ($success > 1)
		{
			$this->setMessage(JText::plural($this->text_prefix . '_N_ARTICLES_UNFEATURED', count($cid)));
		}
		else
		{
			$this->setMessage(JText::_('COM_THM_GROUPS_SAVE_ERROR'), 'error');
		}

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_manager");
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->get('cid', array(), 'array');
		$order = $this->input->get('order', array(), 'array');

		// Sanitize the input
		Joomla\Utilities\ArrayHelper::toInteger($pks);
		Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel('quickpage');

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	/**
	 * Toggles the state of a single binary quickapge attribute
	 *
	 * @return void
	 */
	public function toggle()
	{
		$model = $this->getModel('quickpage');

		// Access checks and output messages are in the model.
		$model->toggle();

		$menuID = $this->input->getInt('Itemid', 0);
		$this->setRedirect(JRoute::_("index.php?option=com_thm_groups&view=quickpage_manager&Itemid=$menuID"));
	}
}