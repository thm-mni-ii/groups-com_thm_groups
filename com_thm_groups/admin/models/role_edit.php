<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';


/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelRole_Edit extends THM_GroupsModelEdit
{
    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }
}
