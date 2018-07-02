/**
 * Created by Peter on 16.02.2015.
 */

/**
 * Validates the user input of all input fields that belongs to dynamicType text or text field.
 * Regex are saved in the specific dynamicType in the database.
 * Required is saved in Json object inside the 'options' field in dynamicType entry.
 *
 * @return null
 **/

jQuery(document).ready(function () {
    validateAll();
});

function validateAll()
{
    var form = document.forms['adminForm'];
    var controls = form.elements;

    for (var i = 0; i < controls.length; i++)
    {
        if ((typeof(controls[i].onchange) === 'undefined') || (controls[i].onchange === null))
        {
            continue;
        }

        controls[i].onchange();
    }
}

function disable()
{
    var buttons = document.getElementsByClassName('btn-small'), form = document.forms['adminForm'],
        controls = form.elements, disable = false, i;

    for (i = 0; i < controls.length; i++)
    {
        if ((typeof(controls[i].onchange) === 'undefined') || (controls[i].onchange === null))
        {
            continue;
        }

        var data = controls[i].getAttribute('data');
        var req = controls[i].getAttribute('data-req');
        if ((data === 'invalid') && (req === 'true'))
        {
            disable = true;
            break;
        }
    }

    // Disable only all save-buttons except cancel-button
    if (disable)
    {
        for (i = 0; i < buttons.length - 1; i++)
        {
            buttons[i].disabled = true;
        }
    }
    else
    {
        for (i = 0; i < buttons.length; i++)
        {
            buttons[i].disabled = false;
        }
    }
}

function validateInput(regex, inputField)
{

    var field, value, regexObj, valid;

    if (regex == '')
    {
        return;
    }

    field = document.getElementById(inputField);
    value = field.value;
    regexObj = new RegExp(regex);
    valid = regexObj.test(input);

    if (valid)
    {
        field.setAttribute('data', 'valid');
        field.style.cssText = "border-color: green;";
        field.title = '';
    }
    else
    {
        field.setAttribute('data', 'invalid');

        if (field.getAttribute('data-req') !== 'false')
        {
            field.style.cssText = "border-color: red;";
            field.title = 'Invalid';
        }
    }
    disable();
}

/**
 * Replaces escape sequences
 *
 * @return   string  str  Relaced String
 **/
function escapeRegExp(str)
{
    return str.replace("\\", "\\\\");
}
