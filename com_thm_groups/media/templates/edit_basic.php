<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsTemplateEdit_Basic
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class provides standardized output of list items
 *
 * @category    Joomla.Component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 */
class THM_GroupsTemplateEdit_Basic
{
	/**
	 * Method to create a form output based solely on the xml configuration.
	 *
	 * @param   object &$view the view context calling the function
	 *
	 * @return void
	 */
	public static function render(&$view)
	{
		?>
		<form action="index.php?option=com_thm_groups"
			  enctype="multipart/form-data"
			  method="post"
			  name="adminForm"
			  id="item-form"
			  class="form-horizontal form-validate">
			<fieldset class="adminform">
				<?php echo $view->form->renderFieldset('details'); ?>
			</fieldset>
			<?php echo $view->form->getInput('id'); ?>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value=""/>
		</form>
		<?php
	}
}
