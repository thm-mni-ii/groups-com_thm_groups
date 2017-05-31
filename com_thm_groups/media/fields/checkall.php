<?php
/**
 * @category    Joomla library
 * @package     THM_Core
 * @subpackage  lib_thm_core.site
 * @name        JFormFieldFields
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.form.formfield');

/**
 * Class loads a grid check all box
 *
 * @category    Joomla.Library
 * @package     thm_core
 * @subpackage  lib_thm_core.site
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