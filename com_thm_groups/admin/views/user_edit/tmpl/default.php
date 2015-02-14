<?php
/**
 * @category    Joomla component
 * @package     THM_Organizer
 * @subpackage  com_thm_organizer.site
 * @name        view pool edit template
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die;
$componentDir = "/administrator/components/com_thm_groups";
$scriptDir = $componentDir . "/assets/js/";

$doc = JFactory::getDocument();

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
JHtml::_('script', JUri::root() . $componentDir . '/assets/js/cropbox.js');
$doc->addStyleSheet(JURI::root(true) . $componentDir . '/assets/css/cropbox.css');

jimport('thm_core.edit.advancedtemplate');
THM_CoreTemplateAdvanced::render($this);
?>

<script>
    jQf = jQuery.noConflict();

    /**
     * Reloads content depending on selected tab in view.
     *
     * @return null
     **/
    function checkSelectedTab(target)
    {
        var option = target.toString();
        option = option.substring(option.indexOf('#') + 1);
        var div = document.getElementById(option);
        while(div.firstChild){
            div.removeChild(div.firstChild);
        }
        reloadContent(option);
    }

    function addGroup()
    {
        var selGroupId = document.getElementById('jformNewGroup').options[document.getElementById('jformNewGroup').selectedIndex].value;
        var selGroupName = document.getElementById('jformNewGroup').options[document.getElementById('jformNewGroup').selectedIndex].text;
        var hasGroup = checkGroup(selGroupId, selGroupName);

        if (hasGroup == "true")
        {
            alert("User is allready in this group");
        }
        else
        {
            jQuery("#groups").append(hasGroup);

        }
    }

    /**
     * Adds SelectFields to new_xy-Group div
     **/
    function addRole(groupName, groupId, btnId)
    {
        var roleContainer = "new_" + groupName;
        var parentDiv = document.getElementById(roleContainer);
        var rolesDiv = document.getElementById('roles_' + groupName);
        var rolesSaved = rolesDiv.getElementsByClassName('controls').length;
        var fields = (parentDiv.getElementsByTagName('div').length);

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=user_edit&task=user_edit.addRole&cid="
            + <?php echo $this->item ?> + "&groupId=" + groupId + "&groupName=" + groupName +""
            +"&btnId=" + btnId + "&roleContainer=" + roleContainer + ""
            +"&counter=" + fields + "&rolesSaved=" + rolesSaved + "",
            async: false,
            datatype: "HTML"
        }).success(function (response){
            if (response != "false")
            {
                jQuery("#" + roleContainer).prepend(response);
            }
            else
            {
                alert("Maximum amount of roles selected");
            }
        });

    }

    /**
     * Not implemented, should save all roles.
     *
     * @param   Integer  groupId  Id of group
     * @param   String   div      Div container
     **/
    function saveRoles(groupId, div)
    {
    }

    function checkGroup(groupId, selGroupName)
    {
        var res = null;
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=user_edit&task=user_edit.checkGroup&cid="
            + <?php echo $this->item ?> + "&groupId=" + groupId + "&groupName=" + selGroupName +"",
            async: false,
            datatype: "HTML"
        }).success(function (response){
            //document.getElementById("groups").innerHTML = response;
            res = response;
        });

        return res;
    }

    function checkRole()
    {

    }

    /**
     * Adds a new group and first role in that group.
     *
     * @param  String   div         Div container
     * @param  Integer  groupId     Id of Group in database
     * @param  Integer  selFieldId  Id of select field
     *
     * @return null
     **/
    function addGroupAndRole(div, groupId, selFieldId)
    {
        var selRoleId = document.getElementById(selFieldId).options[document.getElementById(selFieldId).selectedIndex].value;
        var selRoleName = document.getElementById(selFieldId).options[document.getElementById(selFieldId).selectedIndex].text;
        var res = null;
        //TODO SAVE STUFF INTO DATABASE WHEN ROLE WAS ADDED, change button to delete and add new green add btn

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=user_edit&task=user_edit.addGroupAndRole&cid="
            + <?php echo $this->item ?> + "&groupId=" + groupId + "&roleId=" + selRoleId + "&roleName="
            + selRoleName + "",
            async: false,
            datatype: "HTML"

        }).success(function (response){
            res = response;
        });

        if (res == "true")
        {
            window.location.hash = "groups";
            location.reload(true);
        }
        else
        {
            alert("failure to add group/role");
        }
    }

    /**
     * Validates the user input of all input fields that belongs to dynamicType text or text field.
     * Regex are saved in the specific dynamicType in the database.
     * Required is saved in Json object inside the 'options' field in dynamicType entry.
     *
     * @return null
     **/
    function validateInput(regex, inputField, required)
    {
        var input = document.getElementById(inputField).value;
        var regexObj = new RegExp(regex);
        var valid = regexObj.test(input);
        var proceed = null;

        //Todo: check all required fields are set to valid when save is active
        if (valid)
        {
            if (required == 'true')
            {
                var buttons = document.getElementsByClassName('btn-small');

                for (var i=0;i<buttons.length;i++)
                {
                    buttons[i].disabled = false;
                }
            }

            document.getElementById(inputField).style.cssText = "border-color: green !important; float: left !important;";
            document.getElementById(inputField + "_icon").innerHTML = "<span class='icon-publish'/>";
            jQuery("#" + inputField + "_message").empty();

        }
        else
        {
            if (required == 'true')
            {

                var buttons = document.getElementsByClassName('btn-small');

                for (var i=0;i<buttons.length;i++)
                {
                    buttons[i].disabled = true;
                }
            }

            document.getElementById(inputField).style.cssText = "border-color: red !important; float: left !important;";
            document.getElementById(inputField + "_icon").innerHTML = "<span class='icon-cancel'/>";
            jQuery("#" + inputField + "_message").append("</br></br><div class='text-error'>Entered value ist invalid!</div>");
        }
    }

    /**
     * Replaces escape sequences
     *
     * @return   String  str  Relaced String
     **/
    function escapeRegExp(str) {
        return str.replace("\\", "\\\\");
    }

    /**
     * Reloads user content depending on selected tab
     *
     * @return null
     **/
    function reloadContent(option)
    {
        jQf.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=user_edit&task=user_edit.getUserContent&cid="
            + <?php echo $this->item->id ?> + "&tab=" + option +"",
            datatype: "HTML"

        }).success(function (response){
            jQf("#" + option).append(response);
        });
    }

    /**
     * Binds a imageCropper to the given elements of the bootstrap modal
     * created in the user_edit controller function getUserContent.
     *
     * @return null
     **/
    function bindImageCropper(element, attrID)
    {
        var options =
        {
            imageBox: '#' + element + '_imageBox',
            thumbBox: '#' + element + '_thumbBox',
            spinner:  '#' + element + '_spinner',
            imgSrc: 'avatar.png'
        }
            ,cropper = new cropbox(options)
            ,filename = null
        ;

        document.querySelector('#jform_' + element).addEventListener('change', function(){
            var reader = new FileReader();

            reader.onload = function(e) {
                options.imgSrc = e.target.result;
                cropper = new cropbox(options);
            }

            reader.readAsDataURL(this.files[0]);

            var file = this.files[0];
            filename = file.name;

            this.files = [];
        });

        document.querySelector('#' + element + '_saveChanges').addEventListener('click', function(){
            var blob = cropper.getBlob();

            var fd = new FormData();
            fd.append('fname', 'test.pic');
            fd.append('data', blob);

            jQf.ajax({
                type: "POST",
                url: "index.php?option=com_thm_groups&controller=user_edit&task=user_edit.saveCropped&id="
                + <?php echo $this->item; ?> + "&element=" + element + "&attrID=" + attrID +"&filename="
                + filename + "",
                data: fd,
                processData: false,
                contentType: false
            }).success(function(response) {
                //console.log(data);
                //document.getElementById("info").innerHTML = response;
                document.getElementById(element + "_IMG").innerHTML = response;
            });
        });

        document.querySelector('#'+ element + '_switch').addEventListener('click', function(){
            var box = document.getElementById(element + '_thumbBox');

            // Get old values:
            var style = window.getComputedStyle(box);
            var height = style.getPropertyValue('height');
            var width = style.getPropertyValue('width');

            // Set new values:
            box.style.height = width;
            box.style.width = height;
        });

        document.querySelector('#'+ element + '_btnCrop').addEventListener('click', function(){
            var img = cropper.getDataURL();
            document.querySelector('#' + element + '_cropped').innerHTML = '';
            document.querySelector('#' + element + '_cropped').innerHTML = '<img src="'+img+'">';
        });
        document.querySelector('#'+ element + '_btnZoomIn').addEventListener('click', function(){
            cropper.zoomIn();
        });
        document.querySelector('#'+ element + '_btnZoomOut').addEventListener('click', function(){
            cropper.zoomOut();
        });
    }

    jQf(document).ready(function(){

        // Load stuff once
        reloadContent("user");
        reloadContent("groups");

        if (window.location.hash != "#groups")
        {
            jQf('#myTabTabs a[href="#user"]').tab('show');
        }
        else
        {
            jQf('#myTabTabs a[href="#groups"]').tab('show');
            window.location.hash = "";
        }
    });

</script>