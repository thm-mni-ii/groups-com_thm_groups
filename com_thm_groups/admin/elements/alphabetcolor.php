<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldAlphabetColor
 * @description JFormFieldAlphabetColor file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de

 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * JFormFieldAlphabetColor class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class JFormFieldAlphabetColor extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 * 
	 * @return html
	 */

	public function getInput()
	{
		$scriptDir = str_replace(JPATH_SITE . DS, '', "administrator/components/com_thm_groups/elements/");
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JUri::root() . '/administrator/components/com_thm_groups/elements/mooRainbow.css');

		// Add script-code to the document head
		JHTML::script('mooRainbow.js', $scriptDir, false);
		$img = JUri::root() . '/administrator/components/com_thm_groups/elements/images/';
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

		$html = "<input id='" . $this->name . "' name='" . $this->name . "' type='text' size='13' value='"
		. $this->value . "' style='background-color:" . $this->value . ";' onfocus='change_" . $this->fieldname . "()'/>";
		return $html;
	}
}
