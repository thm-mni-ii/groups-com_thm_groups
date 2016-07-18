<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsHelperProfile
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for template editing
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperTemplate
{
	/**
	 * Returns array with attributes and assigned parameters
	 *
	 * @param   array $allAttributes      All attributes in THM Groups
	 * @param   array $templateAttributes Attributes of a certain template
	 *
	 * @return  mixed  array on success, false otherwise
	 */
	public static function assignParametersToAttributes($allAttributes, $templateAttributes)
	{
		$app = JFactory::getApplication();
		if (empty($allAttributes))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_ATTRIBUTE_ERROR_NO_ATTRIBUTES'), 'error');

			return false;
		}

		if (empty($templateAttributes))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NO_ATTRIBUTES'), 'error');

			return false;
		}

		foreach ($allAttributes as $attribute)
		{
			foreach ($templateAttributes as $templateAttribute)
			{
				if ($attribute->id == $templateAttribute->attributeID)
				{
					if (!empty($templateAttribute->ID))
					{
						$attribute->ID = $templateAttribute->ID;
					}

					$attribute->published = $templateAttribute->published;
					$attribute->order     = $templateAttribute->order;
					$attribute->params    = $templateAttribute->params;
				}
			}
		}

		return $allAttributes;
	}

	/**
	 * Returns all attributes with parameters of a template by its ID
	 *
	 * @param   int $templateID Template ID
	 *
	 * @return  mixed  array on success, false otherwise
	 */
	public static function getTemplateAttributes($templateID)
	{
		$app = JFactory::getApplication();
		if (empty($templateID))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NULL'), 'error');

			return false;
		}

		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query
			->select("*")
			->from('#__thm_groups_profile_attribute')
			->where("profileID = $templateID");
		$dbo->setQuery($query);

		try
		{
			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_MANAGER_ERROR_GET_TEMPLATE_ATTRIBUTES'), 'error');

			return false;
		}
	}
}
