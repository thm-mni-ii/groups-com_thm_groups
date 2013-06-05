
/*
 * This function returns checked options and generates code for THM_Content_Members plugin
 * 
 *  @param	int		count	count is a counter; count = 1 | 2 | 3; 1 - person; 2 - group; 3 - list;
 *  
 *  @return String	text	contains generated string with checked parameters		
 */
function insert(count) {

	var keyword = document.getElementById("keyword").value;	

	switch(count)
	{

	/*
	 * 1 - Person
	 * Example:
	 * {Key:person:uid:showlinks(0,1):params(200,1,left,None,left):struct(500,611,810):userlist(none)}
	 * 		Key 		String 	
	 * 		uid 		int		user ID
	 * 		showlinks	(0 | 1 | (0,1))
	 * 		params		Max-Width
	 * 					ColumnNumber
	 * 					Position
	 * 					Border
	 * 					Float
	 * 		struct		int[] comma separated
	 * 		userlist	(none) 		
	 */
	case 1: 
		var uid = document.getElementById("userID").value;	
		var bool = checkId(uid);
		if(!bool){
			return false;
		}

		text = "{" + keyword + ":" + "person:" + uid + ":";

		text = insertOptions(text,count);
		text = insertAttributes(text,count);

		text = text + "userlist(none)";

		text = text + "}";

		break;

		/*
		 * 2 - Group
		 * Example:
		 * {Key:advanced:gid:showlinks(0,1):params(200,1,left,None,left):struct(500,611,810):userlist(none)}
		 * 	oder
		 * {Key:advanced:gid:showlinks(0,1):params(200,1,left,None,left):struct(500,611,810):userlist(1,2,3,4,5)}
		 * 		Key 		String
		 * 		gid 		int		group ID
		 * 		showlinks	(0 | 1 | (0,1))
		 * 		params		Max-Width
		 * 					ColumnNumber
		 * 					Position
		 * 					Border
		 * 					Float
		 * 		struct		int[] comma separated
		 * 		userlist	((none) | (id of users comma separated))
		 * 		
		 */

	case 2:
		var gid = document.getElementById("groups").value;
		var bool = checkId(gid);
		if(!bool){
			return false;
		}
		
		text = "{" + keyword + ":" + "advanced:" + gid + ":";

		text = insertOptions(text,count);
		text = insertAttributes(text,count);

		var boolSingleUser = "";
		boolSingleUser = document.getElementById("single_user").value;

		if(boolSingleUser == "false") {

			text = text + "userlist(none)";
		}

		else 
		{
			text = text + "userlist(" + getMultipleSelectValues("selectTo") + ")"; 
		}

		text = text + "}";

		break;

		/*
		 * 3 - List
		 * Example:
		 * {Key:list:gid:showlinks(0,1):viewall:ColumnNumber:ordering(1,2,3):showtitel}
		 * 		Key 		String
		 * 		gid 		int
		 * 		showlinks	(0 | 1 | (0,1))
		 * 		viewall		(0 | 1)
		 * 		ColumnNumer int
		 * 		ordering	((1,2,3) | (1,3,2) | (3,2,1) | and so on)
		 * 		showtitel	(0 | 1)
		 */

	case 3:
		var gid = document.getElementById("groups_list").value;
		var bool = checkId(gid);
		if(!bool){
			return false;
		}
		
		text = "{" + keyword + ":" + "list:" + gid + ":";

		text = insertListOptions(text);

		text = text + "}";

		break;
	default:
		break;
	}

	// returns text into editor
	window.parent.jInsertEditorText(text,getParam("e_name"));
	window.parent.SqueezeBox.close();
	return false;
}

function checkId(id){
	if(id == ""){
		alert("Sie haben keine Person/Gruppe ausgewählt!");
		return false;
	}
	return true;
}


/*
 * Returns checked options for LIST-View (from "ShowLinks" to "Title")
 * 
 * @param	String	text	contains previous text
 * 
 * @return  String	text	contains parameters only for list view
 */
function insertListOptions(text){

	text = text + "showlinks(";

	text = text + getCheckedCheckboxes("listLink"); 

	text = text + "):";

	text = text + getCheckedRadioButton("alphabet") + ":";

	text = text + getInput("list_column") + ":";

	text = text + "ordering(" + getMultipleSelectValues("paramsattr") + "):";

	text = text + getCheckedRadioButton("title");

	return text;
}

/*
 * This function checks options like "float", "position", "width", "column" and "Border"
 * 
 * @param	String	text	text contains all previous changes
 * 
 * @param	int		count	count is a counter; count = 1 | 2; 1 - person; 2 - group;
 * 
 * @return 	String	text 	contains new checked options
 */
function insertOptions(text,count){
	var countPosition ="default";
	var countBorder ="default";
	var countFloat ="default";
	var countWidth ="default";
	var countLink ="default";
	var countColumn ="default";
	switch (count)
	{
	//	person
	case 1:
		countPosition = "personPosition";
		countBorder = "personBorder";
		countFloat = "personFloat";
		countWidth = "personDivWidth";
		countLink = "personLink";
		break;
	//  group
	case 2:
		countPosition = "groupPosition";
		countBorder = "groupBorder";
		countFloat = "groupFloat";
		countWidth = "groupDivWidth";
		countColumn = "group_column";
		countLink = "groupLink";
		break;
	}

	//	person
	if(countColumn == "default"){

		text = text + "showlinks(";

		text = text + getCheckedCheckboxes(countLink);

		text = text + "):";

		text = text + "params(";

		text = text + getInput(countWidth) + ",";
	}
	//	group
	else{

		text = text + "showlinks(";

		text = text + getCheckedCheckboxes(countLink);

		text = text + "):";

		text = text + "params(";

		text = text + getInput(countWidth) + "," + getInput(countColumn) + ",";
	}

	//	position
	text = text + getInput(countPosition) + ",";

	//	border
	text = text + getInput(countBorder) + ",";

	//	float
	text = text + getInput(countFloat);

	text = text + "):";

	return text;
}

/*
 * This function returns checked attributes like Titel, Name, Vorname, Telefonnummer
 * 
 * @param	String	text	text contains all previous changes
 * 
 * @param	int		count	count is a counter; count = 1 | 2; 1 - person; 2 - group;
 * 
 * @return 	String	text 	contains previous parameters and new checked attributes
 */
function insertAttributes(text,count){

	var cbToCheck = "";
	var tempVariable;
	var number = 1;

	switch (count){
	case 1: 
		// for person view
		cbToCheck = "cola";
		number = 1;
		break;
	case 2:
		// for group view
		cbToCheck = "cold";
		number = 100;
		break;
	}

	text = text + "struct(";

	var buttonvalueCheck = document.getElementsByName(cbToCheck);

	for( var i =0; i< buttonvalueCheck.length; i++){
		if( buttonvalueCheck[i].checked ){
			tempVariable = buttonvalueCheck[i].value;
			tempVariable /= number;
			text = text +","+ tempVariable;
		}
	}

	text = text + "):";
	return text;
}

/*
 * Returns selected values from Multiple Select
 * 
 * @param	String	id		id of a select for check
 * 
 * @return	String 	text	text contains all values of select with ID = "id"
 */
function getMultipleSelectValues(id){
	var text = "";
	var selectBox = document.getElementById(id);
	for(var i = 0; i < selectBox.length; i++){
		if(selectBox[i].value){
			text = text + selectBox[i].value + ",";
		}
	}
	return text;
}

/*
 * Returns checked checkboxes
 * 
 * @param	String 	name	name of elements for check
 * 
 * @return 	String 	text	text contains all values of checked checkboxes
 */
function getCheckedCheckboxes(name){
	var text = "";
	var checkbox = document.getElementsByName(name);
	for( var i = 0; i < checkbox.length; i++){
		if(checkbox[i].checked){
			text = text + checkbox[i].value + ",";
		}
	}
	if (text == ""){
		text = "default";
	}
	return text;
}

/*
 * Returns checked radio button
 * 
 * @param	String	name	name of an element for check
 * 
 * @return	String 	text	text contains checked radio button
 */
function getCheckedRadioButton(name){
	var text = "";
	var radio = document.getElementsByName(name);
	for( var i = 0; i < radio.length; i++){
		if(radio[i].checked){
			text = text + radio[i].value;
		}
	}
	return text;
}

/*
 * Returns a value from input 
 * 
 * @param	String	id	id of an element for check
 * 
 * @return	String	returns a value of input
 */
function getInput(id){
	return 	document.getElementById(id).value;
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
function onname(count, name){
	var x = "";
	var y = "";
	switch (count)
	{
	case 1 :
		x = name + 10;
		y = name + 1;
		break;
	case 2: 
		x = name + 1000;
		y = name + 100;
		break;
	}
	if(document.getElementById(name).checked==true)
	{
		console.log(name);
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
function incrementOnTheShow(count, id){
	var x = 0;
	var y = 0;
	var valueForwBack = 0;
	switch (count)
	{
	case 1:
		x = id + 10;
		y = id + 1;
		valueForwBack = 10;
		break;
	case 2:
		x = id + 1000;
		y = id + 100;
		valueForwBack = 1000;
		break;
	}

	if(document.getElementById(id).checked==true && document.getElementById(y).checked==false)
	{
		if(document.getElementById(x).checked == true)
		{
			document.getElementById(id).value = parseInt(document.getElementById(id).value) + valueForwBack;
		}
		else
		{
			document.getElementById(id).value = parseInt(document.getElementById(id).value) - valueForwBack;
		}
	}
	if(document.getElementById(id).checked==true && document.getElementById(y).checked==true)
	{
		if(document.getElementById(x).checked==true)
		{
			document.getElementById(id).value = parseInt(document.getElementById(y).value) + valueForwBack;
		}
		else
		{
			document.getElementById(id).value = parseInt(document.getElementById(id).value) - valueForwBack;
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
function incrementOnTheWrap(count, id){
	var valueForwBack = 0;
	var x = 0;
	switch (count)
	{
	case 1:
		x = id + 1;
		valueForwBack = 1;
		break;
	case 2:
		x = id + 100;
		valueForwBack = 100;
		break;
	}

	var y = parseInt( document.getElementById(id).value);

	if(document.getElementById(x).checked==true){
		x = y + valueForwBack;
		document.getElementById(id).value = x;
	}
	else{
		x = y - valueForwBack;
		document.getElementById(id).value = x;
	}
}

/*
 * checks all Checkboxes of a column
 * 
 * @Unused
 */
function jqCheckAll2( id, name )
{
	$("INPUT[name=" + name + "][type='checkbox']").attr('checked', $('#' + id).is(':checked'));
}
