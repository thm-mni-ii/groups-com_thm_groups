<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

$nameAttributes = array();

if (!empty($attributes[1]) && $attributes[1]['value']) {
    $nameAttributes[2] = $attribute['value'];
}

if (!empty($attributes[2]) && $attributes[2]['value']) {
    $nameAttributes[1] = $attribute['value'];
}

$editor  = JFactory::getConfig()->get('editor');
$toolbar = $this->getToolbar();
?>
<form id="adminForm" name="adminForm" class="form-horizontal form-validate"
      action="index.php?option=com_thm_groups" method="post" enctype="multipart/form-data">
    <div class="form-horizontal">
        <?php echo $toolbar; ?>
        <div class="field-container">
            <?php
            foreach ($this->attributes as $attribute) {
                $name    = $attribute['name'];
                $value   = $attribute['value'];
                $options = empty($attribute['options']) ? [] : $attribute['options'];
                ?>
                <div class='control-group'>
                    <div class='control-label'>
                        <label id='jform_<?php echo $name; ?>-lbl'
                               for='jform_<?php echo $name; ?>'
                               aria-invalid='false'><?php echo $name; ?>
                        </label>
                    </div>
                    <div id='jform_<?php echo $name; ?>_box' class='controls'>
                        <?php
                        switch ($attribute['type']) {
                            case 'TEXTFIELD':
                                echo JEditor::getInstance($editor)->display("jform[$name][value]", $value, '', '', '',
                                    '', false);
                                break;

                            case 'PICTURE':
                                echo $this->getPicture($attribute, $nameAttributes);
                                break;

                            default:
                                echo $this->getText($attribute);
                                break;
                        }
                        ?>
                        <div>
                            <?php echo $this->getStructInput($name, 'attributeID', $attribute['structid']); ?>
                            <?php echo $this->getStructInput($name, 'type', $attribute['type']); ?>
                        </div>
                    </div>
                    <div class="publish-container">
                        <?php echo $this->getPublishBox($name, $attribute['publish']); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <input type='hidden' id='jform_groupID' name='jform[groupID]' value='<?php echo $this->groupID; ?>'/>
    <?php if (!empty($this->menuID)): ?>
        <input type='hidden' id='jform_menuID' name='jform[menuID]' value='<?php echo $this->menuID; ?>'/>
    <?php endif; ?>
    <input type='hidden' id='jform_name' name='jform[name]' value='<?php echo $this->name; ?>'/>
    <input type='hidden' id='jform_profileID' name='jform[profileID]' value='<?php echo $this->profileID; ?>'/>
    <input type='hidden' id='jform_referrer' name='jform[referrer]' value='<?php echo $this->referrer; ?>'/>
    <input type="hidden" name="option" value="com_thm_groups"/>
    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
    <?php echo $toolbar; ?>
</form>
