/**
 * @version Orderattributte
 * @package Joomla
 * @subpackage THM Groups List View
 * @author Dieudonne Timma
 * @copyright Copyright (C) 2015 FH Giessen-Friedberg / University of Applied
 *            Sciences
 * @license GNU/GPL, see LICENSE.php Joomla! is free software. This version may
 *          have been modified pursuant to the GNU General Public License, and
 *          as distributed it includes or is derivative of works licensed under
 *          the GNU General Public License or other free or open source software
 *          licenses. See COPYRIGHT.php for copyright notices and details.
 */

    jQuery.noConflict();

jQuery( document ).ready(function() {
    jQuery("#paramsattr").sortable({
        stop: function(event, ui)  {
            var listul ="";
            jQuery('#paramsattr li').each(function(){
                if(jQuery(this).val() != 0)
                listul = jQuery(this).val() +',' +listul;
            });
            var result = '1,' + listul +'4';
            jQuery("#resultOrder").val(result);
            console.log(result);
        }
    });
});
