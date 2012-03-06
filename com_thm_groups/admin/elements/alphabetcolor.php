<?php
/**
 * @version		$Id: mod_giessen_staff.php
 * @package		Joomla
 * @subpackage	GiessenStaff
 * @author		Dennis Priefer
 * @copyright	Copyright (C) 2008 FH Giessen-Friedberg / University of Applied Sciences
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield'); 
?>


<?php
class JFormFieldAlphabetColor extends JFormField {
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */

	function getInput() {
		$scriptDir = str_replace(JPATH_SITE.DS,'',"administrator/components/com_thm_groups/elements/");
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JUri::root().'/administrator/components/com_thm_groups/elements/mooRainbow.css');
		// add script-code to the document head
		JHTML::script('mooRainbow.js', $scriptDir, false);
		$img = JUri::root().'/administrator/components/com_thm_groups/elements/images/';
?>
<script>
var i=0;
function change_<?php echo $this->fieldname?>(){
		var r = new MooRainbow('<?php echo $this->name;?>', {
			id: '<?php echo $this->fieldname?>' + i,
			startColor: $('<?php echo $this->name;?>').style.backgroundColor,
	    	'onChange': function(color) {
		    	$('<?php echo $this->name;?>').value = color.hex;
		        $('<?php echo $this->name;?>').setStyle("backgroundColor", color.hex);
	     	},
	     	imgPath: '<?php echo $img;?>'
		});
		i++;
	}

</script>
<?php 		
		
        $html = "<input id='".$this->name."' name='".$this->name."' type='text' size='13' value='".$this->value."' style='background-color:".$this->value.";' onfocus='change_".$this->fieldname."()'/>";
      	
     	return $html;
	}
}
?>