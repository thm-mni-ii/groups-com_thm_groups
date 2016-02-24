/**
 * Created by Peter on 25.02.2015.
 */
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