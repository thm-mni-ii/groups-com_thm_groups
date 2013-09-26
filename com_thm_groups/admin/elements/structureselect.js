/**
 * @category Web Programming Weeks WS2011/2012: TH Mittelhessen
 * @package  com_thm_groups
 * @author   Peter May (peter.may@mni.thm.de)
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.thm.de
 */


 /**
 * Ersetzt an der angebenen Stelle ein Zeichen im String.
 *
 * @str    Die Zeichenkette in der ein Zeichen ersetzt werden soll
 * @index  Der null-basierende Index des zu ersetzenden Zeichens
 * @char   Das neue Zeichen
 * @return Die neue Zeichenkette
 */
function replaceCharAt(str, index, char) {
    var newStr = "";
    for (var i = 0; i < str.length; i++)
    {
        if (i == index) {
            newStr += char;
        } else {
            newStr += str.charAt(i);
        }
    }
    return newStr;
}


/**
 * Schaltet eine angefuegte Attributinformation um.
 *
 * @elementId                Id des zu bearbeitenden Elements
 * @additionalAttributeIndex Index des angefuegtes Attributs
 */
function switchAdditionalAttribute(elementId, additionalAttributeIndex) {
    var value = document.getElementById(elementId).value;
    var attrNameIndex = value.length - additionalAttributeIndex;
    if (value.charAt(attrNameIndex) == "1") {
        //value[attrNameIndex] = '0';
        value = replaceCharAt(value.toString(), attrNameIndex, "0");
    } else {
        //value[attrNameIndex] = '1';
        value = replaceCharAt(value.toString(), attrNameIndex, "1");
    }
    document.getElementById(elementId).value = value;
}


/**
 * Schaltet die angefuegte Attributinformation "Attributname anzeigen" um.
 *
 * @elementId Id des zu bearbeitenden Elements
 */
function switchAttributeName(elementId) {
    switchAdditionalAttribute(elementId, 2);
}


/**
 * Schaltet die angefuegte Attributinformation "Zeilenumbruch nach Anzeige" um.
 *
 * @elementId Id des zu bearbeitenden Elements
 *///
function switchAttributeWrap(elementId) {
    switchAdditionalAttribute(elementId, 1);
}


/**
 * Aktiviert/Deaktiviert die angefuegten Attribute.
 *
 * @elementId Id des Hauptelements
 *///
function switchEnablingAdditionalAttr(elementId) {
    var disabled = !document.getElementById(elementId).checked;
    document.getElementById(elementId + "ShowName").disabled = disabled;
    document.getElementById(elementId + "WrapAfter").disabled = disabled;
}
