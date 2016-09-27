<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @description THMGroupsViewAdvanced file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author      Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// require implode('/', array(JPATH_ROOT, 'components', 'com_thm_groups', 'helper', 'bootstrap_helper.php'));

jimport('joomla.application.component.view');
require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_data.php";
JHtml::_('bootstrap.framework');
JHtml::_('behavior.modal');

/**
 * THMGroupsViewAdvanced class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.thm.de
 */
class THM_GroupsViewAdvanced extends JViewLegacy
{
	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app   = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$model = $this->getModel('advanced');

		// Mainframe Parameter
		$params        = $app->getParams();
		$userid        = $input->get('userID', 0);
		$pagetitle     = $params->get('page_title');
		$showpagetitle = $params->get('show_page_heading');

		if ($showpagetitle)
		{
			$title = $pagetitle;
		}
		else
		{
			$title = "";
		}

		$pathway = $app->getPathway();

		if ($userid)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('value');
			$query->from($db->qn('#__thm_groups_users_attribute'));
			$query->where('usersID = ' . $userid);
			$query->where('attributeID = 1');

			$db->setQuery($query);
			$firstname = $db->loadObjectList();
			$name      = $input->get('name', '') . ', ' . $firstname[0]->value;
			$pathway->addItem($name, '');
		}

		$this->title     = $title;
		$this->app       = $input;
		$itemId          = $input->get('Itemid', 0, 'get');
		$viewparams      = $model->getViewParams();
		$this->params    = $viewparams;
		$groupnumber     = $model->getGroupNumber();
		$this->groupID   = $groupnumber;
		$this->itemid    = $itemId;
		$canEdit         = $model->canEdit($groupnumber);
		$this->canEdit   = $canEdit;
		$tempdata        = $model->getData();
		$this->data      = $tempdata;
		$gettable        = $model->getDataTable();
		$this->dataTable = $gettable;

		$advancedView = $model->getAdvancedView();

		$this->view = $advancedView;

		// Long Info Truncate
		$truncateLongInfo       = !$params->get('longInfoNotTruncated', false);
		$this->truncateLongInfo = $truncateLongInfo;

		$document = JFactory::getDocument();

		// Load responsive CSS
		$document->addStyleSheet($this->baseurl . '/media/com_thm_groups/css/respAdvanced.css');

		// Load Dynamic CSS
		$mycss = $this->getCssView($params, $advancedView);

		$document->addStyleDeclaration($mycss);

		// Notify Preview Observer
		$token = $input->get('notifytoken', false);

		if (!empty($token))
		{
			$model->notifyPreviewObserver($itemId, $token);
		}

		parent::display($tpl);
	}

	/**
	 * Method to generate table
	 *
	 * @param   Object $data Data
	 *
	 * @return String table
	 */
	public function make_table($data)
	{
		$jsonTable = json_decode($data);
		$table     = "<table class='table'><tr>";

		foreach ($jsonTable[0] as $key => $value)
		{
			$headItem = str_replace("_", " ", $key);
			$table    = $table . "<th>" . $headItem . "</th>";
		}

		$table = $table . "</tr>";

		foreach ($jsonTable as $item)
		{
			$table = $table . "<tr>";

			foreach ($item as $value)
			{
				$table = $table . "<td>" . $value . "</td>";
			}

			$table = $table . "</tr>";
		}

		$table = $table . "</table>";

		return $table;
	}


	/**
	 * Add px Suffix to numeric value (for css)
	 *
	 * @param   Mixed $value Value
	 *
	 * @author    Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
	 * @author    Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
	 *
	 * @return  String    $value    CSS-Value
	 */
	public function addPxSuffixToNumeric($value)
	{
		if (is_numeric($value))
		{
			$value .= 'px';
		}

		return $value;
	}


	/**
	 * Get the Stylesheet for Advance View List
	 *
	 * @param   Array $params       Contains the Paramter for the View
	 * @param   mixed $advancedView Show multiple Containers in one Row
	 *
	 * @author    Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
	 * @author    Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
	 *
	 * @return  String   $result  the HTML code of te view
	 */
	public function getCssView($params, $advancedView = 0)
	{

		// Container Wrapper Width - DO NOT CHANGE
		$containerWrapperWidth = (empty($advancedView)) ? '100%' : '50%';


		// LOAD PARAMS START

		// Container Dimensions
		$containerWidth  = $params->get('containerWidth', '100%');
		$containerHeight = $params->get('containerHeight', 'auto');

		// Container Padding
		$containerPadding = $params->get('containerPadding', 10);

		// Container Margin Bottom
		$containerMarginBottom = $params->get('containerMarginBottom', 10);

		// Container Background
		$containerBackgroundOdd  = $params->get('containerBackgroundOdd', '#f9f9f9');
		$containerBackgroundEven = $params->get('containerBackgroundEven', '#f1f1f1');

		// Font Params
		$fontFamily        = $params->get('fontFamily', 'inherit');
		$fontSize          = $params->get('fontSize', 'inherit');
		$fontColorOdd      = $params->get('fontColorOdd', '#000000');
		$fontColorEven     = $params->get('fontColorEven', '#000000');
		$longInfoColorOdd  = $params->get('longInfoColorOdd', '#525252');
		$longInfoColorEven = $params->get('longInfoColorEven', '#525252');

		// Profile Image Dimensions
		$imgWidth    = $params->get('profileImageWidth', '66');
		$imgHeight   = $params->get('profileImageHeight', 'auto');
		$imgBordered = $params->get('profileImageBorderd', false);

		if ($imgBordered)
		{
			$imgBordered = 'border: 1px solid #ffffff;
                            -webkit-border-radius: 4px;
                            border-radius: 4px;
                            -webkit-box-shadow: 0px 0px 3px 0px #999999;
                            box-shadow: 0px 0px 3px 0px #999999;';
		}
		else
		{
			$imgBordered = '';
		}

		$imgPositionLeft = $params->get('profileImageFloatedLeft', false);

		if ($imgPositionLeft)
		{
			$imgPosition = 'margin:0px 10px 0px 0px!important;float:left;';
		}
		else
		{
			$imgPosition = 'margin:0px 0px 0px 10px!important;float:right;';
		}

		// Addition individual Styles
		$profileContainerStyles = $params->get('profileContainerStyles', false);
		$profileImageStyles     = $params->get('profileImageStyles', false);
		$textLineStyles         = $params->get('textLineStyles', false);
		$textLineLabelStyles    = $params->get('textLineLabelStyles', false);
		$linksStyles            = $params->get('linksStyles', false);
		$showMoreButtonStyles   = $params->get('showMoreButtonStyles', false);
		$longInfoStyles         = $params->get('longInfoStyles', false);


		// LOAD PARAMS END

		$out = 'div#thm_groups_profile_container_list {
                    font-family: ' . $fontFamily . ';
                    font-size: ' . $this->addPxSuffixToNumeric($fontSize) . ';
                }

                div#thm_groups_profile_container_list a {
                    font-family: inherit;
                    font-size: inherit;
                    ' . $linksStyles . '
                }

                div.thm_groups_profile_container_list_row_odd, div.thm_groups_profile_container_list_row_even {
                     //width: 100%;
                    margin: 0px 0px ' . $this->addPxSuffixToNumeric($containerMarginBottom) . ' 0px !important;
                    clear: both;
                    overflow: auto;
                    ' . $profileContainerStyles . '
                }

                div.thm_groups_profile_container_list_row_odd {
                    color: ' . $fontColorOdd . ';
                    background: ' . $containerBackgroundOdd . ';
                }

                div.thm_groups_profile_container_list_row_even {
                    color: ' . $fontColorEven . ';
                    background: ' . $containerBackgroundEven . ';
                }

                div.clearfix {
                    clear:both;
                }

                div.thm_groups_profile_container_list_coloumn_wrapper {
                    width: ' . $containerWrapperWidth . ';
					float: left;
                }

                div.thm_groups_profile_container_list_coloumn {
                    width: ' . $this->addPxSuffixToNumeric($containerWidth) . ';
                    height: ' . $this->addPxSuffixToNumeric($containerHeight) . ';
                    margin: auto;
                }

                div.thm_groups_profile_container_list_coloumn_content_wrapper {
                    padding: ' . $this->addPxSuffixToNumeric($containerPadding) . ';
                }

                img.thm_groups_profile_container_profile_image {
                    max-width: ' . $this->addPxSuffixToNumeric($imgWidth) . ';
                    max-height: ' . $this->addPxSuffixToNumeric($imgHeight) . ';
                    display: block;
                    ' . $imgBordered . '
                    ' . $imgPosition . '
                    ' . $profileImageStyles . '
                }

                input#thm_groups_profile_container_preview_button {
                    cursor: pointer;
                }

                span.thm_groups_profile_container_profile_read_more {
                    text-decoration: underline;
                    cursor: pointer;
                    ' . $showMoreButtonStyles . '
                }

                div.thm_groups_profile_container_list_row_odd div.thm_groups_profile_container_profile_long_info,
                div.thm_groups_profile_container_list_row_odd div.thm_groups_profile_container_profile_long_info li {
                    color: ' . $longInfoColorOdd . ';
                }

                div.thm_groups_profile_container_list_row_even div.thm_groups_profile_container_profile_long_info,
                div.thm_groups_profile_container_list_row_even div.thm_groups_profile_container_profile_long_info li {
                    color: ' . $longInfoColorEven . ';
                }

                div.thm_groups_profile_container_profile_long_info {
                    ' . $longInfoStyles . '
                }

                div.thm_groups_profile_container_line {
                    ' . $textLineStyles . '
                }

                span.thm_groups_profile_container_line_label {
                    ' . $textLineLabelStyles . '
                }';

		return $out;
	}

	/**
	 * to get the Link to show the User Information
	 *
	 * @param   String  $paramLinkTarget Show Option for Link
	 * @param   Integer $itemid          Menu Id
	 * @param   String  $lastName        Group Member last anem
	 * @param   Integer $groupID         Group Id
	 * @param   Integer $userID          Group Member ID
	 * @param   Array   $attribut        Group Member Attribut
	 *
	 * @return the HTML to link the User information
	 */
	public function getUserinInformation($paramLinkTarget, $itemid, $lastName, $groupID, $userID, $attribut)
	{
		$displayInline = " style='display: inline'";
		$displayLeft   = " style='float: left'";
		$result        = "";

		switch ($paramLinkTarget)
		{
			case "module":
				$path = "index.php?option=com_thm_groups&view=advanced&layout=list&Itemid=";
				$result .= "<a href="
					. JRoute::_($path . $itemid . '&userID=' . $userID . '&name=' . trim($lastName) . '&groupID=' . $groupID)
					. ">";
				break;
			case "profile":
				$path = 'index.php?option=com_thm_groups&view=profile&layout=default';
				$result .= "<a href="
					. JRoute::_($path . '&userID=' . $userID . '&name=' . trim($lastName) . '&groupID=' . $groupID)
					. ">";
				break;
			default:
				$path = "index.php?option=com_thm_groups&view=advanced&layout=list&Itemid=";
				$result .= "<a href="
					. JRoute::_($path . $itemid . '&userID=' . $userID . '&name=' . trim($lastName) . '&groupID=' . $groupID)
					. ">";
		}

		if (trim($attribut->value) != "")
		{
			$result .=
				"<div class='gs_advlist_longinfo'"
				. ($attribut->structwrap ? $displayLeft : $displayInline) . ">"
				. trim($attribut->value)
				. "</div> ";
		}

		$result .= "</a>";

		return $result;
	}

	/**
	 * To get All the User of a Group for Advanced-View
	 *
	 * @param   Array   $allUsersData     content All Member of a groups
	 * @param   Integer $col_view         Number of column
	 * @param   String  $paramLinkTarget  Show Option for Link
	 * @param   Boolean $canEdit          Can User Edit
	 * @param   Integer $groupID          Group Id
	 * @param   Integer $itemid           Menu Id
	 * @param   Integer $app_option       Group Member ID
	 * @param   Array   $app_layout       Layout Option
	 * @param   Array   $app_view         view Option
	 * @param   Boolean $truncateLongInfo Hidden the Attribute
	 *
	 * @return the HTML Code with all users information
	 */
	public function showAllUserOfGroup(
		$allUsersData,
		$col_view,
		$paramLinkTarget,
		$canEdit,
		$groupID,
		$itemid,
		$app_option,
		$app_layout,
		$app_view,
		$truncateLongInfo)
	{
		// Show Profiles
		$members = $allUsersData;
		$User    = JFactory::getUser();
		$result  = "";

		// 1 Column or 2 Columns in one Row.
		$countOfColoumns = $col_view;
		$elementCounter  = 0;
		$rowCounter      = 0;
		$lastIndex       = count($members) - 1;

		// Get mobile Navigation
		$result .= $this->getMobileNavbar($allUsersData);

		foreach ($members as $id => $member)
		{
			// Open Row Tag - Even / Odd
			if ($elementCounter % $countOfColoumns == 0)
			{
				// Count Elements
				$rowCounter++;

				$cssListRowClass = ($rowCounter % 2) ? '_odd' : '_even';
				$result .= '<div class="thm_groups_profile_container_list_row' . $cssListRowClass . '">';
			}

			// Open Coloumn Wrapper Tag - Only for float-attribute, now is easy to work with width:100%
			if ($countOfColoumns == 1)
			{
				$cssListColoumnClass = '_full';
				$result .= '<div class="thm_groups_profile_container_list_coloumn_wrapper' . $cssListColoumnClass . '">';
			}
			else
			{
				$cssListColoumnClass = ($elementCounter % $countOfColoumns == 0) ? '_left' : '_right';
				$result .= '<div class="thm_groups_profile_container_list_coloumn_wrapper '
					. ' col_med_6_advanced col-sm-6 span6 thm_groups_profile_container_list_coloumn_wrapper' . $cssListColoumnClass . '">';
			}

			// Open Coloumn Tag - Only for dimensions
			$result .= '<div class="thm_groups_profile_container_list_coloumn">';

			// Open Content Wrapper Tag - For Properties like padding, border etc.
			$result .= '<div class="thm_groups_profile_container_list_coloumn_content_wrapper">';

			// Load Profile Content
			$lastName = "";
			$picture  = null;
			$picpath  = null;

			$componentparams = JComponentHelper::getParams('com_thm_groups');

			foreach ($member as $memberhead)
			{
				// Daten fuer den HEAD in Variablen speichern
				switch ($memberhead->structid)
				{
					case "2":
						$lastName = $memberhead->value;
						$result .= "<div id='" . $lastName . "' style='visibility:hidden;'></div>";
						break;
					default:
						if ($memberhead->type == "PICTURE" && $picture == null && $memberhead->publish)
						{
							$picture = $memberhead->value;

							if (isset($memberhead->options))
							{
								$pictureOption = json_decode($memberhead->options);
							}
							else
							{
								$pictureOption = json_decode($memberhead->dynOptions);
							}

							$tempposition = explode('images/', $pictureOption->path, 2);
							$picpath      = 'images/' . $tempposition[1];
						}
						break;
				}
			}

			$result .= "<div id='secondWrapper'>";

			if ($picture != null)
			{
				$result .= JHTML::image(
					JURI::root()
					. $picpath . $picture, "Portrait", array('class' => 'thm_groups_profile_container_profile_image')
				);
			}

			$result .= "<div id='gs_advlistTopic'>";

			$tempcanEdit = (($User->id == $id && $componentparams->get('editownprofile', 0) == 1) || $canEdit);

			// Every user can edit himself
			if ($tempcanEdit)
			{
				$linkTitle = 'bearbeiten';
				$data             = ['Itemid'  => $itemid,
				                     'option'  => 'com_thm_groups',
				                     'view'    => 'profile_edit',
				                     'userID'  => $id,
				                     'groupID' => $groupID,
				                     'name'    => $lastName
				];

				$link = 'index.php?' . http_build_query($data);
				$result .= JHtml::link(JRoute::_($link), JHtml::image("components/com_thm_groups/img/edit.png", $linkTitle));
			}

			$result .= "</div>";
			$wrap = true;

			// Rest des Profils darstellen
			$result .= "<div>";

			foreach ($member as $memberitem)
			{
				if ($memberitem->value != "" && $memberitem->publish)
				{
					if ($memberitem->structwrap == true)
					{
						if ($memberitem->structname == true && (($memberitem->name == 'Email') || ($memberitem->structid == 4)))
						{
							$result .= "<div class='gs_advlist_longinfo thm_groups_profile_container_line respMail-line'>";
						}
						else
						{
							$result .= "<div class='gs_advlist_longinfo thm_groups_profile_container_line'> ";
						}
					}
					else
					{
						$result .= "<div style='display: inline;'> ";
					}

					// Attributnamen anzeigen
					if ($memberitem->structname == true)
					{
						if (($memberitem->name == 'Email'
								|| $memberitem->structid == 4)
							|| ($memberitem->name == 'Website'
								|| $memberitem->type == 'TEXTFIELD')
						)
						{
							$result .= '<span class="thm_groups_profile_container_line_label respMail-label" style="float: left" >'
								. JText::_($memberitem->name) . ':&nbsp;' . '</span>';
						}
						else
						{
							$result .= '<span class="thm_groups_profile_container_line_label" style="float: left" >'
								. JText::_($memberitem->name) . ':&nbsp;' . '</span>';
						}
					}

					// Attribut anzeigen
					switch ($memberitem->structid)
					{
						// Reihenfolge ist von ORDER in Datenbank abhaengig. somit ist hier die Reihenfolge egal
						case "1":
							$result .= $this->getUserinInformation($paramLinkTarget, $itemid, $lastName, $groupID, $id, $memberitem);
							break;
						case "2":
							$result .= $this->getUserinInformation($paramLinkTarget, $itemid, $lastName, $groupID, $id, $memberitem);
							break;
						case "5":
							$result .= nl2br(htmlspecialchars_decode($memberitem->value));
							break;
						case "4":
							// EMail
							$result .= '<span class="respEmail"><a href="mailto:' . $memberitem->value . '" class="btn" role="button">'
								. '<span class="icon-mail-2"></span></a></span>';
							$result .= '<span class="respEmail-hidden">' . JHTML::_('email.cloak', $memberitem->value) . '</span>';
							break;
						default:
							switch ($memberitem->type)
							{
								case "LINK":
									$result .= "<span class='respEmail'><a href='"
										. htmlspecialchars_decode($memberitem->value) . "' class='btn' role='button'>"
										. "<span class='icon-home'></span>"
										. "</a></span>"
										. "<span class='respEmail-hidden'>"
										. "<a href='" . htmlspecialchars_decode($memberitem->value) . "'>"
										. htmlspecialchars_decode($memberitem->value) . "</a>"
										. "</span>";
									break;
								case "PICTURE":
									// TODO
									break;
								case "TABLE":
									$result .= $this->make_table($memberitem->value);
									break;
								case "TEXTFIELD":
									// Long Info
									$text = JString::trim(htmlspecialchars_decode($memberitem->value));

									if (!empty($text))
									{
										if (stripos($text, '<li>') === false && stripos($text, '<table') === false)
										{
											$text = nl2br($text);
										}
										// Truncate Long Info Text
										if ($truncateLongInfo)
										{
											$result .= '<span class="respEmail btn" onclick="toogle(this);" style="width: 26%;">'
												. '<span class="thm_groups_profile_container_profile_read_more">'
												. $memberitem->name . '</span></span>';
											$result .= '<span class="thm_groups_profile_container_profile_read_more respEmail-hidden">'
												. JText::_('COM_THM_GROUPS_PROFILE_CONTAINER_LONG_INFO_READ_MORE') . '</span>';
											$result .=
												'<div class="thm_groups_profile_container_profile_long_info" style="display:none;"><br/>'
												. $text
												. '<br/></div>';
										}
										else
										{
											$result .= '<div class="thm_groups_profile_container_profile_long_info">' . $text . '</div>';
										}
									}
									break;
								default:
									$result .= nl2br(htmlspecialchars_decode($memberitem->value));
									break;
							}
							break;
					}

					$result .= "</div>";

					if ($memberitem->structwrap == true)
					{
						$wrap = true;
					}
					else
					{
						$wrap = false;
						$result .= " ";
					}
				}
			}

			$result .= "</div>";

			$result .= '<div class="clearfix"></div>';
			$result .= "</div>";

			// Close Content Wrapper Tag
			$result .= '</div>';

			// Close Coloumn Tag
			$result .= '</div>';

			// Close Coloumn Wrapper Tag
			$result .= '</div>';

			// Close Wrapper Tag
			if (($elementCounter + 1) % $countOfColoumns == 0)
			{
				$result .= '<div class="clearfix"></div>';
				$result .= '</div>';
			}

			if (($elementCounter % 2 == 0) && ($elementCounter == $lastIndex))
			{
				if ($countOfColoumns == 2)
				{
					$result .= '</div>';
				}
			}

			// Count Elements
			$elementCounter++;

		}
		// Truncate Long Info Text
		if ($truncateLongInfo)
		{
			$result .= '<script type="text/javascript">
                            jQuery(".thm_groups_profile_container_profile_read_more").click(
                                function() {
                                    jQuery(this).next().slideToggle();});
                            jQuery("#openFrame").click(function(){
                            jQuery("#thm_groups_profile").on("show", function () {});
                            jQuery("#thm_groups_profile").modal({show:true})});

                            function toogle(caller){
                                if(caller.nextElementSibling.nextElementSibling.style.display == "none"){
                                    caller.nextElementSibling.nextElementSibling.style.display = "inherit";
                                }
                                else
                                {
                                    caller.nextElementSibling.nextElementSibling.style.display = "none";
                                }
                            }
                        </script>';
		}

		return $result;
	}

	/**
	 * Return a navigation for the mobile Context
	 *
	 * @param   mixed $attribs Attributes
	 *
	 * @return  String
	 */
	public function getMobileNavbar($attribs)
	{
		/* Put the first letter of a name and all names for
		 * this latter in an array.
		 */

		$list             = array();
		$list['alphabet'] = array();
		$list['names']    = array();
		$list['names']    = array();
		$namesCounter     = 0;

		foreach ($attribs as $user)
		{
			foreach ($user as $object)
			{
				if ($object->structid == '2')
				{
					if (!in_array(substr($object->value, 0, 1), $list['alphabet']))
					{
						$currentLetter = substr($object->value, 0, 1);
						array_push($list['alphabet'], $currentLetter);
						$list['names'][$namesCounter] = array();

						foreach ($attribs as $namesForLetter)
						{
							foreach ($namesForLetter as $name)
							{
								if (($name->structid == '2') && (substr($name->value, 0, 1) == $currentLetter))
								{
									array_push($list['names'][$namesCounter], $name->value);
								}
							}
						}

						$namesCounter++;
					}
				}
			}
		}

		$navbar = "";
		$navbar .= "<div id='mob_nav' class='navbar navbar-inverse navbar-fixed-top'>";
		$navbar .= "<div class='navbar-inner'>";
		$navbar .= "<div class='container'>";

		$navbar .= "<a class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>";
		$navbar .= "<span class='icon-bar'></span>";
		$navbar .= "<span class='icon-bar'></span>";
		$navbar .= "<span class='icon-bar'></span>";
		$navbar .= "</a>";

		$navbar .= "<div class='nav-collapse collapse navbar-responsive-collapse'>";
		$navbar .= "<ul id='adv_nav_menu' class='nav'>";

		// Generate the list items
		$navbar .= $this->getUserList($list);

		$navbar .= "</ul>";
		$navbar .= "</div>";
		$navbar .= "</div>";
		$navbar .= "</div>";
		$navbar .= "</div>";

		return $navbar;
	}

	/**
	 * Get the nav items HTML as string.
	 *
	 * @param   Array $list Array of Users sorted by first letter of name
	 *
	 * @return  String
	 */
	public function getUserList($list)
	{

		$navItems = "";

		for ($i = 0; $i < sizeof($list['alphabet']); $i++)
		{
			$navItems .= "<li class='dropdown'>"
				. "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button'"
				. "aria-haspopup='true' aria-expanded='false'>"
				. $list['alphabet'][$i] . "<span class='caret'></span></a>";

			$navItems .= "<ul class='dropdown-menu'>";

			foreach ($list['names'][$i] as $name)
			{
				$navItems .= "<li style='height: 44px; width: 100%;'><a href='#" . $name . "'>" . $name . "</a></li>";
			}

			$navItems .= "</ul>";
			$navItems .= "</li>";
			$navItems .= "<li class='divider'></li>";
		}

		return $navItems;
	}
}
