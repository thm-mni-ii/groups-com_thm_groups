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
jimport('thm_core.edit.view');
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
		$user      = JFactory::getUser();
		$canCreate = $user->authorise('core.create', 'com_thm_groups');
		$canEdit   = $user->authorise('core.edit', 'com_thm_groups');
		$hasAccess = ($canCreate OR $canEdit);
		if (!$hasAccess)
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$allAttributes = THM_GroupsHelperProfile::getAllAttributes();
		$id    = JFactory::getApplication()->input->getInt('id', 0);
		if (empty($id))
		{
			$this->attributes = $allAttributes;
		}
		else
		{
			$templateAttributes = THM_GroupsHelperTemplate::getTemplateAttributes($id);
			$this->attributes   = THM_GroupsHelperTemplate::assignParametersToAttributes($allAttributes, $templateAttributes);
			usort(
				$this->attributes,
				function ($a, $b)
				{
					if (isset($a->order) AND isset($b->order))
					{
						if ($a->order == $b->order)
						{
							return 0;
						}

						return ($a->order < $b->order) ? -1 : 1;
					}
				}
			);
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
		JHtml::_('jquery.ui');
		JHtml::_('jquery.ui', ['sortable']);
		JHtml::script(JUri::root() . 'media/jui/js/sortablelist.js');
		JHtml::stylesheet(JUri::root() . 'media/jui/css/sortablelist.css');
		JHtml::script(JUri::root() . 'media/com_thm_groups/js/template_edit.js');
		JHtml::stylesheet(JUri::root() . 'media/com_thm_groups/css/template_edit.css');
	}

	/**
	 * Renders radio button using radio layout
	 *
	 * @param   string $name         Radio button name
	 * @param   object $attribute    Attribute object
	 * @param   int    $defaultValue Value of radio button
	 *
	 * @return  string
	 */
	public function renderRadioBtn($name, $attribute, $defaultValue)
	{
		$data = [];
		$data['id'] = 'jform_attributes_' . str_replace(' ', '', $attribute->id) . "_$name";
		$data['name'] = "jform[attributes][$attribute->id][$name]";
		$data['class'] = 'btn-group btn-group-yesno';
		$data['default'] = $defaultValue;
		$data['options'] = [['value' => 1, 'text' => 'JYES'], ['value' => 0, 'text' => 'JNO']];
		$layout = new JLayoutFile('radio', $basePath = JPATH_ROOT . '/media/com_thm_groups/layouts/field');

		return $layout->render($data);
	}
}
