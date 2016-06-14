<?php

/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Quickpage resource controller class
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 */
class THM_GroupsControllerQuickpage extends JControllerLegacy
{
	private $_baseURL = 'index.php?option=com_thm_groups';

	protected $text_prefix = 'COM_THM_GROUPS';

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// TODO: rename to changeState
		// All state tasks are handled by the publish function
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('archive', 'publish');
		$this->registerTask('trash', 'publish');
	}

	/**
	 * Toggles quickpage boolean properties
	 *
	 * @todo  use standard joomla form instead of get, ie post for token validation
	 *
	 * @return void
	 */
	public function toggle()
	{
		$model = $this->getModel('quickpage');

		// Access checks and output messages are in the model.
		$model->toggle();

		$menuID     = $this->input->getInt('Itemid', 0);
		$forwardURL = $this->_baseURL . "&view=quickpage_manager&Itemid=$menuID";
		$this->setRedirect(JRoute::_($forwardURL));
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 *
	 */
	public function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('quickpage');

		// Access checks and output messages are in the model.
		$model->publish();

		$menuID     = $this->input->getInt('Itemid', 0);
		$forwardURL = $this->_baseURL . "&view=quickpage_manager&Itemid=$menuID";
		$this->setRedirect(JRoute::_($forwardURL));
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
}

