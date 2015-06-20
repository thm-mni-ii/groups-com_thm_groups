<?php
/**
 * @version     v3.4.6
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
 * @author      Dieudonne Timma, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal', 'a.modal-button');
JHTML::_('behavior.calendar');

$user = JFactory::getUser();

$componentparams = JComponentHelper::getParams('com_thm_groups');

$backLink = $this->backRef;
$canEdit = (($user->id == $this->userid && $componentparams->get('editownprofile', 0) == 1 ) || $this->canEdit);
$model = new THMLibThmGroupsUser;

// Get user information
$userInfoArray =  $this->model->getData();

$backAttribute = $this->backAttribute;

$html = buildHtmlOutput($this->userid, $userInfoArray, $backLink, $backAttribute,$canEdit);

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
 * @param   Integer  $userid         contains user id
 *
 * @param   Array    $userData       user data
 *
 * @param   String   $backLink       back link
 *
 * @param   String   $backAttribute  back params
 *
 * @return information about user
 */
function buildHtmlOutput($userid, $userData, $backLink, $backAttribute,$canEdit)
{
    // Class for title, first name, name, post title and portrait image
    $head = '<div class="thm_groups_contentheading">';

    // Class for user information
    $body = '<div class="thm_groups_contentbody">';

    // Contains at the end $head and $body
    $result = '';

    $firstPic = true;
    $componentparams = JComponentHelper::getParams('com_thm_groups');

    // For edit url
    $attribut = THMLibThmGroupsUser::getUrl(array("name", "gsuid", "gsgid"));
    $result .= '<div class="thm_groups_content_profile">';

    // For edit Button
    $tempName = JFactory::getUser($userid)->get('name');
    $nameArray = explode(" ", $tempName);
    $lastName =(array_key_exists(1, $nameArray) ? $nameArray[1] : "");

    if ($userid)
    {


        // Edit icon
        if (($userid == JFactory::getUser()->id && $componentparams->get('editownprofile', 0) == 1) || $canEdit == true)
        {
            $head .= '<div class="thm_groups_content_profile_edit">';

            $head .= "<a href='" . JRoute::_('index.php?option=com_thm_groups&view=user_edit&layout=default&' . $attribut . '&gsuid=' . $userid
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
                        $head .= '<span class="head_text" id="' . $data->name . '">';
                        $head .= $data->value . '&nbsp;';
                        $head .= '</span>';
                        break;

                    // Nachname
                    case "2" :
                        $head .= '<span class="head_text" id="' . $data->name . '">';
                        $head .= $data->value . '&nbsp;';
                        $head .= '</span>';
                        break;

                    // Titel
                    case "5" :
                        $head .= '<span class="head_text" id="' . $data->name . '">';
                        $head .= $data->value . '&nbsp;';
                        $head .= '</span>';
                        break;

                    // Posttitel
                    case "6" :
                        $head .= '<span class="head_text" id="' . $data->name . '">';
                        $head .= $data->value . '&nbsp;';
                        $head .= '</span>';
                        break;

                    // EMail
                    case "4" :
                        $body .= '<div class="field_container" id="' . $data->name . '_field">';
                        $body .= '<div class="label" id="' . $data->name . '_label">';
                        $body .= $data->name . ':';
                        $body .= '</div>';
                        $body .= '<div class="value" id="' . $data->name . '_value">';
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
                                    $body .= '<div class="field_container" id="' . $data->name . '_field">';
                                    $body .= '<div class="label" id="' . $data->name . '_label">';
                                    $body .= $data->name . ':';
                                    $body .= '</div>';
                                    $body .= '<div class="value" id="' . $data->name . '_value">';
                                    $body .= "<a href='" . htmlspecialchars_decode($data->value) . "'>"
                                             . htmlspecialchars_decode($data->value) . "</a>";
                                    $body .= '</div>';
                                    $body .= '</div>';
                                }
                                break;

                            case "PICTURE" :
                                $attribs['class'] = 'picture';
                                $attribs['id'] = 'pic_' . $data->structid;
                                $path = THMLibthmGroupsUser::getPicPathValue($data->structid);

                                // The first structure of the type image will be a portrait picture
                                if ($firstPic)
                                {
                                    $image = JHTML::image($path . '/' . $data->value, 'Portrait', $attribs);
                                    $head .= $image;
                                    $firstPic = false;

                                }

                                // All other pictures
                                else
                                {
                                    $body .= '<div class="field_container" id="' . $data->name . '_field">';
                                    $body .= '<div class="label" id="' . $data->name . '_label">';
                                    $body .= $data->name . ':';
                                    $body .= '</div>';
                                    $body .= '<div class="value" id="' . $data->name . '_value">';
                                    $image = JHTML::image("$path" . '/' . $data->value, 'Image', $attribs);
                                    $body .= $image;
                                    $body .= '</div>';
                                    $body .= '</div>';
                                }

                                break;

                            case "TABLE" :
                                $body .= '<div class="field_container" id="' . $data->name . '_field">';
                                $body .= '<div class="label" id="' . $data->name . '_label">';
                                $body .= $data->name . ':';
                                $body .= '</div>';
                                $body .= '<div class="table" id="' . $data->name . '_value">';
                                $body .= getTable($data);
                                $body .= '</div>';
                                $body .= '</div>';
                                break;

                            default :
                                $body .= '<div class="field_container" id="' . $data->name . '_field">';
                                $body .= '<div class="label" id="' . $data->name . '_label">';
                                $body .= $data->name . ':';
                                $body .= '</div>';
                                $body .= '<div class="value" id="' . $data->name . '_value">';
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

    if (JComponentHelper::getParams('com_thm_groups')->get('backButtonForProfile') == 1)
    {
        if (empty($backAttribute))
        {

            // Back button with javascript
            $body .= '<div><input type="button" class="btn btn-default" style="margin-top:10px" value="'
                    . JText::_("COM_THM_GROUPS_BACK_BUTTON") . '" onclick="window.history.back()" /> </div>';
        }
        else
        {
            // Back button with self generated link
            $body .= '<div><a href="' . $backLink . '"><input type="button" class="btn btn-default" style="margin-top:10px" value="'
                    . JText::_("COM_THM_GROUPS_BACK_BUTTON") . '" /></div>';
        }
    }

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

    // Get header of table from DB
    $result = json_decode($data->options);

    $jsonTable = json_decode($data->value);

    $table = "<table class='table'><tr>";

    $titles = explode(';', $result[0]);
    foreach ($titles as $title)
    {
        $table = $table . "<th>" . $title . "</th>";
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
                width:100%;
            }

            .thm_groups_contentheading > span
            {
                float:left;
                font-size: 1.375em;
            }

            .thm_groups_contentheading img
            {
                margin-top: 10px;
                float:left;
                clear:both;
            }

            .thm_groups_content_profile_edit
            {
                float:right !important;
            }

            #content .breadcrumb{
                margin: 0px 0px 18px 0px;
            }

            .thm_groups_contentbody > div
            {
                float:left;
                clear:both;
            }

            .thm_groups_contentbody > .field_container
            {
                margin-top:20px;
            }

            .field_container > div
            {
                float:left;
            }

            .field_container > .label
            {
                width:90px;
                font-weight: bold;
            }

            .field_container > .value
            {
                margin-left:20px;
                width:500px;
            }

            .field_container > .table
            {
                margin-left: 20px;
            }
            ';
    return $out;
}
