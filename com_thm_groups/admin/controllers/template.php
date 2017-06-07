<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerTemplate
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

/**
 * THM_GroupsControllerTemplate class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsControllerTemplate extends JControllerLegacy
{
	/**
	 * Redirects to the dynamic_type_edit view for the creation of new element
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 */
	public function add()
	{
		if (!JFactory::getUser()->authorise('core.create', 'com_thm_groups'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$input = JFactory::getApplication()->input;
		$input->set('view', 'template_edit');
		$input->set('id', '0');

		return parent::display();
	}

	/**
	 * Saves changes to the template being edited and redirects back to the same edit view.
	 *
	 * @return void
	 */
	public function apply()
	{
		$canSave = $this->canSave();

		if (!$canSave)
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$templateID = $this->getModel('template')->save();
		if (empty($templateID))
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
			$this->setRedirect('index.php?option=com_thm_groups&view=template_edit&id=0', $msg, 'error');
		}
		else
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
			$this->setRedirect('index.php?option=com_thm_groups&view=template_edit&id=' . $templateID, $msg);
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object $model The model.
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		// Set the model
		$model = $this->getModel('template');

		$model->batch();

		// Output messages are set in the model.
		$this->setRedirect(JRoute::_('index.php?option=com_thm_groups&view=template_manager', false));
	}

	/**
	 * Redirects to the category manager view without making any persistent changes
	 *
	 * @param   Integer $key contains the key
	 *
	 * @return  void
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function cancel($key = null)
	{
		$this->setRedirect('index.php?option=com_thm_groups&view=template_manager');
	}

	/**
	 * Checks whether the user has access to save
	 *
	 * @todo  Standardize this across the backend/frontend with manage and item id checks
	 *
	 * @return  bool true if user has access, otherwise false
	 */
	private function canSave()
	{
		$canCreate = JFactory::getUser()->authorise('core.create', 'com_thm_groups');
		$canEdit   = JFactory::getUser()->authorise('core.edit', 'com_thm_groups');

		return ($canCreate OR $canEdit);
	}

	/**
	 * Deletes the selected category and redirects to the category manager
	 *
	 * @return void
	 */
	public function delete()
	{
		if (!JFactory::getUser()->authorise('core.delete', 'com_thm_groups'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$success = $this->getModel('template')->delete();
		if ($success)
		{
			$msg  = JText::_('COM_THM_GROUPS_DELETE_SUCCESS');
			$type = 'message';
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_DELETE_ERROR');
			$type = 'error';
		}
		$this->setRedirect("index.php?option=com_thm_groups&view=template_manager", $msg, $type);
	}

	/**
	 * Removes group -> profile associations
	 *
	 * @return  void
	 */
	public function deleteGroup()
	{
		$model   = $this->getModel('template');
		$success = $model->deleteGroup();
		if ($success)
		{
			$msg  = JText::_('COM_THM_GROUPS_DELETE_SUCCESS');
			$type = 'message';
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_DELETE_ERROR');
			$type = 'error';
		}
		$this->setRedirect("index.php?option=com_thm_groups&view=template_manager", $msg, $type);
	}

	/**
	 * Redirects to the category_edit view for the editing of existing categories
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 */
	public function edit()
	{
		if (!JFactory::getUser()->authorise('core.edit', 'com_thm_groups'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->input->set('view', 'template_edit');
		$this->input->set('hidemainmenu', 1);

		return parent::display();
	}

	/**
	 * Saves the profile being edited and closes the edit view.
	 *
	 * @param   Integer $key    contain key
	 * @param   String  $urlVar contain url
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function save($key = null, $urlVar = null)
	{
		$canSave = $this->canSave();
		if (!$canSave)
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$success = $this->getModel('template')->save();

		if ($success)
		{
			$msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
			$type = 'message';
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
			$type = 'error';
		}
		$this->setRedirect('index.php?option=com_thm_groups&view=template_manager', $msg, $type);
	}

	/**
	 * Saves the profile being edited as a copy of the original
	 *
	 * @return  void
	 */
	public function save2copy()
	{
		$canSave = $this->canSave();
		if (!$canSave)
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$templateID = $this->getModel('template')->save(true);
		if (empty($templateID))
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
			$this->setRedirect('index.php?option=com_thm_groups&view=template_edit&id=0', $msg, 'error');
		}
		else
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
			$this->setRedirect('index.php?option=com_thm_groups&view=template_edit&id=' . $templateID, $msg);
		}
	}

	/**
	 * Saves the profile being edited and opens a blank profile edit view
	 *
	 * @return void
	 */
	public function save2new()
	{
		$canSave = $this->canSave();
		if (!$canSave)
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$success = $this->getModel('template')->save();
		if ($success)
		{
			$msg  = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
			$type = 'message';
		}
		else
		{
			$msg  = JText::_('COM_THM_GROUPS_SAVE_ERROR');
			$type = 'error';
		}
		$this->setRedirect('index.php?option=com_thm_groups&view=template_edit&id=0', $msg, $type);
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
		$pks = $this->input->get('cid', array(), 'array');
		Joomla\Utilities\ArrayHelper::toInteger($pks);
		$order = array_keys($pks);
		Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel('template');

		try
		{
			// Save the ordering
			$return = $model->saveorder($pks, $order);
		}
		catch (Exception $exception)
		{
			echo "<pre>" . print_r($exception->getMessage(), true) . "</pre>";
		}

		if (!empty($return))
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}
