<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @description THMGroupsViewAdvanced file from com_thm_groups
 * @author      Dennis Priefer,  <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,   <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,   <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,   <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis,  <niklas.simonis@mni.thm.de>
 * @author      Peter May,       <peter.may@mni.thm.de>
 * @author      Alexander Boll,  <alexander.boll@mni.thm.de>
 * @author      Tobias Schmitt,  <tobias.schmitt@mni.thm.de>
 * @author		Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author		Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
?>
<div id="title"><?php echo "<h2 class='contentheading'>" . $this->title . "</h2>" ?></div>
<div id="thm_groups_profile_container_list">

<?php
    // Show Profiles
    $members = $this->data;
    $User = JFactory::getUser();

    $struct = array();
    foreach ($this->structure as $structItem)
    {
        $struct[$structItem->id] = $structItem->field;
    }

    $countOfColoumns = $this->view + 1;
    $elementCounter = 0;
    $rowCounter = 0;
    foreach ($members as $id => $member)
    {
        /*
        var_dump($member);
        $UserInfo = new THMLibThmGroups;
        $user = $UserInfo->userInfodata($id, $this->params->get('struct'));
        var_dump($user);
        die();
        */


        // Open Row Tag - Even / Odd
        if ($elementCounter % $countOfColoumns == 0)
        {
            // Count Elements
            $rowCounter++;

            $cssListRowClass = ($rowCounter % 2) ? '_odd': '_even';
            echo '<div class="thm_groups_profile_container_list_row' . $cssListRowClass . '">';
        }

        // Open Coloumn Wrapper Tag - Only for float-attribute, now is easy to work with width:100%
        if ($countOfColoumns == 1)
        {
            $cssListColoumnClass = '_full';
        }
        else
        {
            $cssListColoumnClass = ($elementCounter % $countOfColoumns == 0) ? '_left': '_right';
        }
        echo '<div class="thm_groups_profile_container_list_coloumn_wrapper thm_groups_profile_container_list_coloumn_wrapper'
            . $cssListColoumnClass . '">';

        // Open Coloumn Tag - Only for dimensions
        echo '<div class="thm_groups_profile_container_list_coloumn">';

        // Open Content Wrapper Tag - For Properties like padding, border etc.
        echo '<div class="thm_groups_profile_container_list_coloumn_content_wrapper">';

        // Load Profile Content
        $title = "";
        $firstName = "";
        $lastName = "";
        $picture = null;
        $picpath = null;
        $paramLinkTarget = $this->params->get('linkTarget');
        $wrapTitle = false;
        $wrapFirstName = false;
        $componentparams = JComponentHelper::getParams('com_thm_groups');
        $canEdit = (($User->id == $id && $componentparams->getValue('editownprofile', '0') == 1) || $this->canEdit);
        foreach ($member as $memberhead)
        {
            // Daten fuer den HEAD in Variablen speichern
            switch ($memberhead['structid'])
            {
                case "1":
                    $firstName = $memberhead['value'];
                    $wrapFirstName = $memberhead['structwrap'];
                    break;
                case "2":
                    $lastName = $memberhead['value'];
                    break;
                case "5":
                    $title = $memberhead['value'];
                    $wrapTitle = $memberhead['structwrap'];
                    break;
                default:
                    if ($memberhead['type'] == "PICTURE" && $picture == null && $memberhead['publish'])
                    {
                        $picture = $memberhead['value'];
                        $picpath = $memberhead['picpath'];
                    }
                    break;
            }
        }

        echo "<div id='secondWrapper'>";

        // Darstellen des Portraits
        if ($picture != null)
        {
            echo JHTML :: image($picpath . '/' . $picture, "Portrait", array ('class' => 'thm_groups_profile_container_profile_image'));
        }

        // Darstellen des Links (Titel, Vorname, Name)
        echo "<div id='gs_advlistTopic'>";
        $displayInline = " style='display: inline'";
        if (trim($title) != "")
        {
            echo "<div class='gs_advlist_longinfo'" . ($wrapTitle ? "" : $displayInline) . ">" . trim($title) . "</div> ";
        }
        switch ($paramLinkTarget)
        {
            case "module":
                $path = "index.php?option=com_thm_groups&view=advanced&layout=list&Itemid=";
                echo "<a href="
                        . JRoute::_($path . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) . '&gsgid=' . $this->gsgid)
                        . ">";
                break;
            case "profile":
                $path = 'index.php?option=com_thm_groups&view=profile&layout=default';
                echo "<a href="
                        . JRoute::_($path . '&gsuid=' . $id . '&name=' . trim($lastName) . '&gsgid=' . $this->gsgid)
                        . ">";
                break;
            default:
                $path = "index.php?option=com_thm_groups&view=advanced&layout=list&Itemid=";
                echo "<a href="
                        . JRoute::_($path . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) . '&gsgid=' . $this->gsgid)
                        . ">";
        }

        if (trim($firstName) != "")
        {
            echo "<div class='gs_advlist_longinfo'" . ($wrapTitle && $wrapFirstName ? "" : $displayInline) . ">" . trim($firstName) . "</div> ";
        }
        if (trim($lastName) != "")
        {
            echo "<div class='gs_advlist_longinfo'" . ($canEdit || !$wrapFirstName ? $displayInline : "") . ">" . trim($lastName) . "</div>";
        }
        echo "</a>";
        $canEdit = (($User->id == $id && $componentparams->getValue('editownprofile', '0') == 1) || $this->canEdit);

        // Jeder Benutzer kann sich selbst editieren
        if ($canEdit)
        {
            $attribs['title'] = 'bearbeiten';

            // Daten fuer die EditForm
            $option = JRequest :: getVar('option', 0);
            $layout = JRequest :: getVar('layout', 0);
            $view = JRequest :: getVar('view', 0);
            $path = "index.php?option=com_thm_groups&view=edit&layout=default&Itemid=";
            $gspart = '&gsgid=' . $this->gsgid . '&option_old=';
            $trim = "&name=" . trim($lastName);
            echo "<a href="
                    . JRoute :: _(
                            $path . $this->itemid . '&gsuid=' . $id . $trim . $gspart . $option . '&view_old=' . $view . '&layout_old=' . $layout
                    )
                    . ">"
                            . JHTML :: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";
        }
        echo "</div>";

        $wrap = true;

        // Rest des Profils darstellen
        echo "<div>";
        foreach ($member as $memberitem)
        {
            if ($memberitem['value'] != "" && $memberitem['publish'])
            {
                if ($wrap == true && $memberitem['structwrap'] == true)
                {
                    echo "<div class='gs_advlist_longinfo thm_groups_profile_container_line'>";
                }
                else
                {
                    echo "<div style='display: inline;'>";
                }
                // Attributnamen anzeigen

                if ($memberitem['structname'] == true)
                {
                    echo '<span class="thm_groups_profile_container_line_label">' . JText::_($struct[$memberitem['structid']]) . ": " . '</span>';
                }

                // Attribut anzeigen
                switch ($memberitem['structid'])
                {
                    // Reihenfolge ist von ORDER in Datenbank abhaengig. somit ist hier die Reihenfolge egal
                    case "1":
                        // Vorname
                    case "2":
                        // Nachname
                    case "5":
                        // Titel
                        // Diese Daten wurden vorher verarbeitet
                        break;
                    case "4":
                        // EMail
                        echo JHTML :: _('email.cloak', $memberitem['value']);
                        break;
                    case 96:
                        // Long Info
                        $text = JString::trim(htmlspecialchars_decode($memberitem['value']));
                        if (!empty($text))
                        {
                            if (stripos($text, '<li>') === false)
                            {
                                $text = nl2br($text);
                            }
                            // Truncate Long Info Text
                            if ($this->truncateLongInfo)
                            {
                                echo '<span class="thm_groups_profile_container_profile_read_more">' .
                                    JText::_('COM_THM_GROUPS_PROFILE_CONTAINER_LONG_INFO_READ_MORE') . '</span>';
                                echo '<div class="thm_groups_profile_container_profile_long_info" style="display:none;">' . $text . '</div>';
                            }
                            else
                            {
                                echo '<div class="thm_groups_profile_container_profile_long_info">' . $text . '</div>';
                            }
                        }
                        break;
                    default:
                        switch ($memberitem['type'])
                        {
                            case "LINK":
                                echo "<a href='" . htmlspecialchars_decode($memberitem['value']) . "'>"
                                        . htmlspecialchars_decode($memberitem['value']) . "</a>";
                                        break;
                            case "PICTURE":
                                // TODO
                                break;
                            case "TABLE":
                                echo $this->make_table($memberitem['value']);
                                break;
                            default:
                                echo nl2br(htmlspecialchars_decode($memberitem['value']));
                                break;
                        }
                        break;
                }

                echo	"</div>";
                if ($memberitem['structwrap'] == true)
                {
                    $wrap = true;
                }
                else
                {
                    $wrap = false;
                    echo " ";
                }
            }
            else
            {
            }
        }
        echo "</div>";

        echo '<div class="clearfix"></div>';
        echo "</div>";

        // Close Content Wrapper Tag
        echo '</div>';

        // Close Coloumn Tag
        echo '</div>';

        // Close Coloumn Wrapper Tag
        echo '</div>';

        // Close Wrapper Tag
        if (($elementCounter + 1) % $countOfColoumns == 0)
        {
            echo '<div class="clearfix"></div>';
            echo '</div>';
        }

        // Count Elements
        $elementCounter++;

    }
?>

</div>
<?php
    // Truncate Long Info Text
    if ($this->truncateLongInfo)
    {
?>
<script type="text/javascript">
    $('.thm_groups_profile_container_profile_read_more').click(
        function() {
            $(this).hide();
            $(this).next().fadeIn("slow");
        }
    );
</script>
<?php
    }
