<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * Class loads an ordering button
 */
class JFormFieldOrderingButton extends JFormField
{
    protected $type = 'OrderingButton';

    /**
     * Makes an ordering button
     *
     * @return  string  a HTML checkbox
     */
    public function getInput()
    {
        return JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', 'asc', '', null, 'asc',
            'JGRID_HEADING_ORDERING');
    }
}
