<?php

defined('JPATH_PLATFORM') or die;
jimport('cms/html/category.php');
require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_quickpages_data.php";
jimport('joomla.application.categories');

/**
 * Class JFormFieldCustomCategory
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.thm.de
 */
class JFormFieldCustomCategory extends JFormFieldCategory
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'CustomCategory';

	/**
	 * Method to get the field options for category
	 *
	 * @return  array    The field option objects.
	 *
	 */
	protected function getOptions()
	{
		$options = parent::getOptions();

		// Get quickpages params
		$objParams = THM_GroupsQuickpagesData::getQPParams();
		$params    = json_decode($objParams->params);

		// Get user quickpages root category
		$obj_user_qp_category  = THM_GroupsQuickpagesData::getUserQuickpageCategory(JFactory::getUser()->id);
		$user_qp_root_category = $obj_user_qp_category->categoriesID;

		// Get children categories of the user root category
		$categories = JCategories::getInstance('Content');
		$cat        = $categories->get($user_qp_root_category);
		$children   = $cat->getChildren();

		$pks = [];

		// Add user root category to array
		array_push($pks, $user_qp_root_category);

		// Add user root category children to array
		foreach ($children as $child)
		{
			array_push($pks, $child->id);
		}

		// Filter only user's categories
		if ($params->qp_show_all_categories != 1)
		{
			foreach ($options as $key => $option)
			{
				if (array_search($option->value, $pks) === false)
				{
					unset($options[$key]);
				}
			}
		}

		return $options;
	}
}