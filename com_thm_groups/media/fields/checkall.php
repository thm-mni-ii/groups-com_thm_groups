<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        JFormFieldFields
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.form.formfield');

/**
 * Class loads a grid check all box
 *
 * @category    Joomla.Component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
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
