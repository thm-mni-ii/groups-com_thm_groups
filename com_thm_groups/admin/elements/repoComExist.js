function saveSelectedValue(){
    var select = document.getElementById("jform_params_repocomexist");
    var selected = select.selectedIndex;
    var saveSelectedValue = document.getElementById("selectedOption");
    saveSelectedValue.value = select.options[selected].value;
}
