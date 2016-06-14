<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerProfile
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THM_GroupsControllerQuickpage_Content class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsControllerQuickpage_Content extends JControllerAdmin
{
	protected $text_prefix = 'COM_THM_GROUPS';

	/**
	 * Method to publish a single item in the list, table '#__content'
	 *
	 * @return  void
	 */
	public function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid      = JFactory::getApplication()->input->get('cid', array(), 'array');
		$statuses = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task     = $this->getTask();
		$value    = Joomla\Utilities\ArrayHelper::getValue($statuses, $task, 0, 'int');
		$success  = 0;

		$model = $this->getModel('quickpage_content');

		// Make sure the item ids are integers
		Joomla\Utilities\ArrayHelper::toInteger($cid);

		// Publish the items.
		try
		{
			$success = $model->publish($cid, $value);
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		$text = "";
		if (!$success)
		{
			JFactory::getApplication()->enqueueMessage(JText::_($this->text_prefix . '_ARTICLE_FAILED_PUBLISHING'), 'error');
		}
		else
		{
			if ($value == 1)
			{
				$text = $this->text_prefix . '_ARTICLE_PUBLISHED';
			}
			elseif ($value == 0)
			{
				$text = $this->text_prefix . '_ARTICLE_UNPUBLISHED';
			}
			elseif ($value == 2)
			{
				$text = $this->text_prefix . '_ARTICLE_ARCHIVED';
			}
			else
			{
				$text = $this->text_prefix . '_ARTICLE_TRASHED';
			}
		}

		$this->setMessage(JText::_($text));
		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_content_manager");
	}

	/**
	 * Toggles the state of a single item after click
	 * on a toggle button in the list
	 *
	 * @return void
	 */
	public function toggle()
	{
		$canEdit = THM_GroupsHelperComponent::canEdit();
		if (!$canEdit)
		{
			return;
		}

		$app     = JFactory::getApplication();
		$input   = $app->input;
		$cid     = $input->get('cid', array(), 'array');
		$model   = $this->getModel('quickpage_content');
		$success = 0;

		try
		{
			$success = $model->toggle($cid);
		}
		catch (Exception $exception)
		{
			$app->enqueueMessage($exception->getMessage(), 'error');
		}

		$attribute = strtoupper($input->getString('attribute', ''));
		$value     = $input->getInt('value', 1);

		// $task = unpublished/unfeatured or published/featured
		$task = $value === 1 ? 'UN' . $attribute : $attribute;

		if ($success)
		{
			$this->setMessage(JText::_($this->text_prefix . '_ARTICLE_' . $task));
		}
		else
		{
			$this->setMessage(JText::_('COM_THM_GROUPS_SAVE_ERROR'), 'error');
		}

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_content_manager");
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

		$model   = $this->getModel('quickpage_content');
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

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_content_manager");
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

		$model   = $this->getModel('quickpage_content');
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

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_content_manager");

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

		$model   = $this->getModel('quickpage_content');
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

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_content_manager");
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

		$model   = $this->getModel('quickpage_content');
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

		$this->setRedirect("index.php?option=com_thm_groups&view=quickpage_content_manager");
	}
}