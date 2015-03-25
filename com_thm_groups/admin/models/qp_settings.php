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

    public function save($data)
    {
        $qp_status = $data['qp_status'];
        $qp_root_category = $data['qp_root_category'];

        return($this->setNewComponentParams($qp_status, $qp_root_category));
    }

    function setNewComponentParams($qp_status, $qp_root_category)
    {

        $this->cleanCache('_system');

        $componentName = 'com_thm_groups';

        // To access the extensions table we need the id of the component
        $componentId = JComponentHelper::getComponent('com_thm_groups')->id;
        assert($componentId != 0); // make sure that no error will cause the creation of a new entry in the extenions table

        // set the new value using set()
        $params = JComponentHelper::getParams('com_thm_groups');
        $params->set('qp_status', $qp_status);
        $params->set('qp_root_category', $qp_root_category);

        // Get a new database query instance
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Build the query
        $query->update('#__extensions AS a');
        $query->set('a.params = ' . $db->quote((string)$params));
        $query->where("a.element = '$componentName'");

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