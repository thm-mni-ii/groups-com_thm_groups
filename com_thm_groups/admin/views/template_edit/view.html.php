<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewTemplate_Edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/template.php';
require_once JPATH_ROOT . '/media/com_thm_groups/views/edit.php';

/**
 * THM_GroupsViewDynamic_Type_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewTemplate_Edit extends THM_GroupsViewEdit
{

	public $attributes;

	/**
	 * Loads model data into the view context
	 *
	 * @param   string $tpl the name of the template to be used
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
		{
			$exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
			JErrorPage::render($exc);
		}

		$allAttributes = THM_GroupsHelperProfile::getAllAttributes();
		$id            = JFactory::getApplication()->input->getInt('id', 0);

		if (empty($id))
		{
			$this->attributes = $allAttributes;
		}
		else
		{
			$templateAttributes = THM_GroupsHelperTemplate::getTemplateAttributes($id);
			$this->attributes   = THM_GroupsHelperTemplate::assignParametersToAttributes($allAttributes, $templateAttributes);

			usort($this->attributes, array('THM_GroupsViewTemplate_Edit', 'orderSort'));
		}

		parent::display($tpl);
	}

	/**
	 * Adds items to the toolbar
	 *
	 * @return  void  modifies the JToolbar
	 */
	protected function addToolbar()
	{
		$isNew = JFactory::getApplication()->input->getInt('id', 0) == 0;
		if ($isNew)
		{
			$title         = JText::_('COM_THM_GROUPS_TEMPLATE_EDIT_NEW_TITLE');
			$closeConstant = 'JTOOLBAR_CLOSE';
		}
		else
		{
			$title         = JText::_('COM_THM_GROUPS_TEMPLATE_EDIT_EDIT_TITLE');
			$closeConstant = 'JTOOLBAR_CANCEL';
		}

		JToolBarHelper::title($title, 'test');
		JToolBarHelper::apply('template.apply');
		JToolBarHelper::save('template.save');
		if (!$isNew)
		{
			JToolBarHelper::save2copy('template.save2copy');
		}
		JToolBarHelper::save2new('template.save2new');
		JToolBarHelper::cancel('template.cancel', $closeConstant);
	}

	/**
	 * Adds styles and scripts to the document
	 *
	 * @return  void  modifies the document
	 */
	protected function modifyDocument()
	{
		parent::modifyDocument();
		JHtml::_('jquery.ui', ['core', 'sortable']);
		JHtml::script(JUri::root() . 'media/com_thm_groups/js/template_edit.js');

		JHtml::stylesheet(JUri::root() . 'media/jui/css/sortablelist.css');
		JHtml::stylesheet(JUri::root() . 'media/com_thm_groups/css/template_edit.css');
	}

	/**
	 * Sorts attributes according to their ordering property
	 *
	 * @param array $attributeOne the first attribute
	 * @param array $attributeTwo the second attribute
	 *
	 * @return int -1 if the first value should be after the second, 0 if the ordering is equal (initial state),
	 * or 1 if the first attribute should be displayed first.
	 */
	private static function orderSort($attributeOne, $attributeTwo)
	{
		if (isset($attributeOne['ordering']) AND isset($attributeTwo['ordering']))
		{
			if ($attributeOne['ordering'] == $attributeTwo['ordering'])
			{
				return 0;
			}

			return ($attributeOne['ordering'] < $attributeTwo['ordering']) ? -1 : 1;
		}

		// Neither have yet been set and they therefore have the same ordering
		return 0;
	}

	/**
	 * Renders radio button using radio layout
	 *
	 * @param   string $name         Radio button name
	 * @param   array  $attribute    Attribute object
	 * @param   int    $defaultValue Value of radio button
	 *
	 * @return  string
	 */
	public function renderRadioBtn($name, $attribute, $defaultValue)
	{
		$data            = [];
		$data['id']      = 'jform_attributes_' . str_replace(' ', '', $attribute['id']) . "_$name";
		$data['name']    = "jform[attributes][{$attribute['id']}][$name]";
		$data['class']   = 'btn-group btn-group-yesno';
		$data['default'] = $defaultValue;
		$data['options'] = [['value' => 1, 'text' => 'JYES'], ['value' => 0, 'text' => 'JNO']];
		$layout          = new JLayoutFile('radio', $basePath = JPATH_ROOT . '/media/com_thm_groups/layouts/field');

		return $layout->render($data);
	}
}
