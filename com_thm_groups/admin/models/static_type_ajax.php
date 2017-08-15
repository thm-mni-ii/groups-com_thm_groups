<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelStatic_Type_Extra_Options_Ajax
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/static_type.php';

/**
 * Class provides method for extra options of static types
 *
 * @category    Joomla.Component.Site
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.site
 */
class THM_GroupsModelStatic_Type_Ajax extends JModelForm
{
	/**
	 * Returns static type's name of a dynamic type by dynamic type ID
	 *
	 * @return string on success, else false
	 * @throws Exception
	 */
	public function getNameByDynamicID()
	{
		$input     = JFactory::getApplication()->input;
		$dynTypeID = $input->getInt('dynTypeID', 0);

		if (empty($dynTypeID))
		{
			return '';
		}

		$query = $this->_db->getQuery(true);

		$query
			->select('static.name')
			->from('#__thm_groups_static_type AS static')
			->innerJoin('#__thm_groups_dynamic_type AS dynamic ON dynamic.static_typeID = static.id')
			->where("dynamic.id = $dynTypeID");
		$this->_db->setQuery($query);

		try
		{
			return $this->_db->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception, 'error');

			return false;
		}
	}

	/**
	 * Returns static type's name by its ID
	 *
	 * @return string on success, else false
	 * @throws Exception
	 */
	public function getNameByID()
	{
		$input        = JFactory::getApplication()->input;
		$staticTypeID = $input->getInt('staticTypeID', 0);

		if (empty($staticTypeID))
		{
			return '';
		}

		$query = $this->_db->getQuery(true);

		$query
			->select('name')
			->from('#__thm_groups_static_type')
			->where("id = $staticTypeID");
		$this->_db->setQuery($query);

		try
		{
			return $this->_db->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception, 'error');

			return false;
		}
	}

	/**
	 * Returns json options from #__thm_groups_attribute
	 *
	 * @param   int $attributeID attribute ID
	 *
	 * @return string in json format on success, else false
	 * @throws Exception
	 */
	public function getAttributeOptionsByID($attributeID)
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('options')
			->from('#__thm_groups_attribute')
			->where("id = $attributeID");
		$this->_db->setQuery($query);

		try
		{
			return $this->_db->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception, 'error');

			return false;
		}
	}

	/**
	 * Returns json options from #__thm_groups_dynamic_type
	 *
	 * @param   int $dynTypeID dynamic type ID
	 *
	 * @return string in json format on success, else false
	 * @throws Exception
	 */
	public function getDynTypeOptionsByID($dynTypeID)
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('options')
			->from('#__thm_groups_dynamic_type')
			->where("id = $dynTypeID");
		$this->_db->setQuery($query);

		try
		{
			return $this->_db->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception, 'error');

			return false;
		}
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   12.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$option = $this->get('option');
		$name   = $this->get('name');

		return $this->loadForm("$option.$name", $name, array('control' => 'jform', 'load_data' => $loadData));
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 */
	public function loadFormData()
	{
		$input          = JFactory::getApplication()->input;
		$dynTypeOptions = $this->getDynTypeOptionsByID($input->getInt('dynTypeID', 0));

		if (!empty(json_decode($dynTypeOptions)))
		{
			$dynTypeOptions = json_decode($dynTypeOptions);
		}
		else
		{
			$staticTypeID = $input->getInt('staticTypeID', 0);

			// New dynamic type -> get default static type parameters
			$dynTypeOptions = THM_GroupsHelperStatic_Type::getOption($staticTypeID);
		}

		$data        = array();
		$attrOptions = json_decode($this->getAttributeOptionsByID($input->getInt('attributeID', 0)));

		// Attribute
		if (!empty($attrOptions))
		{
			foreach ($attrOptions as $key => $value)
			{
				if ($key !== 'required')
				{
					$data[$key] = !empty($value) ? $value : $dynTypeOptions->$key;
				}
			}

			return $data;
		}

		// Dynamic type
		foreach ($dynTypeOptions as $key => $value)
		{
			if ($key !== 'required')
			{
				$data[$key] = !empty($value) ? $value : '';
			}
		}

		return $data;
	}
}
