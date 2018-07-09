<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

// Create shortcut
$urls = json_decode($this->item->urls);

// Create shortcuts to some parameters.
$params = $this->item->params;
if ($urls && (!empty($urls->urla) || !empty($urls->urlb) || !empty($urls->urlc))) {
    ?>
    <div class="content-links">
        <ul>
            <?php
            $urlarray = [
                [$urls->urla, $urls->urlatext, $urls->targeta, 'a'],
                [$urls->urlb, $urls->urlbtext, $urls->targetb, 'b'],
                [$urls->urlc, $urls->urlctext, $urls->targetc, 'c']
            ];
            foreach ($urlarray as $url) {
                $link   = $url[0];
                $label  = $url[1];
                $target = $url[2];
                $id     = $url[3];

                if (!$link) {
                    continue;
                }

                // If no label is present, take the link
                $label = ($label) ? $label : $link;

                // If no target is present, use the default
                $target = $target ? $target : $params->get('target' . $id);
                ?>
                <li class="content-links-<?php echo $id; ?>">
                    <?php
                    // Compute the correct link

                    switch ($target)
                    {
                    case 1:
                        // Open in a new window
                        echo '<a href="' . htmlspecialchars($link) . '" target="_blank"  rel="nofollow">' .
                            htmlspecialchars($label) . '</a>';
                        break;

                    case 2:
                        // Open in a popup window
                        $attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=600';
                        echo "<a href=\"" . htmlspecialchars($link) . "\" onclick=\"window.open(this.href, 'targetWindow', '" . $attribs . "');"
                            . " return false;\">" .
                            htmlspecialchars($label) . '</a>';
                        break;
                    case 3:
                    // Open in a modal window
                    JHtml::_('behavior.modal', 'a.modal'); ?>
                    <a class="modal" href="<?php echo htmlspecialchars($link); ?>"
                       rel="{handler: 'iframe', size: {x:600, y:600}}">
                        <?php echo htmlspecialchars($label) . ' </a>';
                        break;

                        default:
                            // Open in parent window
                            echo '<a href="' . htmlspecialchars($link) . '" rel="nofollow">' .
                                htmlspecialchars($label) . ' </a>';
                            break;
                        }
                        ?>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php
}