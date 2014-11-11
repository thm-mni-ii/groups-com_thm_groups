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

    jQuery.noConflict();
    (function( $ ) {$(function(){
        // $("#paramsattr").draggable();
        jQuery("#paramsattr").sortable({
            update: function(event, ui)  {
                var listul ="";
                jQuery('#paramsattr li').each(function(){
                    listul = jQuery(this).val() +',' +listul;
                });
                var result = '1,' + listul +'4';
                jQuery("#resultOrder").val(result);
            }
        });
        //$("#paramsattr").disableSelectibon();

    });})(jQuery);
   // $("#paramsattr").disableSelectibon();
