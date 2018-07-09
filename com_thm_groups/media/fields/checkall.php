<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.form.formfield');

/**
 * Class loads a grid check all box
 */
class JFormFieldCheckAll extends JFormField
{
    protected $type = 'CheckAll';

    /**
     * Makes a checkbox
     *
     * @return  string  a HTML checkbox
     */
    public function getInput()
    {
        return JHtml::_('grid.checkall');
    }
}
