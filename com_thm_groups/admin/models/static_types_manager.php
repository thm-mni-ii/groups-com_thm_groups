<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelStatic_Types_Manager
 * @description THMGroupsModelStatic_Types_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelStatic_Types_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelStatic_Types_Manager extends JModelList
{

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {

        // JLog Test
        $options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
        $options['text_file'] = 'com_thm_groups.log.php';
        JLog::addLogger($options, JLog::ALL, array('com_thm_groups.log'));

        JLog::add('Test it motherfucker!', JLog::INFO, 'com_thm_groups.log');
        JLog::add(
        // Entry
            'Error while doing something',
            // Priority (EMERGENCY|ALERT|CRITICAL|ERROR|WARNING|NOTICE|INFO|DEBUG|)
            JLog::ERROR,
            // Category
            'com_thm_groups.log'
        );

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('id, name')
            ->from('#__thm_groups_static_type');
        return $query;

    }
}
