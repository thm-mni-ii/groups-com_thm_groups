<?php
/**
 * @version     v3.0.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewGroups
 * @description THMGroupsViewGroups file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

    defined('_JEXEC')or die('Restricted access');
    echo "<ul class='gs_advtable_left'>";
        foreach ($this->groups as $group)
        {
            echo "<li>";
                echo "<div name='memberwrapper' id='gs_advlist_memberwrapper' style='margin-bottom:30px;'>";
                echo "<div id='secondWrapper'>";
                if ($this->params->get('showPicture') == "yes")
                {
                    echo "<div class='gs_advlistPicture'>";
                        if (JFile::exists("components/com_thm_groups/img/portraits/$group->picture"))
                        {
                            echo JHTML :: image(
                                            "components/com_thm_groups/img/portraits/$group->picture", 'Logo',
                                             array('class' => 'mod_gs_portraitB', 'style' => 'max-height:40px;')
                                            );
                        }
                        else
                        {
                            echo JHTML :: image(
                                            "components/com_thm_groups/img/portraits/default_group.png", 'Logo',
                                             array('class' => 'mod_gs_portraitB', 'style' => 'max-height:40px;')
                                            );
                        }
                    echo "</div>";
                }
                elseif ($this->params->get('showPicture') == "yesnodef" )
                {
                    if (JFile::exists("components/com_thm_groups/img/portraits/$group->picture"))
                    {
                        echo "<div class='gs_advlistPicture'>";
                        echo JHTML :: image(
                                        "components/com_thm_groups/img/portraits/$group->picture", 'Logo',
                                         array('class' => 'mod_gs_portraitB', 'style' => 'max-height:40px;')
                                        );
                        echo "</div>";
                    }
                }
                    echo "<div id='gs_advlistTopic'>";
                        $displayInline = "style='display: inline'";
                        echo str_repeat("|&mdash;", $group->level);
                        echo "<a href="
                            . JRoute::_('index.php?option=com_thm_groups&view=groups&layout=default&Itemid=' . $this->itemid . '&gsgid=' . $group->id)
                            . ">"
                            . "<div class='gs_advlist_longinfo'" . $displayInline . ">" . $group->title . "</div> "
                            . "</a>";

                        // Daten fuer die EditForm
                        $option = JRequest :: getVar('option', 0);
                        $layout = JRequest :: getVar('layout', 'default');
                        $view = JRequest :: getVar('view', 0);

                        // Abfrage, ob angemeldeter User Moderator in der anzuzeigenden Gruppe ist
                        foreach ($this->canEdit as $group_mod)
                        {
                            if ($group_mod->gid == $group->id )
                            {
                                $canEdit = 1;
                            }
                        }
                        if ($canEdit)
                        {
                            $attribs['title'] = 'bearbeiten';
                            $path = "'index.php?option=com_thm_groups&view=editgroup&layout=default&Itemid=";
                            $gid = $group->id;
                            $iid = $this->itemid;
                            echo "<a href="
                            . JRoute :: _(
                                            $path . $iid . '&gsgid=' . $gid . '&option_back='
                                            . $option . '&view_back=' . $view . '&layout_back=' . $layout
                                        )
                            . "'> "
                            . JHTML :: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";
                        }
                    echo "</div>";
                    echo "<div class='gs_advlist_longinfo' style='clear: left;'>";
                        if ($group->longinfo != null)
                        {
                            if ($this->params->get('cutLongtext') == "yes")
                            {
                                $exploded = explode("<br />", $group->longinfo);
                                $stripped = strip_tags($exploded[0]);
                                $output = $stripped . '...';
                                echo $output;
                            }
                            else
                            {
                                echo $group->longinfo . "\n";
                            }
                        }
                    echo "</div>";
                echo "</div>";
                echo "</div>";
            echo"</li>";
        }
    echo "</ul>";
