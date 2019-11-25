<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die; ?>
    <script type="text/javascript">
        const rootURI = '<?php echo JUri::base(); ?>';
    </script>
<?php
require_once JPATH_ROOT . '/media/com_thm_groups/layouts/list.php';
THM_GroupsLayoutList::render($this);
