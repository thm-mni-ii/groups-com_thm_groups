
/*
 * This function returns checked options and generates code for another plugin 
 */
function insert() {
	var uid = document.getElementById("sel").value;

	var keyword = document.getElementById("keyword").value;

	text = "{" + keyword + ":" + "mode(person):" + uid + ":" + "view(none):";

	text = insertOptions(text);
	text = insertAttributes(text);

	text = text + "userlist(none)";

	text = text + "}";

	window.parent.jInsertEditorText(text,getParam("e_name"));
	window.parent.SqueezeBox.close();
	return false;
}


/*
 * This function checks options like "float", "position" and "Border"
 */
function insertOptions(text){
	var position = document.getElementsByName("personPosition");
	var border = document.getElementsByName("personBorder");
	var float = document.getElementsByName("personFloat");

	text = text + "params(";

	for( var i =0; i< position.length; i++){
		if( position[i].checked ){
			text = text + position[i].value + ",";
		}
	}

	for( var i =0; i< float.length; i++){
		if( float[i].checked ){
			text = text + float[i].value + ",";
		}
	}

	for( var i =0; i< border.length; i++){
		if( border[i].checked ){
			text = text + border[i].value;
		}
	}
	text = text + "):";
	return text;
}

/*
 * This function returns checked Attributes
 */
function insertAttributes(text){

	text = text + "struct(";

	var buttonvalueCheck = document.getElementsByName("strucktur");

	for( var i =0; i< buttonvalueCheck.length; i++){
		if( buttonvalueCheck[i].checked ){
			text = text +","+ buttonvalueCheck[i].value;
		}
	}

	text = text + "):";
	return text;
}
/*
 * This function takes parameters from link
 *
 * @param  String  sParamName  sParamName is a name of parameter, that we need
 *
 * @return a value of the parameter 
 */
function getParam(sParamName) {
	var Params = location.search.substring(1).split("&");

	var variable = "";

	for (var i = 0; i < Params.length; i++)
	{
		if (Params[i].split("=")[0] == sParamName)
		{
			if (Params[i].split("=").length > 1) variable = Params[i].split("=")[1]; 
			return variable;
		}
	}
	return "";
}
/*
 *  This function opens next checkboxes in the row
 *  
 *  @param Integer  name  name is a number, that is an id of a first checkbox in a column
 */
function onname(name){
	var x = name + 10;
	var y = name + 1;
	if(document.getElementById(name).checked==true)
	{
		document.getElementById(x).disabled=false;
		document.getElementById(y).disabled=false;
	}
	if(document.getElementById(name).checked==false)
	{

		document.getElementById(x).disabled=true;
		document.getElementById(x).checked=false;
		document.getElementById(y).disabled=true;
		document.getElementById(y).checked=false;
		document.getElementById(name).value = name;
	}
}

/*
 *  This function increments a value of the first checkbox in the row, if the second checkbox was checked.
 *  For example, a value of the first checkbox will be 110, it means, that first and second 
 *  checkboxes were checked.
 *  A value 100 means, that only a first checkbox was checked.
 *  
 *  @param  Integer  id  id is a number of the first checkbox in the row
 *  
 */
function incrementOnTheShow(id){
	var x = id + 10;
	var y = id + 1;

	if(document.getElementById(id).checked==true && document.getElementById(y).checked==false)
	{
		if(document.getElementById(x).checked == true)
		{
			document.getElementById(id).value = parseInt(document.getElementById(id).value) + 10;
		}
		else
		{
			document.getElementById(id).value = parseInt(document.getElementById(id).value) - 10;
		}
	}
	if(document.getElementById(id).checked==true && document.getElementById(y).checked==true)
	{
		if(document.getElementById(x).checked==true)
		{
			document.getElementById(id).value = parseInt(document.getElementById(y).value) + 10;
		}
		else
		{
			document.getElementById(id).value = parseInt(document.getElementById(id).value) - 10;
		}
	}

}

/*
 *  This function increments a value of the first checkbox in the row, if the third checkbox was checked.
 *  For example, a value of the first checkbox will be 111 it means, that first, second and third 
 *  checkboxes were checked.
 *  A value 101 means, that only  first and third checkboxes were checked.
 *  And so on.
 *  For more comments just write to me: ilja.michajlow@min.thm.de 
 *  
 *  @param  Integer  id  id is a number of the first checkbox in the row
 *  
 */
function incrementOnTheWrap(id){
	var x = id + 1;
	var y = parseInt( document.getElementById(id).value);

	if(document.getElementById(x).checked==true){
		x = y + 1;
		document.getElementById(id).value = x;
	}
	else{
		x = y - 1;
		document.getElementById(id).value = x;
	}
}

/*
 * This function changes the value "display" of elements
 */
function magic(id){
	switch (id)
	{
	case 'personOptions':
		document.getElementById("THM_Plugin_Members_GroupOptions").style.display = 'none';
		document.getElementById("THM_Plugin_Members_Options").style.display = 'block';
		document.getElementById("THM_Plugin_Members_SelectMenu").style.display = 'block';
		document.getElementById("THM_Plugin_Members_DivForContentOptions").style.display = 'block';
		document.getElementById("THM_Plugin_Members_Tipp1").style.display = 'block';
		document.getElementById("THM_Plugin_Members_Tipp2").style.display = 'block';
		document.getElementById("THM_Plugin_Members_AddButton").style.display = 'block';
		
		//var div = document.getElementById("THM_Plugin_Members_Tipp");
		//var content = document.createTextNode("Tipp:Neo...wake up..Matrix has you...");
		//div.appendChild(content);
		break;
	case 'groupOptions':
		document.getElementById("THM_Plugin_Members_Options").style.display = 'none';
		document.getElementById("THM_Plugin_Members_SelectMenu").style.display = 'none';
		document.getElementById("THM_Plugin_Members_DivForContentOptions").style.display = 'none';
		document.getElementById("THM_Plugin_Members_Tipp2").style.display = 'none';
		document.getElementById("THM_Plugin_Members_AddButton").style.display = 'none';
		document.getElementById("THM_Plugin_Members_GroupOptions").style.display = 'block';
		break;
	}
}


/*
 * Not in use...
 * 
 * This function is needed for ajax requests
 */
function showUser()
{

	if(document.getElementById("THM_Plugin_Members_GroupOptions").style.display == 'none'){
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		document.getElementById("THM_Plugin_Members_SelectMenu").innerHTML=xmlhttp.responseText;
		xmlhttp.open("GET","../components/com_thm_groups/views/members/tmpl/ajax/eventHandler.php?type=get",true);
		xmlhttp.send();
	}
}
