/**
 * @version $Id: menuitemselect.js 229 2009-02-02 23:14:17Z kernelkiller $
 * @package Joomla
 * @subpackage GiessenLatestNews
 * @author Frithjof Kloes
 * @copyright Copyright (C) 2008 FH Giessen-Friedberg / University of Applied
 *            Sciences
 * @license GNU/GPL, see LICENSE.php Joomla! is free software. This version may
 *          have been modified pursuant to the GNU General Public License, and
 *          as distributed it includes or is derivative of works licensed under
 *          the GNU General Public License or other free or open source software
 *          licenses. See COPYRIGHT.php for copyright notices and details.
 */

// change the sort of the attributes, selected attribute one position higher
function attrup() {

    var role = document.getElementById('paramsattr');

    // If no Param is selected------------------------------------
    if (role.selectedIndex == -1)
        alert("Bitte ein Attribut auswaehlen");
    // ------------------------------------------------------------
    else {
        // Change Roles down------------------------------------------
        selected = role.selectedIndex;
        var tmpvalue = role.options[selected].value;
        var tmptext = role.options[selected].text;
        document.getElementById('paramsattr').options[selected].value = role.options[selected - 1].value
        document.getElementById('paramsattr').options[selected].text = role.options[selected - 1].text
        document.getElementById('paramsattr').options[selected - 1].value = tmpvalue;
        document.getElementById('paramsattr').options[selected - 1].text = tmptext;
        document.getElementById('paramsattr').options[selected - 1].selected = true;
        // ------------------------------------------------------------

        // Write new sorted Roles into hidden paramsfield-------------
        var temp = "";
        for (i = 0; i < document.getElementById('paramsattr').length; i++) {
            temp += document.getElementById('paramsattr').options[i].value
                    + ',';
        }
        // remove the last char (,) from the string
        temp = temp.substr(0, temp.length - 1);
        // write sorted roles to hidden parameter box
        document.getElementById('jform_params_orderingAttributes').value = temp;
        // ------------------------------------------------------------
    }
}
// change the sort of the attributes, selected attribute one position down
function attrdown() {
    var role = document.getElementById('paramsattr');
    // If no Param is selected------------------------------------
    if (role.selectedIndex == -1)
        alert("Bitte ein Attribut auswaehlen");
    // ------------------------------------------------------------
    else {
        // Change Roles down------------------------------------------
        selected = role.selectedIndex;
        var tmpvalue = role.options[selected].value;
        // alert(role.value);
        var tmptext = role.options[selected].text;
        document.getElementById('paramsattr').options[selected].value = role.options[selected + 1].value
        document.getElementById('paramsattr').options[selected].text = role.options[selected + 1].text
        document.getElementById('paramsattr').options[selected + 1].value = tmpvalue;
        document.getElementById('paramsattr').options[selected + 1].text = tmptext;
        document.getElementById('paramsattr').options[selected + 1].selected = true;
        // ------------------------------------------------------------

        // Write new sorted Roles into hidden paramsfield-------------
        var temp = "";
        for (i = 0; i < document.getElementById('paramsattr').length; i++) {
            temp += document.getElementById('paramsattr').options[i].value
                    + ',';
        }
        // remove the last char (,) from the string
        temp = temp.substr(0, temp.length - 1);
        // write sorted roles to hidden parameter box
        document.getElementById('jform_params_orderingAttributes').value = temp;
        // ------------------------------------------------------------
    }
}
