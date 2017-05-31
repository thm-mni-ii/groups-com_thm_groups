<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.html.editor');

$session = JFactory::getSession();
?>
<script>jQf = jQuery.noConflict();</script>

<form id="adminForm" name="adminForm" class="form-horizontal"
	  action="index.php?option=com_thm_groups" method="post" enctype="multipart/form-data">
	<div class="form-horizontal">
		<div id="user" class="tab-pane active">
            <?php
            foreach ($this->attributes as $attribute):
            $name    = $attribute['name'];
            $value   = $attribute['value'];
            $options = empty($attribute['options']) ? new stdClass : json_decode($attribute['options']);
            ?>
			<div class='control-group'>
				<div class='control-label'>
					<label id='jform_<?php echo $name; ?>-lbl'
						   class=''
						   for='jform_<?php echo $name; ?>'
						   aria-invalid='false'><?php echo $name; ?>
					</label>
				</div>
				<div id='jform_<?php echo $name; ?>_box' class='controls'>
                    <?php
                    switch ($attribute['type'])
                    {
                        case 'TEXTFIELD':
                            $editor = JFactory::getEditor();
                            echo $editor->display("jform[$name][value]", $value, '', '', '', '', false);
                            break;

                        case 'MULTISELECT':
                            echo $this->getSelect($attribute);
                            break;

                        case 'TABLE':
                            $tableData = json_decode($value, true);
                            echo $this->getTable($name, $tableData);
                            break;

                        case 'PICTURE':
                            echo $this->getPicture($attribute);
                            break;

                        default:
                            echo $this->getText($attribute);
                            break;
                    }
                    ?>
					<div id='jform_<?php echo $name; ?>_icon' class="validation-container"
					'>
				</div>
				<div>
                    <?php echo $this->getStructInput($name, 'attributeID', $attribute['structid']); ?>
                    <?php echo $this->getStructInput($name, 'type', $attribute['type']); ?>
				</div>
				<div id='jform_<?php echo $name; ?>_message'/>
			</div>
			<div id='info'></div>
		</div>
		<div class="publish-container">
            <?php echo $this->getPublishBox($name, $attribute['publish']); ?>
		</div>
	</div>
    <?php endforeach; ?>
	</div>
	</div>
	</div>
	<input type="hidden" name="option" value="com_thm_groups"/>
	<input type="hidden" name="task" value="user.apply"/>
	<input type='hidden' id='jform_userID' name='jform[userID]' value='<?php echo $this->userID; ?>'/>
    <?php if (!empty($this->menuID)): ?>
		<input type='hidden' id='jform_menuID' name='jform[menuID]' value='<?php echo $this->menuID; ?>'/>
    <?php endif; ?>
    <?php echo JHtml::_('form.token'); ?>
</form>
