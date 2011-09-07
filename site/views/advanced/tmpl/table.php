<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/

?>
<div id="title"><?php echo "<h2 class='contentheading'>".$this->title."</h2>" ?></div>

<div id="gslistview">

<?php
	$members=$this->dataTable;
	$user = & JFactory::getUser();
	$usedUser = array();
	echo "<ul class='gs_advtable_left'>";
	foreach($members['left'] as $id => $member) {

		echo "<li>" .
				"<div name='memberwrappertable' id='gs_advtable_memberwrapper'>" ;
		
		$firstName = "";
		$lastName = "";
		$title = "";
		
		foreach ($member as $memberhead) {
			
			if($memberhead['structid'] == '1')
				$firstName = $memberhead['value'];
			if($memberhead['structid'] == '2')
				$lastName = $memberhead['value'];
			if($memberhead['structid'] == '5')
				$title = $memberhead['value'];
		}
		echo $title . " <a href=".JRoute::_('index.php?option=com_thm_groups&view=advanced&layout=table&Itemid='.$this->itemid.'&gsuid='.$id.'&name='.trim($lastName).'&gsgid='.$this->gsgid).">". trim($firstName) . " " . trim($lastName) . "</a>";
		if ($user->id == $id || $this->canEdit) {
			$attribs['title'] = 'bearbeiten';
			echo 		"<a href='" . JRoute :: _('index.php?option=com_thm_groups&view=edit&layout=default&Itemid=' . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) .'&gsgid='.$this->gsgid). "'> " . JHTML :: image("images/edit.png", 'bearbeiten', $attribs) . "</a>";
		}	
		foreach($member as $memberitem){

			if ($memberitem['type'] == 'PICTURE') {
				echo			"<div class='gs_advlistPicture'>";
				if($memberitem['value'] != "")
					echo JHTML :: image("components/com_thm_groups/img/portraits/".$memberitem['value'], "Portrait", array ('class' => 'mod_gs_portraitB'));
				else 
					echo JHTML :: image("components/com_thm_groups/img/portraits/anonym.jpg", "Portrait", array ('class' => 'mod_gs_portraitB'));									
				echo 		"</div>";
			} else if(!($memberitem['structid'] == '1' || $memberitem['structid'] == '2' || $memberitem['structid'] == '3' || $memberitem['structid'] == '5')){
				echo	"<div id='gs_advlist_longinfo'>";
				if ($memberitem['structid'] == '4')
					echo "<a href='mailto: ". $memberitem['value'] . "'>" . nl2br($memberitem['value']) . "</a>";
				else 
					echo nl2br($memberitem['value']);
				echo	"</div>";	
			}
		}
		echo	"</div>" .
			"</li>";
	}
	echo "</ul><ul class='gs_advtable_right'>";

	foreach($members['right'] as $id => $member) {
				echo "<li>" .
				"<div name='memberwrappertable' id='gs_advtable_memberwrapper'>" ;
		
		$firstName = "";
		$lastName = "";
		$title = "";
		
		foreach ($member as $memberhead) {
			
			if($memberhead['structid'] == '1')
				$firstName = $memberhead['value'];
			if($memberhead['structid'] == '2')
				$lastName = $memberhead['value'];
			if($memberhead['structid'] == '5')
				$title = $memberhead['value'];
		}
		echo $title . " <a href=".JRoute::_('index.php?option=com_thm_groups&view=advanced&layout=table&Itemid='.$this->itemid.'&gsuid='.$id.'&name='.trim($lastName).'&gsgid='.$this->gsgid).">". trim($firstName) . " " . trim($lastName) . "</a>";
		if ($user->id == $id || $this->canEdit) {
			$attribs['title'] = 'bearbeiten';
			echo 		"<a href='" . JRoute :: _('index.php?option=com_thm_groups&view=edit&layout=default&Itemid=' . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) .'&gsgid='.$this->gsgid). "'> " . JHTML :: image("images/edit.png", 'bearbeiten', $attribs) . "</a>";
		}	
		foreach($member as $memberitem){

			if ($memberitem['type'] == 'PICTURE') {
				echo			"<div class='gs_advlistPicture'>";
				if($memberitem['value'] != "")
					echo JHTML :: image("components/com_thm_groups/img/portraits/".$memberitem['value'], "Portrait", array ('class' => 'mod_gs_portraitB'));
				else 
					echo JHTML :: image("components/com_thm_groups/img/portraits/anonym.jpg", "Portrait", array ('class' => 'mod_gs_portraitB'));									
				echo 		"</div>";
			} else if(!($memberitem['structid'] == '1' || $memberitem['structid'] == '2' || $memberitem['structid'] == '3' || $memberitem['structid'] == '5')){
				echo	"<div id='gs_advlist_longinfo'>";
				if ($memberitem['structid'] == '4')
					echo "<a href='mailto: ". $memberitem['value'] . "'>" . nl2br($memberitem['value']) . "</a>";
				else 
					echo nl2br($memberitem['value']);
				echo	"</div>";	
			}
		}
		echo	"</div>" .
			"</li>";
	}
	echo "</ul>";
?>

</div>