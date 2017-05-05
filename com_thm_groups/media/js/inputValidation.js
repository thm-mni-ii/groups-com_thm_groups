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

jQuery(document).ready(function ()
{
	validateAll();
});

function validateAll()
{
	var form = document.forms['adminForm'];
	var controls = form.elements;

	for (var i = 0; i < controls.length; i++)
	{
		if ((typeof(controls[i].onchange) == 'undefined' ) || (controls[i].onchange == null))
		{
			continue;
		}
		else
		{
			controls[i].onchange();
		}
	}
}

function disable()
{
	var buttons = document.getElementsByClassName('btn-small');
	var form = document.forms['adminForm'];
	var controls = form.elements;
	var disable = false;

	for (var i = 0; i < controls.length; i++)
	{
		if ((typeof(controls[i].onchange) == 'undefined' ) || (controls[i].onchange == null))
		{
			continue;
		}
		else
		{
			var data = controls[i].getAttribute('data');
			var req = controls[i].getAttribute('data-req');
			if ((data == 'invalid') && (req == 'true'))
			{
				disable = true;
				break;
			}
		}
	}

	// Disable only all save-buttons except cancel-button
	if (disable)
	{
		for (var i = 0; i < buttons.length - 1; i++)
		{
			buttons[i].disabled = true;
		}
	}
	else
	{
		for (var i = 0; i < buttons.length; i++)
		{
			buttons[i].disabled = false;
		}
	}
}

function validateInput(regex, inputField)
{
	var field = document.getElementById(inputField);
	var input = field.value;
	var regexObj = new RegExp(regex);
	var valid = regexObj.test(input);

	//Todo: check all required fields are set to valid when save is active
	if (valid)
	{
		field.setAttribute('data', 'valid');
		field.style.cssText = "border-color: green !important; float: left !important;";
		document.getElementById(inputField + "_icon").innerHTML = "<span class='icon-publish'/>";
		jQuery("#" + inputField + "_message").empty();
	}
	else
	{
		document.getElementById(inputField + "_message").innerHTML = "";
		field.setAttribute('data', 'invalid');
		if (field.getAttribute('data-req') != 'false')
		{
			field.style.cssText = "border-color: red !important; float: left !important;";
			document.getElementById(inputField + "_icon").innerHTML = "<span class='icon-cancel'/>";
			jQuery("#" + inputField + "_message").append("</br></br><div class='text-error'>Entered value ist invalid!</div>");
		}
	}
	disable();
}

/**
 * Replaces escape sequences
 *
 * @return   String  str  Relaced String
 **/
function escapeRegExp(str)
{
	return str.replace("\\", "\\\\");
}
