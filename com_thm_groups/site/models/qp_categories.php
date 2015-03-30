<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelQp_Categories
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.categories');
jimport('thm_groups.data.lib_thm_groups_quickpages');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THMGroupsModelQp_Categories extends JModelForm
{

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return mixed A JForm object on success, false on failure
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_thm_groups.qp_categories', 'qp_categories', array());

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Creates a new category to a user quickpages root category
     *
     * @return bool
     * @throws Exception
     */
    public function save()
    {
        $input = JFactory::getApplication()->input;
        $uid = JFactory::getUser()->id;
        $catTitle = $input->get('qp_name', '', 'STRING');

        THMLibThmQuickpages::createQuickpageSubcategoryForProfile($uid, $catTitle);

        // TODO check if a category was successfully added
        return true;
    }
}