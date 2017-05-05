<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @description default view template file for a joomla user list
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined("_JEXEC") or die;

class QuickpageCategoriesTemplateModal
{

	/**
	 * Method to create a list output
	 *
	 * @param   object &$view the view context calling the function
	 *
	 * @return void
	 */
	public static function render(&$view)
	{
		?>
		<form action="index.php?" id="adminForm" method="post" name="adminForm" xmlns="http://www.w3.org/1999/html">
			<div id="j-main-container" class="panel panel-primary">

				<?php echo $view->form->renderFieldset('category');
				?>
				<div class="clr"></div>
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="option"
					   value="<?php echo JFactory::getApplication()->input->get('option'); ?>"/>
				<input type="hidden" name="view" value="<?php echo $view->get('name'); ?>"/>
				<input type="hidden" name="tmpl" value="component"/>
				<?php echo JHtml::_('form.token'); ?>
				<?php echo $view->getToolbar(); ?>
			</div>
		</form>
		<?php
	}
}

QuickpageCategoriesTemplateModal::render($this);


