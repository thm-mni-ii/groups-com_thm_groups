<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelQp_Settings extends JModelAdmin
{

    /**
     * Method to get the form
     *
     * @param   Array    $data      Data         (default: Array)
     * @param   Boolean  $loadData  Load data  (default: true)
     *
     * @return  mixed  JForm object on success, False on error.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        $option = $this->get('option');
        $name = $this->get('name');
        $form = $this->loadForm("$option.$name", $name, array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {

        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery();

        $query
            ->select('qp_enabled, qp_root_category')
            ->from('#__thm_groups_quickpages_settings');
        $dbo->setQuery($query);

        $item = $dbo->loadObject();

        return $item;
    }

    public function save($data)
    {
        $qp_enabled = $data['qp_enabled'];
        $qp_root_category = $data['qp_root_category'];

        return($this->setNewComponentParams($qp_enabled, $qp_root_category));
    }

    function setNewComponentParams($qp_enabled, $qp_root_category)
    {

        // Get a new database query instance
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Build the query
        $query->update('#__thm_groups_quickpages_settings AS a');
        $query->set('a.qp_enabled = ' . $db->quote((string)$qp_enabled));
        $query->set('a.qp_root_category = ' . $db->quote((string)$qp_root_category));

        // Execute the query
        $db->setQuery($query);
        $success = $db->execute();

        if($success)
        {
            return true;
        }

        return false;
    }
}