<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerUser
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THM_GroupsControllerUser class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerProfile extends JControllerLegacy
{
	private $baseURL = 'index.php?option=com_thm_groups';

	private $profileID;

	private $groupID;

	private $menuID;

	private $surname;

	/**
	 * THM_GroupsControllerProfile constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array())
	{
		parent::__construct($config);
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_thm_groups/models');
	}

	/**
	 * Saves changes to the profile and returns to the edit view
	 *
	 * @return  void
	 */
	public function apply()
	{
		$this->preProcess();

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
		$model   = JModelLegacy::getInstance('profile', 'THM_GroupsModel');
		$success = $model->deletePicture();

		echo empty($success) ? 'error' : '';

		JFactory::getApplication()->close();
	}

	/**
	 * Saves changes to the profile and redirects to the profile on success
	 *
	 * @return  void
	 */
	public function save2Profile()
	{
		$this->preProcess();

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
		$model = JModelLegacy::getInstance('profile', 'THM_GroupsModel');

		$success = $model->saveCropped();

		if ($success != false)
		{
			echo $success;
		}

		JFactory::getApplication()->close();
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
			$this->redirect();
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
	public function redirect($view = 'profile')
	{
		$this->input->set('view', $view);
		$this->input->set('groupID', $this->groupID);
		$this->input->set('profileID', $this->profileID);
		$this->input->set('Itemid', $this->menuID);
		$this->input->set('name', $this->surname);


		parent::display();
	}
}