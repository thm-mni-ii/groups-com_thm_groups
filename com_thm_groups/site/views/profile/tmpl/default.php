<?php
/**
 * @version     v3.4.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @description THMGroupsViewProfile file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal', 'a.modal-button');
JHTML::_('behavior.calendar');

$user = JFactory::getUser();

$componentparams = JComponentHelper::getParams('com_thm_groups');

$canEdit = (($user->id == $this->userid && $componentparams->getValue('editownprofile', '0') == 1) || $this->canEdit);

$model = new THMLibThmGroupsUser;

// Get user information
$userInfoAsObject = $model::getUserInfo($this->userid);
$userInfoArray = $userInfoAsObject->profilInfos;

$html = buildHtmlOutput($this->userid, $userInfoArray);

// Get css
$mycss = getProfilCss();

$document = JFactory::getDocument();
$document->addStyleDeclaration($mycss);

// Print HTML
echo $html;

// ************************************Functions************************************

/**
 * Returns styled information about user
 *
 * @param   Integer  $userid    contains user id
 *
 * @param   Array    $userData  user data
 *
 * @return information about user
 */
function buildHtmlOutput($userid, $userData)
{
    // Class for title, first name, name, post title and portrait image
    $head = '<div class="contentheading">';

    // Class for user information
    $body = '<div class="contentbody">';

    // Contains at the end $head and $body
    $result = '';

    $firstPic = true;

    // For edit url
    $attribut = THMLibThmGroupsUser::getUrl(array("name", "gsuid", "gsgid"));
    $result .= '<div class="thm_groups_content_profile">';

    // For edit Button
    $tempName = JFactory::getUser($userid)->get('name');
    $nameArray = explode(" ", $tempName);
    $lastName =(array_key_exists(1, $nameArray) ? $nameArray[1] : "");

    if ($userid)
    {
        $struct = array();

        // Save structure names
        foreach (THMLibThmGroupsUser::getStructure() as $structItem)
        {
            $struct[$structItem->id] = $structItem->field;
        }

        // Edit icon
        if ($userid == JFactory::getUser()->id && THMLibThmGroupsUser::canEdit() == true)
        {
            $head .= '<div class="thm_groups_content_profile_edit">';

            $head .= "<a href='" . JRoute::_('index.php?option=com_thm_groups&view=edit&layout=default&' . $attribut . '&gsuid=' . $userid
                    . '&name=' . trim($lastName) . '&gsgid=4' ) . "'>"
                    . JHTML::image("libraries/thm_groups/assets/icons/edit.png", 'bearbeiten', 'bearbeiten') . "</a>";
            $head .= '</div>';
        }

        // Loop through all structures
        for ($index = 0; $index < count($userData); $index ++)
        {

            $data = $userData[$index];

            if ($data->value != "" && $data->publish)
            {
                switch ($data->structid)
                {
                    // Vorname
                    case "1" :
                        $head .= '<div class="thm_groups_head_text" id="' . $struct[$data->structid] . '">';
                        $head .= '<h2>' . $data->value . '&nbsp;</h2>';
                        $head .= '</div>';
                        break;

                    // Nachname
                    case "2" :
                        $head .= '<div class="thm_groups_head_text" id="' . $struct[$data->structid] . '">';
                        $head .= '<h2>' . $data->value . '&nbsp;</h2>';
                        $head .= '</div>';
                        break;

                    // Titel
                    case "5" :
                        $head .= '<div class="thm_groups_head_text" id="' . $struct[$data->structid] . '">';
                        $head .= '<h2>' . $data->value . '&nbsp;</h2>';
                        $head .= '</div>';
                        break;

                    // Posttitel
                    case "7" :
                        $head .= '<div class="thm_groups_head_text" id="' . $struct[$data->structid] . '">';
                        $head .= '<h2>' . $data->value . '&nbsp;</h2>';
                        $head .= '</div>';
                        break;

                    // EMail
                    case "4" :
                        $body .= '<div class="thm_groups_field_container" id="' . $struct[$data->structid] . '_field">';
                        $body .= '<div class="thm_groups_label" id="' . $struct[$data->structid] . '_label">';
                        $body .= '<b>' . $struct[$data->structid] . ':</b>';
                        $body .= '</div>';
                        $body .= '<div class="thm_groups_value" id="' . $struct[$data->structid] . '_value">';
                        $body .= JHTML::_('email.cloak', $data->value, 1, $data->value, 0);
                        $body .= '</div>';
                        $body .= '</div>';
                        break;
                    default :
                        switch ($data->type)
                        {
                            case "LINK" :
                                if ($data->type == 'LINK' && trim($data->value) != "")
                                {
                                    $body .= '<div class="thm_groups_field_container" id="' . $struct[$data->structid] . '_field">';
                                    $body .= '<div class="thm_groups_label" id="' . $struct[$data->structid] . '_label">';
                                    $body .= '<b>' . $struct[$data->structid] . ':</b>';
                                    $body .= '</div>';
                                    $body .= '<div class="thm_groups_value" id="' . $struct[$data->structid] . '_value">';
                                    $body .= "<a href='" . htmlspecialchars_decode($data->value) . "'>"
                                             . htmlspecialchars_decode($data->value) . "</a>";
                                    $body .= '</div>';
                                    $body .= '</div>';
                                }
                                break;

                            case "PICTURE" :
                                $attribs['class'] = 'picture';
                                $attribs['id'] = 'pic_' . $data->structid;
                                $path = THMLibthmGroupsUser::getPicPath($data->structid);

                                // The first structure of the type image will be a portrait picture
                                if ($firstPic)
                                {
                                    $image = JHTML::image("$path" . '/' . $data->value, 'Portrait', $attribs);
                                    $head .= $image;
                                    $firstPic = false;

                                }

                                // All other pictures
                                else
                                {
                                    $body .= '<div class="thm_groups_field_container" id="' . $struct[$data->structid] . '_field">';
                                    $body .= '<div class="thm_groups_label" id="' . $struct[$data->structid] . '_label">';
                                    $body .= '<b>' . $struct[$data->structid] . ':</b>';
                                    $body .= '</div>';
                                    $body .= '<div class="thm_groups_value" id="' . $struct[$data->structid] . '_value">';
                                    $image = JHTML::image("$path" . $data->value, 'Image', $attribs);
                                    $body .= $image;
                                    $body .= '</div>';
                                    $body .= '</div>';
                                }

                                break;

                            case "TABLE" :
                                $body .= '<div class="thm_groups_field_container" id="' . $struct[$data->structid] . '_field">';
                                $body .= '<div class="thm_groups_label" id="' . $struct[$data->structid] . '_label">';
                                $body .= '<b>' . $struct[$data->structid] . ':</b>';
                                $body .= '</div>';
                                $body .= '<div class="thm_groups_table" id="' . $struct[$data->structid] . '_value">';
                                $body .= getTable($data->value);
                                $body .= '</div>';
                                $body .= '</div>';
                                break;

                            default :
                                $body .= '<div class="thm_groups_field_container" id="' . $struct[$data->structid] . '_field">';
                                $body .= '<div class="thm_groups_label" id="' . $struct[$data->structid] . '_label">';
                                $body .= '<b>' . $struct[$data->structid] . ':</b>';
                                $body .= '</div>';
                                $body .= '<div class="thm_groups_value" id="' . $struct[$data->structid] . '_value">';
                                $body .= nl2br(htmlspecialchars_decode($data->value));
                                $body .= '</div>';
                                $body .= '</div>';
                                break;
                        }
                        break;
                }
            }
        }
    }

    $head .= '</div>';
    $body .= '</div>';
    $result .= $head . $body;
    $result .= '</div>';

    return $result;
}

/**
 * getTable
 *
 * @param   Array  $data  data
 *
 * @return String
 */
function getTable($data)
{
    $jsonTable = json_decode($data);
    $table = "<table class='table'><tr>";
    foreach ($jsonTable[0] as $key => $value)
    {
        $headItem = str_replace("_", " ", $key);
        $table = $table . "<th>" . $headItem . "</th>";
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
 * Generates the css code
 *
 * @return String with css code
 */
function getProfilCss()
{
    // Here you can define or change css styles
    $out = '';
    $out .= '
            .thm_groups_content_profile
            {
                width:400px;
            }

            .contentheading > div
            {
                float:left;
            }

            .contentheading img
            {
                float:left;
                clear:both;
            }

            .thm_groups_content_profile_edit
            {
                float:right !important;
            }

            .contentbody > div
            {
                float:left;
                clear:both;
            }

            .thm_groups_field_container
            {
                margin-top:20px;
            }

            .thm_groups_field_container >div
            {
                float:left;
            }

            .thm_groups_label
            {
                width:70px;
            }

            .thm_groups_value
            {
                margin-left:20px;
            }
            ';
    return $out;
}
