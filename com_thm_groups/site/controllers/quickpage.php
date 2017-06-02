<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
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
 * Class provides access & validity checks, data manipulation function calls, and redirection
 * for THM Groups associated content.
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 */
class THM_GroupsControllerQuickpage extends JControllerLegacy
{
	protected $text_prefix = 'COM_THM_GROUPS';

	/**
	 * Constructor. Additionally maps function calls named after published states to the publish function.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// All state tasks are handled by the publish function
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('archive', 'publish');
		$this->registerTask('trash', 'publish');
		$this->registerTask('report', 'publish');
	}

	/**
	 * Method to publish a list of items
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

		$menuID = $this->input->getInt('Itemid', 0);
		$this->setRedirect(JRoute::_("index.php?option=com_thm_groups&view=quickpage_manager&Itemid=$menuID"));
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