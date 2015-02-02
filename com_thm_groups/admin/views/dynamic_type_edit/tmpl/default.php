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

// Script include changed in 3.x
JHTML::_('script', Juri::root() . $scriptDir . 'jquery-1.9.1.min.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jquery.easing.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jqueryFileTree.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jquery-ui-1.9.2.custom.js');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root(true) . "/libraries/thm_groups/assets/elements/explorer.css");
$doc->addStyleSheet(JURI::root(true) . $componentDir . "/assets/css/jqueryFileTree.css");
?>

<script>

    function getRegex(){
        var selected = document.getElementById('jform_regex_select').options[document.getElementById('jform_regex_select').selectedIndex].value;

        /**
         * The user can type in a custom regex when
         * 'Other' is selected in the regex menu
         */
        if (selected != 'Other')
        {
            document.getElementById("jform_regex").disabled = true;
            document.getElementById("jform_regex").value = selected;
        }
        else
        {
            document.getElementById("jform_regex").disabled = false;
            document.getElementById("jform_regex").value = "";
        }

    }

    function showFTree(){
        var jRoot = '<?php
                        $rep = JPATH_ROOT;
                        $path = str_replace(array('\\'), array('/'), $rep);
                        echo $path . '/images/';
                        ?>';

        document.getElementById('fileBrowser').style.visibility = 'visible';

        jQuery(function(){
            jQuery('#fileBrowser').draggable();
        });

        jQuery('#fileBrowserInnerContent').fileTree({root: jRoot, script:'<?php echo Juri::root(),
        $componentDir,"/elements/jqueryFileTree.php"; ?>'}, function(file){
            getFile(file);
        }, function(dire){
            getDir(dire);
        });
    }

    function hideFTree(){
        document.getElementById('fileBrowser').style.visibility = 'hidden';
    }

    function getFile(file){
        var fileName = /[^/]*$/.exec(file)[0];
        //var fileNameSize = fileName.length;

        //TODO optimize selection for allowed picture formats
        if (((fileName.match('.jpg')||fileName.match('.png'))||fileName.match('gif'))||fileName.match('jpeg')){
            document.getElementById('PICTURE_name').value = fileName;
        }
        else
        {
            document.getElementById('PICTURE_name').value = 'anonym.jpg';
        }

        //document.getElementById('PICTURE_path').value = file.substr(0,(file.length - fileNameSize));
    }

    function getDir(dire){
        document.getElementById('PICTURE_path').value = dire;
        document.getElementById('PICTURE_name').value = 'anonym.jpg';
    }

    function getTypeOptions(){
        var selectedOption = document.getElementById('staticType').options[document.getElementById('staticType').selectedIndex].text;
        var selectedID = document.getElementById('staticType').options[document.getElementById('staticType').selectedIndex].value;

        if(selectedID != <?php echo $this->item->static_typeID; ?>){
            var isActType = false;
        }else{
            var isActType = true;
        }

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=dynamic_type_edit&task=dynamic_type_edit.getTypeOptions&cid="
            + <?php echo $this->item->id; ?> + "&selected=" + selectedOption +
                 "&isActType=" + isActType +"",
            datatype: "HTML"
        })
            .success(function( response ) {
                document.getElementById("ajax-container").innerHTML = response;
            });

        reloadTypeRegexOptions(selectedID, isActType);

    }

    function reloadTypeRegexOptions(selectedID, isActType){
        document.getElementById("jform_regex").disabled = false;

        if (!isActType)
        {
            document.getElementById("jform_regex").value = "";
        }

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=dynamic_type_edit&task=" +
            "dynamic_type_edit.reloadTypeRegexOptions&selectedID=" + selectedID +"",
            datatype: "HTML"
        })
            .success(function(response){
                document.getElementById("regexSelectField").innerHTML = response;
            });
    }

    jQuery(document).ready(function(){
        getTypeOptions();
    });

</script >

<!--<form action='index.php?option=com_thm_groups' method="post" name="adminForm" id="adminForm" accept-charset="UTF-8">-->
<form action='index.php?option=com_thm_groups' method="post" name="adminForm" id="adminForm" accept-charset="UTF-8">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo 'TEST LEGEND'; ?></legend>
            <!--<div class="row-fluid">
                <div class="span6">
                    <?php /*foreach ($this->form->getFieldset() as $field): */ ?>
                        <div class="control-group">
                            <div class="control-label"><?php /*echo $field->label; */ ?></div>
                            <div class="controls"><?php /*echo $field->input; */ ?></div>
                        </div>

                    <?php /*endforeach; */ ?>
                </div>
            </div>-->

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
                <span id="ajax-container">
                </span>
            </div>
        </fieldset>

        <!-- Hidden field for ID -->
        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>