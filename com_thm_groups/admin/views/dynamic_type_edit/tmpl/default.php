<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewDynamic_Type_Edit
 * @description THMGroupsViewDynamic_Type_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');

$scriptDir = "libraries/thm_groups/assets/js/";
$componentDir = "/administrator/components/com_thm_groups";

JHTML::_('script', Juri::root() . $scriptDir . 'jquery-1.9.1.min.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jquery.easing.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jqueryFileTree.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jquery-ui-1.9.2.custom.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/dynamic.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/reloadRegex.js');

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root(true) . "/libraries/thm_groups/assets/elements/explorer.css");
$doc->addStyleSheet(JURI::root(true) . $componentDir . "/assets/css/jqueryFileTree.css");
?>

<form action='index.php?option=com_thm_groups' method="post" name="adminForm" id="adminForm" accept-charset="UTF-8">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <div class="control-label">
                <div class="control-label">
                    <?php echo $this->form->getLabel('name'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('name'); ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('staticTypeName'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->selectFieldStaticTypes ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('regex'); ?>
                </div>
                <div id="regexSelect" class="controls">
                    <?php echo $this->form->getInput('regex'); ?>
                    <span id="regexSelectField">
                        <?php
                            if ($this->regexOptions != null)
                            {
                                echo $this->regexOptions;
                            }
                        ?>
                    </span>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('description'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('description'); ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('additional'); ?>
                </div>
                <div class="controls">
                    <span id="ajax-container">
                    <?php echo $this->form->getInput('additional'); ?>
                    </span>
                </div>
            </div>
        </fieldset>

        <!-- Hidden field for ID -->
        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="dyn" id="dyn" value="<?php echo $this->item->id ?>"/>
        <input type="hidden" name="static" id="static" value="<?php echo $this->item->static_typeID ?>"/>
        <input type="hidden" name="fpath" id="fpath" value="<?php echo $this->fileTreePath ?>"/>
        <input type="hidden" name="path" id="path" value="<?php echo $this->path ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>