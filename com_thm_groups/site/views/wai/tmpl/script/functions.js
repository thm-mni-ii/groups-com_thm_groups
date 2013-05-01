function insertOptions() 
{
	var showList = document.getElementById("showList").value;
	var showAdvanced = document.getElementById("showAdvanced").value;
	var showSmallview = document.getElementById("showSmallview").value;
	var uid = document.getElementById("sel").value;
	var keyword = document.getElementById("keyword").value;
	var showListCheck = document.getElementById("showList");
	var showAdvancedCheck = document.getElementById("showAdvanced");
	var showSmallviewCheck = document.getElementById("showSmallview");
	var radioElements = document.getElementsByName("group1");
	text = "{" +keyword+ ":" +uid;
	if(showListCheck.checked)
		{
			text = text + ":"+showList;
		}
	if(showAdvancedCheck.checked)
		{
		 	text = text+ ":" +showAdvanced;
		}
	
	if(showSmallviewCheck.checked)
		{
	 		text = text+ ":" +showSmallview;
		}
	for(var j = 0 ; j < radioElements.length ; ++j) 
		{
			if(radioElements[j].checked)
				{
					var horOrVert = radioElements[j].value;
	 				text = text+ ":" +horOrVert;
				}
		}
	text = text + "}";
	window.parent.jInsertEditorText(text,getParam("e_name"));
	window.parent.SqueezeBox.close();
	return false;
}
/*
 * This function takes parameters from link
 */
function getParam(sParamName)
	{
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