/**
 * Created by Peter on 23.02.2015.
 */

function getTypeOptions(){
    var selectedOption = document.getElementById('staticType').options[document.getElementById('staticType').selectedIndex].text;
    var selectedID = document.getElementById('staticType').options[document.getElementById('staticType').selectedIndex].value;

    if(selectedID != document.getElementById('static').value)
    {
        var isActType = false;
    }
    else
    {
        var isActType = true;
    }

    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_thm_groups&controller=dynamic_type_edit&task=dynamic_type_edit.getTypeOptions" +
        "&tmpl=component&cid=" + document.getElementById('dyn').value +"&selected=" + selectedOption +
        "&isActType=" + isActType + "",
        datatype: "HTML"
    })
        .success(function (response) {
            document.getElementById("ajax-container").innerHTML = response;
        });

    reloadTypeRegexOptions(selectedID, isActType);
}

jQuery(document).ready(function(){
    getTypeOptions();
});