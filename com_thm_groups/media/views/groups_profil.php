<?php
/**
 * @version     v1.2.1
 * @category    Joomla plugin
 * @package     THM_Groups_View
 * @subpackage  lib_thm_groups_view
 * @name        HelperPage
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 *
 */

defined('_JEXEC') or die;


JHTML::_('behavior.tooltip');


require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_data.php";
require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_user_data.php";


/**
 * Library of the Listview
 *
 * @category  Joomla.Library
 * @package   thm_groups
 * @since     v1.2.0
 */
class THM_GroupsProfilview extends THM_GroupsUserData
{
	/**
	 * Return information about user
	 *
	 * @param   Integer $userid      contains user id
	 *
	 * @param   Array   $userData    user data
	 *
	 * @param   Array   $verlinklist Contain the Element with Hyperlink
	 *
	 * @return  information about user
	 */
	public function buildinfo($userid, $userData, $verlinklist)
	{
		// Person:{Schlüsselwort:person:uid:links(name,vorname):params(Max-Breite,Position,Rahmen,float):struct(...):userlist(none)}


		$result        = '';
		$displayInline = 'class="thm_groups_content_member_edit"';
		$result .= '<div style= "thm_groups_content_member_div_style">';
		$attribut  = parent::getUrl(array("name", "gsuid", "gsgid"));
		$tempName  = JFactory::getUser($userid)->get('name');
		$nameArray = explode(" ", $tempName);
		$lastName  = (array_key_exists(1, $nameArray) ? $nameArray[1] : "");

		if ($userid)
		{
			$struct = array();
			foreach (parent::getStructure() as $structItem)
			{
				$struct[$structItem->id] = $structItem->field;
			}


			if ($userid == JFactory::getUser()->id && parent::canEdit() == true)
			{
				$result .= "<div $displayInline>";

				// Id einfügen???
				$result .= "<a href='" . JRoute::_('index.php?option=com_thm_groups&view=edit&layout=default&'
						. $attribut
						. '&gsuid=' . $userid
						. '&name=' . trim($lastName)
						. '&gsgid=4'

					)
					. "'>" . JHTML::image("media/thm_groups/images/edit.png", 'bearbeiten', 'bearbeiten') . "</a>";
				$result .= "</div>";
			}
			// $userData as $data

			for ($i = 0; $i < count($userData); $i++)
			{
				$data    = $userData[$i];
				$counter = $i + 1;

				//
				while ($counter < count($userData))
				{
					$next = $userData[$counter];

					if ($next->publish == true)
					{
						break;
					}
					$counter++;
				}
				if ($data->value != "" && $data->publish)
				{

					if (!empty($next) && $next->structwrap == true)
					{
						// $result .= '<div style ="inline:block; float:bottom; clear:both after; align:text-bottom;" >';
						$result .= '<div class="thm_groups_content_member_structur_wrap" >';
					}
					else
					{
						$result .= '<div class="thm_groups_content_member_structur_nowrap" >';
					}
					if ($data->structname == true)
					{
						$result .= JText::_($struct[$data->structid]) . ": ";
					}
					else
					{
					}

					// Attribut anzeigen

					$islink = self::isverlink($verlinklist, $struct[$data->structid]);


					switch ($data->structid)
					{
						// Vorname
						case "1":
							if ($islink == true)
							{
								$result .= "<a href='" . JRoute::_('index.php?option=com_thm_groups&view=profile&layout=default&'
										. $attribut
										. '&gsuid=' . $userid
										. '&name=' . trim($lastName)
										. '&gsgid=4'

									) . "'>" . $data->value . "</a>" . "&nbsp";
							}
							else
							{
								$result .= $data->value . "&nbsp;";
							}
							break;

						// Nachname
						case "2":
							if ($islink == true)
							{
								$result .= "<a href='" . JRoute::_('index.php?option=com_thm_groups&view=profile&layout=default&'
										. $attribut
										. '&gsuid=' . $userid
										. '&name=' . trim($lastName)
										. '&gsgid=4'

									) . "'>" . $data->value . "</a>" . "&nbsp";
							}
							else
							{
								$result .= $data->value . "&nbsp;";
							}
							break;

						// Titel
						case "5":
							$result .= $data->value . "&nbsp;";
							break;

						// EMail
						case "4":
							$image = self::getImage('media/thm_groups/images/icon_mail.png', 'Mail', 'plg_insert_user_logo');
							$result .= JHTML::_('email.cloak', $data->value, 1, $image, 0);

							break;
						default:
							switch ($data->type)
							{
								case "LINK":
									if ($data->type == 'LINK' && trim($data->value) != "")
									{
										$image = JHTML::image("media/thm_groups/images/icon_www.png", 'WWW', 'class=plg_insert_user_logo');
										$result .= self::getLink(trim(htmlspecialchars_decode($data->value)), $image, 'link');
									}
									else
									{
									}
									break;
								case "PICTURE":

									$path  = parent::getPicPath($data->structid);
									$image = JHTML::image("$path" . '/' . $data->value, 'Portrait', 'class=plg_insert_user_img');

									$result .= "$image";

									break;

								default:

									$result .= nl2br(htmlspecialchars_decode($data->value)) . "&nbsp;";
									break;
							}
							break;
					}
					$result .= "</div>";

				}
			}
			$result .= "</div>";
		}

		else
		{
			$result .= "<div>";
			$result .= "";
			$result .= "</div>";
		}


		return $result;
	}

	/**
	 * return true if the Attribut is in the Liste
	 *
	 * @param   Array  $verlinklist the list of selected Attributs
	 *
	 * @param   String $attribut    the selected Attribut
	 *
	 * @return  boolean  $result
	 */
	public function isverlink($verlinklist, $attribut)
	{

		for ($i = 0; $i < count($verlinklist); $i++)
		{
			$wort = " ";
			switch ($verlinklist[$i])
			{
				case '1':
					$wort = 'Vorname';
					break;
				case '2':
					$wort = 'Nachname';
					break;
				case '0':
					$wort = 'Titel';
					break;
				case '3':
					$wort = 'Posttitel';
					break;
			}
			if (strcmp($wort, $attribut) == 0)
			{
				return true;
			}
		}
	}

	/**
	 * Return information about all Users of a Group
	 *
	 * @param   Integer $gid         contains groups ID
	 *
	 * @param   Array   $parameter   contain Number of colum
	 *
	 * @param   Array   $structs     All Structure Element for a User
	 *
	 * @param   Array   $listusers   The list of selected User
	 *
	 * @param   Array   $verlinklist The list of Attribut with link
	 *
	 * @return  information about All User of a Group
	 */

	public function groupAdvancedBuild($gid, $parameter, $structs, $listusers, $verlinklist)
	{
// Groups Advanced:{Schlüsselwort:advanced:gid:links(name,vorname):params(Max-breite,ColumnNumber,Position,Rahmen,float):struct(...):userlist(...)}

		$listId = array();

		$column = (intval($parameter[1]) == 0) ? 1 : intval($parameter[1]);
		if (!isset($listusers) || count($listusers) <= 1)
		{
			$allMembersId = parent::getMitglieder($gid);
			foreach ($allMembersId as $userid)
			{
				array_push($listId, $userid->user_id);
			}
		}
		else
		{
			$listId = $listusers;
		}


		$style = "float:" . $parameter[2] . ";clear:" . $parameter[4] . ";border:" . $parameter[3];

		$position = (strcmp($parameter[2], 'left') != 0 || strcmp($parameter[2], 'right') != 0) ? 'left' : $parameter[2];

		$styleDiv2 = "float:" . $position . ";clear:" . $position;
		$output    = '<div style="' . $style . '"><div style="' . $styleDiv2 . '">';


		$count = 0;
		foreach ($listId as $userid)
		{
			if (intval($userid) != 0)
			{
				if ($count < $column)
				{
					$styling = "float:" . $position . ";max-width:" . $parameter[0] . "px;margin:5px";
					$output .= '<div style="' . $styling . '">';
				}
				if ($count >= $column)
				{
					$count     = 0;
					$styleDiv3 = "float:bottom;clear:" . $position;
					$output .= '</div><br><div style="' . $styleDiv3 . '">';
					$styling = "float:" . $position . ";max-width:" . $parameter[0] . "px;margin:10px";
					$output .= '<div style="' . $styling . '">';


				}

//            $cssConfig = array($parameter[0],'none','none','none');

				$userData = parent::getUserInfo($userid, $structs);
				$output .= self::buildinfo($userid, $userData->profilInfos, $verlinklist);

				$output .= "</div>";
				$count++;
			}
		}
		$output .= "</div></div>";

		return $output;
	}

	/**
	 * Returns a HTML image element.
	 *
	 * @param   String $path Relative module path to image.
	 * @param   String $text Alternative text if picture can't be shown.
	 * @param   String $cssc String  CSS class to use.
	 *
	 * @return  String  HTML image element.
	 */
	public static function getImage($path, $text, $cssc)
	{
		return JHTML::image(
			"$path",
			$text,
			array(
				'class' => $cssc
			)
		);
	}

	/**
	 * Returns HTML link.
	 *
	 * @param   String $path Target URL.
	 * @param   String $text Text to display.
	 * @param   String $cssc CSS class to use.
	 *
	 * @return  String    HTML link.
	 */
	public function getLink($path, $text, $cssc = '')
	{
		return "<a class=\"$cssc\" href=\"$path\" target=\"_blank\">$text</a>";
	}

	/**
	 * generate the css code
	 *
	 * @param   Integer $maxbreite contains the maximal width
	 *
	 * @param   Integer $maxheigth contains the maximal heigth
	 *
	 * @param   Array   $cssConfig Css attribut for Configarition [Max-Breite,Position,Rahmen,float]
	 *
	 * @return  String Css code
	 */
	public function getProfilCss($maxbreite, $maxheigth, $cssConfig)
	{
		$out = ".plg_insert_user_img {
            vertical-align:text-bottom;
            max-width:" . $maxbreite . "px;
            height: " . $maxheigth . " px;
        }";

		$out .= ".plg_insert_user_logo {
            vertical-align:text-bottom;
        }";
		$out .= ".plg_insert_user_checkboxes {
            font-size: 12px;
            border: 0 none;
            clear: right;
            float: left;
            margin: 0 0 5px;
            padding: 0;
        }";
		$out .= " .thm_groups_content_member_div_style {" . "max-width:" . $cssConfig[0]
			. "; float:" . $cssConfig[1] . ";border:" . $cssConfig[2] . ";clear:" . $cssConfig[3] . ";}";
		$out .= " .thm_groups_content_member_structur_wrap{inline:block;clear:both after;}";
		$out .= " .thm_groups_content_member_structur_nowrap{inline:block;clear:both after;float:left;}";
		$out .= " .plg_thm_groups_content_member_edit{inline:block}";

		return $out;

	}
}
