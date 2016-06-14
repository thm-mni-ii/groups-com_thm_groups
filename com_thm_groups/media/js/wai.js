var globalIndex = 0,
    globalLeftIndex = 0,
    globalRightIndex = 0,
    globalPlaceholder = "";

//Foreach for array
Array.prototype.foreach = function (callback)
{
    for (var k = 0; k < this.length; k++)
    {
        callback(k, this[k]);
    }
};

/*
 * Decides, edit an old placeholder or create a new placeholder
 */
window.onload = function chooseStrategy()
{
    if (checkContentForPlaceholder())
    {
        loadParametersFromPlaceholderToPopUp();
    }
};

/*
 * Checks, if in content a placeholder exists
 *
 * @return	boolean
 */
function checkContentForPlaceholder()
{
    var editor_element = window.parent.tinyMCE.get('jform_articletext');
    var index = getCursorPosition(editor_element);
    var content = window.parent.tinyMCE.get('jform_articletext').getContent();

    for (var i = index; i > 0; i--)
    {
        if (content.charAt(i) == "}")
        {
            return false;
        }
        if (content.charAt(i) == "{")
        {
            return true;
        }
    }

    for (var j = index; j < content.length; j++)
    {
        if (content.charAt(j) == "{")
        {
            return false;
        }
        if (content.charAt(i) == "}")
        {
            return true;
        }
    }
}

/*
 * Returns a cursor position in editor
 * Function from http://blog.squadedit.com/tinymce-and-cursor-position/
 *
 * @param	String	editor	contains editor object
 *
 * @return  int		index	contains an index of a cursor in editor
 */
function getCursorPosition(editor)
{
    //set a bookmark so we can return to the current position after we reset the content later
    var bm = editor.selection.getBookmark(0);

    //select the bookmark element
    var selector = "[data-mce-type=bookmark]";
    var bmElements = editor.dom.select(selector);

    //put the cursor in front of that element
    editor.selection.select(bmElements[0]);
    editor.selection.collapse();

    //add in my special span to get the index...
    //we won't be able to use the bookmark element for this because each browser will put id and class attributes in different orders.
    var elementID = "######cursor######";
    var positionString = '<span id="' + elementID + '"></span>';
    editor.selection.setContent(positionString);

    //get the content with the special span but without the bookmark meta tag
    var content = editor.getContent({format: "html"});
    //find the index of the span we placed earlier
    var index = content.indexOf(positionString);

    //remove my special span from the content
    editor.dom.remove(elementID, false);

    //move back to the bookmark
    editor.selection.moveToBookmark(bm);

    return index;
}

/*
 * Sets a cursor position in editor
 * Function from http://blog.squadedit.com/tinymce-and-cursor-position/
 *
 * @param	String	editor	contains editor object
 * @param	int		index	number of a new cursor position
 */
function setCursorPosition(editor, index)
{
    //get the content in the editor before we add the bookmark...
    //use the format: html to strip out any existing meta tags
    var content = editor.getContent({format: "html"});

    //split the content at the given index
    var part1 = content.substr(0, index);
    var part2 = content.substr(index);

    //create a bookmark... bookmark is an object with the id of the bookmark
    var bookmark = editor.selection.getBookmark(0);

    //this is a meta span tag that looks like the one the bookmark added... just make sure the ID is the same
    var positionString = '<span id="' + bookmark.id + '_start" data-mce-type="bookmark" data-mce-style="overflow:hidden;line-height:0px"></span>';
    //cram the position string inbetween the two parts of the content we got earlier
    var contentWithString = part1 + positionString + part2;

    //replace the content of the editor with the content with the special span
    //use format: raw so that the bookmark meta tag will remain in the content
    editor.setContent(contentWithString, ({format: "raw"}));

    //move the cursor back to the bookmark
    //this will also strip out the bookmark metatag from the html
    editor.selection.moveToBookmark(bookmark);

    //return the bookmark just because
    return bookmark;
}

function loadParametersFromPlaceholderToPopUp()
{
    var placeholder = "";
    var ph_obj = "";
    placeholder = cutPlaceholderFromContent();
    console.log(placeholder);
    parsePlaceholder(placeholder);
}

function parsePlaceholder(ph)
{
    var array = "";
    array = ph.split(":");

    $("#sel").val(array[1]).attr("selected", true);

    array.foreach(function (index, value)
    {
        switch (value)
        {
            case "showlist":
                $("#showList").attr("checked", true);
                break;
            case "showadvanced":
                $("#showAdvanced").attr("checked", true);
                break;
            case "showsmallview":
                $("#showSmallview").attr("checked", true);
                break;
            case "vertical":
                $("#ver").attr("checked", true);
                break;
            case "horizontal":
                $("#hor").attr("checked", true);
                break;
        }
    });
}

/*
 * Cuts a placeholder from content
 *
 * @return	String	placeholder	text with parameters
 */
function cutPlaceholderFromContent()
{
    var editor_element = window.parent.tinyMCE.get('jform_articletext');
    var index = getCursorPosition(editor_element);
    var returnedValues = [];
    var left = "";
    var right = "";
    var placeholder = "";

    returnedValues = findTextBeforeLeftBrace(index);

    // returnedValues[0] = left part of placeholder
    left = returnedValues[0];

    // returnedValues[1] = new cursor position in editor
    // right = findTextBeforeRightBrace(returnedValues[1]);
    right = findTextBeforeRightBrace(index);

    placeholder = left + right;
    globalPlaceholder = placeholder;

    return placeholder;
}

/*
 * Pastes a new content into editor
 *
 * @param	String	content		contains a new content for editor
 */
function setNewContentInEditor(content)
{
    window.parent.tinyMCE.get('jform_articletext').setContent('');
    window.parent.tinyMCE.get('jform_articletext').setContent(content);
}

/*
 *  Reads text before left brace and then deletes this part from content in editor
 *
 *  @param	int		index		a position of a cursor in the editor by opening
 *
 *  @return	array	returnValue	contains cutted text and a new index of a cursor in the editor
 */
function findTextBeforeLeftBrace(index)
{
    var editor_element = window.parent.tinyMCE.get('jform_articletext');
    var content = window.parent.tinyMCE.get('jform_articletext').getContent();
    var left_reversed = "";
    var left = "";
    var returnValues = [];

    for (var i = index - 1; i > 0; i--)
    {
        if (content.charAt(i) != "{")
        {
            left_reversed = left_reversed + content.charAt(i);
        }
        else
        {
            //!!!content = sliceContent(content, i, index);
            window.globalLeftIndex = i;
            break;
        }
    }

//	setNewContentInEditor(content);

//	setCursorPosition(editor_element, i);
    window.globalIndex = i;
    console.log(window.globalLeftIndex);

    // reverse string
    left = left_reversed.split("").reverse().join("");
    returnValues[0] = left;
    returnValues[1] = i;

    return returnValues;
}

/*
 * Reads the text before right brace and then deletes this part from content in editor
 *
 * @param	int		index	a position of a cursor in the editor
 *
 * @return	String	right	a cutted text before right brace
 */
function findTextBeforeRightBrace(index)
{
    var content = window.parent.tinyMCE.get('jform_articletext').getContent();
    var right = "";
    for (var i = index; i < content.length; i++)
    {
        if (content[i] != "}")
        {
            right = right + content[i];
        }
        else
        {
//			content = sliceContent(content, index, i);
            window.globalRightIndex = i;
            break;
        }
    }

    console.log(window.globalRightIndex);

//	setNewContentInEditor(content);

    return right;
}

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

    text = "{" + keyword + ":" + uid;

    if (showListCheck.checked)
    {
        text = text + ":" + showList;
    }
    if (showAdvancedCheck.checked)
    {
        text = text + ":" + showAdvanced;
    }

    if (showSmallviewCheck.checked)
    {
        text = text + ":" + showSmallview;
    }
    for (var j = 0; j < radioElements.length; ++j)
    {
        if (radioElements[j].checked)
        {
            var horOrVert = radioElements[j].value;
            text = text + ":" + horOrVert;
        }
    }

    text = text + "}";
    var editor_element = window.parent.tinyMCE.get('jform_articletext');

    var content = window.parent.tinyMCE.get('jform_articletext').getContent();

    if (window.globalLeftIndex === 0)
    {
        window.globalLeftIndex = getCursorPosition(editor_element);
        window.globalRightIndex = window.globalLeftIndex;
    }

    if (window.globalRightIndex === window.globalLeftIndex)
    {
        // placeholder does not exist
        content = content.slice(0, window.globalLeftIndex) + text + content.slice(window.globalRightIndex, content.length);
    }
    else
    {
        // placeholder exists
        content = content.slice(0, window.globalLeftIndex) + text + content.slice(window.globalRightIndex + 1, content.length);
    }

    setNewContentInEditor(content);

//	window.parent.jInsertEditorText(text,getParam("e_name"));
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
            if (Params[i].split("=").length > 1)
            {
                variable = Params[i].split("=")[1];
            }
            return variable;
        }
    }
    return "";
}
