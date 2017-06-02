<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/static_type.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelDynamic_Type_Edit extends THM_GroupsModelEdit
{
	protected $form = false;

	/**
	 * Method to get the form
	 *
	 * @param   Array   $data     Data         (default: Array)
	 * @param   Boolean $loadData Load data  (default: true)
	 *
	 * @return  A Form object
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function getForm($data = array(), $loadData = true)
	{
		if (empty($this->form))
		{
			$this->form = $this->loadForm('com_thm_groups.dynamic_type_edit', 'dynamic_type_edit', array('control' => 'jform', 'load_data' => $loadData));
		}

		return $this->form;
	}

	/**
	 * returns table object
	 *
	 * @param   string $type   type
	 * @param   string $prefix prefix
	 * @param   array  $config config
	 *
	 * @return  JTable|mixed
	 */
	public function getTable($type = 'Dynamic_Type', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to load the form data
	 *
	 * @return  Object
	 */
	protected function loadFormData()
	{
		$input      = JFactory::getApplication()->input;
		$name       = $this->get('name');
		$resource   = str_replace('_edit', '', $name);
		$task       = $input->getCmd('task', "$resource.add");
		$resourceID = $input->getInt('id', 0);

		// Edit can only be explicitly called from the list view or implicitly with an id over a URL
		$edit = (($task == "$resource.edit") OR $resourceID > 0);
		if ($edit)
		{
			if (!empty($resourceID))
			{
				$item    = $this->getItem($resourceID);
				$options = json_decode($item->options);
				if (!empty($options))
				{
					if (isset($options->required))
					{
						$item->validate = $options->required === false ? 0 : 1;
					}
				}

				return $item;
			}

			$resourceIDs = $input->get('cid', null, 'array');

			return $this->getItem($resourceIDs[0]);
		}

		return $this->getItem(0);
	}

}