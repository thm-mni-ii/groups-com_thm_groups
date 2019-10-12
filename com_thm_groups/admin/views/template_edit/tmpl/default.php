<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
?>
<form action="<?php echo JURI::base(); ?>" enctype="multipart/form-data" method="post" name="adminForm"
      id="adminForm" class="form-horizontal form-validate">
    <div class="form-horizontal">
        <div class="span12">
            <fieldset class="form-vertical">
                <?php
                if ($this->templateID === 1) {
                    $this->get('Form')->setFieldAttribute('templateName', 'readonly', 'true');
                }
                echo $this->form->renderFieldSet('details');
                ?>
            </fieldset>
        </div>
    </div>
    <table class="attributes-sortable table-striped">
        <thead>
        <tr>
            <th><span class="hasTooltip"
                      title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_ORDER',
                          'COM_THM_GROUPS_ORDER_TIP') ?>"><?php echo JText::_('COM_THM_GROUPS_ORDER'); ?></span>
            </th>
            <th><span class="hasTooltip"
                      title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_LABEL',
                          'COM_THM_GROUPS_LABEL_DESC') ?>"><?php echo JText::_('COM_THM_GROUPS_LABEL'); ?></span>
            </th>
            <th><span class="hasTooltip"
                      title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_PUBLISHED',
                          'COM_THM_GROUPS_PUBLISHED_DESC') ?>"><?php echo JText::_('COM_THM_GROUPS_PUBLISHED'); ?></span>
            </th>
            <th><span class="hasTooltip"
                      title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_SHOW_ICON',
                          'COM_THM_GROUPS_SHOW_ICON_DESC') ?>"><?php echo JText::_('COM_THM_GROUPS_SHOW_ICON'); ?></span>
            </th>
            <th><span class="hasTooltip"
                      title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_SHOW_LABEL',
                          'COM_THM_GROUPS_SHOW_LABEL_DESC') ?>"><?php echo JText::_('COM_THM_GROUPS_SHOW_LABEL'); ?></span>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($this->attributes)) {
            echo $this->loadTemplate('rows');
        }
        ?>
        </tbody>
    </table>
    <input type="hidden" name="option" value="com_thm_groups"/>
    <?php echo $this->form->getInput('id'); ?>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value=""/>
</form>
