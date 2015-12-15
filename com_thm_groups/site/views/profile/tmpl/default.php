<script>
    // TODO save it in an external script
    $ = jQuery.noConflict();
    $(document).ready(function() {
        $('#sbox-btn-close').on('click', function(){
            window.parent.location.reload();
        });
    });
</script>

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

// Include Bootstrap
JHtmlBootstrap::loadCSS();

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
    $head = '<div id="cHead" class="thm_groups_contentheading">';

    // Class for user information
    $body = '<div class="thm_groups_contentbody">';

    // Contains at the end $head and $body
    $result = '';

    $firstPic = true;
    $componentparams = JComponentHelper::getParams('com_thm_groups');

    // For edit url
    $attribut = THMLibThmGroupsUser::getUrl(array("name", "gsuid", "gsgid"));
    $result .= '<div class="row-fluid">';
    $result .= '<div class="thm_groups_content_profile span12">';
    $result .= '<div id="j-main-container">';

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

            /*$head .= "<a href='" . JRoute::_('index.php?option=com_thm_groups&view=user_edit&layout=default&' . $attribut . '&gsuid=' . $userid
                    . '&name=' . trim($lastName) . '&gsgid=4' ) . "'>"
                    . JHTML::image("libraries/thm_groups/assets/icons/edit.png", 'bearbeiten', 'bearbeiten') . "</a>";*/

            $attribs['title'] = 'bearbeiten';

            $path = "index.php?option=com_thm_groups&view=user_edit&layout=default&tmpl=component";

            // TODO use existing group id and not just a number
            $gspart = '&gsgid=4';
            $trim = "&name=" . trim($lastName);
            $head .= "<a href='" . JRoute:: _(
                    $path . '&gsuid=' . $userid . $trim . $gspart
                )
                . "' class='modal' rel='{size: {x: 1000, y: 600}, handler: \"iframe\", onClose: \"window.location.reload();\"}'>"
                . JHTML:: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";
            $head .= '</div>';
        }

        // Temporary variable to add pic after the user name.
        $profilePic = "";

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

                                // Get the thumbnails from folder
                                $temp = getResizedPictures($data->value, $path);

                                // Extract small and medium filename
                                if ($temp != null)
                                {
                                    if ($temp[0][1] === 'small')
                                    {
                                        $small = $temp[0][0];
                                        $medium = $temp[1][0];
                                    }
                                    else
                                    {
                                        $small = $temp[1][0];
                                        $medium = $temp[0][0];
                                    }
                                }

                                // The first structure of the type image will be a portrait picture
                                if ($firstPic)
                                {
                                    // $image = JHTML::image($path . '/' . $data->value, 'Portrait', $attribs);
                                    $profilePic .= "<div id='pictureContainer'>";
                                    $root = JURI::root(true);

                                    $imgDimensions = getimagesize(JPATH_BASE . "/" . $path . "/" . $data->value);

                                    $image = "<a href='" . $root . "/" . $path . "/" . $data->value . "'"
                                     . "class='modal'"
                                     . "rel='{size: {x: " . $imgDimensions[0] . ", y: " . $imgDimensions[1] . "}}'>"
                                     . "<img class='Portrait' ";

                                    // Pictures don't have to be in the defined resolution, it's just a value for
                                    // the browser to decide what it should load. 'w' is not supported by iOS yet.
                                    if ($temp != null)
                                    {
                                        $image .= "srcset='" . $root . "/" . $path . "thumbs/" . $small . " 480w";
                                        $image .= ", " . $root . "/" . $path . "thumbs/" . $medium . " 600w";
                                        $image .= ", " . $root . "/" . $path . "/" . $data->value . " 700w' ";

                                        // Outcomment this, if picturefill is used. Otherwise this picture will load twice.
                                        $image .= "src='" . $root . "/" . $path . "thumbs/" . $small . "'";
                                    }
                                    else
                                    {
                                        // Outcomment this, if picturefill is used. Otherwise this picture will load twice.
                                        $image .= "src='" . $root . "/" . $path . "/" . $data->value . "'";
                                    }

                                    $image .= " alt='Profilbild' /></a>";

                                    $profilePic .= $image;
                                    $profilePic .= "</div>";
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

                                    // $image = JHTML::image("$path" . '/' . $data->value, 'Image', $attribs);
                                    $image = "<img class='Image' ";

                                    if ($temp != null)
                                    {
                                        $image .= "srcset='" . $path . "/thumbs/" . $small . " 480w";
                                        $image .= ", " . $path . "/thumbs/" . $medium . " 600w";
                                        $image .= ", " . $path . "/" . $data->value . " 700w' ";
                                    }

                                    $image .= "src='" . $path . "/" . $data->value . "'";
                                    $image .= " alt='Bild' />";
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
                                $body .= '<div class="thm_table_area">';
                                $body .= getTable($data);
                                $body .= '</div>';
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

    $head .= $profilePic;
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
    $result .= '</div></div></div>';

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
    $document = JFactory::getDocument();
    $tableHeaders = "@media only screen and (max-width: 760px){
    #" . $data->name . "'_value .thm_table_area{";

    // Get header of table from DB
    $result = json_decode($data->options);

    $jsonTable = json_decode($data->value);

    $table = "<table class='table'><tr>";

    $titles = explode(';', $result[0]);

    $tableHeaderscounter = 1;

    foreach ($titles as $title)
    {
        $table = $table . "<th>" . $title . "</th>";

        // Write table header into td for mobile view.
        // TODO: test this!
        $tableHeaders .= "
        td:nth-of-type(" . $tableHeaderscounter . "):before { content: " . $title . "; }";
        $tableHeaderscounter ++;
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

    // Write CSS for table headers.
    $tableHeaders .= "}";
    $document->addStyleDeclaration($tableHeaders);

    return $table;
}

/**
 * Searches smaller pictures for responsive images.
 * Compares the pictures in the thumb folder and marks them as
 * small or medium, based on the file size.
 * The given path has to be the attribute path of the picture.
 * The thumb folder is relative to the attribute path.
 * Returns an array that consists of 2 arrays with the picture filename
 * and the tag 'small' or 'medium'. Return null if no files found.
 *
 * Image sizes of thumbs are generated in the
 * user_edit model at the backend, change them if needed.
 *
 * @param   String  $filename  The name of the picture
 * @param   String  $path      The path of the picture
 *
 * @return  array, null
 */
function getResizedPictures($filename, $path)
{
    $pictures = array();
    $picsFound = 0;
    $pathToThumbs = $path . 'thumbs\\';

    if (is_dir($pathToThumbs) != false)
    {
        $folder = scandir($pathToThumbs);

        foreach ($folder as $folderPic)
        {
            if ($folderPic === '.' || $folderPic === '..')
            {
                continue;
            }
            else
            {
                /**
                 * Get the filename till the '_width-height.extension' part
                 * and check if its part of the saved filename in database.
                 *
                 * When a pos was found it will be dropped from the folder.
                 */
                $extPos = strrpos($folderPic, '_');
                $length = strlen($folderPic);
                $thumbFileName = substr($folderPic, 0, -($length - $extPos));

                $pos = strpos($filename, $thumbFileName);

                if ($pos === 0)
                {
                    $temp = array();
                    $temp[0] = $folderPic;
                    array_push($pictures, $temp);
                    $picsFound ++;
                }
            }
        }
    }

    if ($picsFound != 0)
    {
        if (filesize($path . '/thumbs/' . $pictures[0][0]) < filesize($path . '/thumbs/' . $pictures[1][0]))
        {
            $pictures[1][1] = 'medium';
            $pictures[0][1] = 'small';
        }
        else
        {
            $pictures[0][1] = 'medium';
            $pictures[1][1] = 'small';
        }

        return $pictures;
    }
    else
    {
        return null;
    }
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

            .field_container{
                width: 100%;
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
                width: 100px;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .field_container > .value
            {
                width: 80%;
            }

            @media screen and (min-width: 480px){
                .field_container > .value{
                     margin-left: 20px;
                }
            }

            .field_container > .table
            {
                margin-left: 20px;
            }

            #pictureContainer{
                width: 30%;
            }

            @media screen and (min-width: 600px){
                .Portrait{
                    max-width:45%;
                }
            }

            .sbox-content-image img{
                height: auto !important;
                width: auto !important;
                max-height: 100% !important;
                max-width: 100% !important;
                margin: 0px auto !important;
            }
            ';

    return $out;
}


