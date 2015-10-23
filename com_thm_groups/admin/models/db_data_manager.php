<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelRole
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/install.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/update.php';

/**
 *
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelDB_Data_Manager extends JModelLegacy
{

    public function execute()
    {
        $jinput = JFactory::getApplication()->input;

        // array with action command
        $action = $jinput->get('migration_action', array(), 'array');

        switch ($action[0])
        {
            case 'install_example_data':
                self::installExampleData();
                break;
            case 'copy_data_from_joomla25_thm_groups_tables':
                self::restoreData();
                break;
            default:
                return false;
        }

        return true;
    }

    private static function restoreData()
    {
        if (self::copyData()
            && THM_Groups_Update_Script::update())
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('DB_Data_Manager -> Restore Data', 'error');
        return false;
    }

    /**
     * install_example_data
     */
    private static function installExampleData()
    {
        if (self::createExampleDynamicTypes()
            && self::createExampleAttributes()
            && self::createExampleRoles()
            && self::createExampleProfiles()
            && self::createExampleProfileAttributes()
            && THM_Groups_Install_Script::install())
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('DB_Data_Manager -> Install Example Data', 'error');
        return false;
    }

    private static function createExampleDynamicTypes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        /* INSERT INTO '#__thm_groups_dynamic_type' ('id', 'static_typeID', 'name', 'regex', 'options') VALUES
           (1, 1, 'Name', '^[0-9a-zA-ZäöüÄÖÜ]+$', '{ "length" : "40" }'),
           (2, 1, 'Email', '^([0-9a-zA-Z\\\\.]+)@(([\\\\w]|\\\\.\\\\w)+)\\\\.(\\\\w+)$', '{ "length" : "40" }'),
           (3, 3, 'Website', '(http|ftp|https:\\\\/\\\\/){0,1}[\\\\w\\\\-_]+(\\\\.[\\\\w\\\\-_]+)+([\\\\w\\\\-\\\\.,@?^=%&amp;:/~\\\\+#]*[\\\\w\\\\-\\\\@?^=%&amp;/~\\\\+#])?', '{}'),
           (4, 6, 'Table', '', '{ "columns" : "Spalte1;Spalte2;", "required" : "true" }');*/

        $columns = array('id', 'static_typeID', 'name', 'options');
        $values[] = array(1, 1, $db->q('Name'), $db->q('{ "length" : "40" }'));
        $values[] = array(2, 1, $db->q('Email'), $db->q('{ "length" : "40" }'));
        $values[] = array(3, 3, $db->q('Website'), $db->q('{}'));
        $values[] = array(4, 6, $db->q('Table'), $db->q('{"columns" : "Spalte1;Spalte2;", "required" : "true" }'));
        $query
            ->insert('#__thm_groups_dynamic_type')
            ->columns($db->qn($columns));

        foreach($values as $value)
        {
            $query
                ->values(implode(',', $value));
        }

        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage('createExampleDynamicTypes ' . $e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    private static function createExampleAttributes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        /* INSERT INTO '#__thm_groups_attribute' ('id', 'name', 'dynamic_typeID', 'options') VALUES
            (1, 'Vorname', 1, '{ "length" : "40", "required" : "true" }'),
            (2, 'Nachname', 1, '{ "length" : "40", "required" : "true" }'),
            (3, 'Username', 1, '{ "length" : "40", "required" : "true" }'),
            (4, 'Email', 2, '{ "length" : "40", "required" : "true" }'),
            (5, 'Titel', 1, '{ "length" : "40", "required" : "true" }'),
            (6, 'Posttitel', 1, '{ "length" : "40", "required" : "true" }'),
            (7, 'Website', 3, '{"required" : "true"}'),
            (8, 'Curriculum', 4, '{ "columns" : "Spalte1;Spalte2;", "required" : "true" }');*/

        $columns = array('id', 'name', 'dynamic_typeID', 'published', 'ordering', 'options');
        $values[] = array(1, $db->q('Vorname'), 1, 1, 1, $db->q('{ "length" : "40", "required" : "true" }'));
        $values[] = array(2, $db->q('Nachname'), 1, 1, 2, $db->q('{ "length" : "40", "required" : "true" }'));
        $values[] = array(3, $db->q('Username'), 1, 1, 3, $db->q('{ "length" : "40", "required" : "true" }'));
        $values[] = array(4, $db->q('Email'), 2, 1, 4, $db->q('{ "length" : "40", "required" : "true" }'));
        $values[] = array(5, $db->q('Titel'), 1, 1, 5, $db->q('{ "length" : "40", "required" : "true" }'));
        $values[] = array(6, $db->q('Posttitel'), 1, 1, 6, $db->q('{ "length" : "40", "required" : "true" }'));
        $values[] = array(7, $db->q('Website'), 3, 1, 7, $db->q('{"required" : "true"}'));
        $values[] = array(8, $db->q('Curriculum'), 4, 1, 8, $db->q('{ "columns" : "Spalte1;Spalte2;", "required" : "true" }'));
        $query
            ->insert('#__thm_groups_attribute')
            ->columns($db->qn($columns));

        foreach($values as $value)
        {
            $query
                ->values(implode(',', $value));
        }

        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage('createExampleAttributes ' . $e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    private static function createExampleRoles()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        /* INSERT INTO '#__thm_groups_roles' ('id', 'name') VALUES
            (1, 'Mitglied'),
            (2, 'Moderator'),
            (3, 'Role1'),
            (4, 'Role2'),
            (5, 'Role3');*/

        $columns = array('id', 'name');
        $values[] = array(1, $db->q('Mitglied'));
        $values[] = array(2, $db->q('Moderator'));
        $values[] = array(3, $db->q('Role1'));
        $values[] = array(4, $db->q('Role2'));
        $values[] = array(5, $db->q('Role3'));
        $query
            ->insert('#__thm_groups_roles')
            ->columns($db->qn($columns));

        foreach($values as $value)
        {
            $query
                ->values(implode(',', $value));
        }

        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage('createExampleRoles ' . $e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    private static function createExampleProfiles()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        /* INSERT INTO '#__thm_groups_profile' ('id', 'name', 'order') VALUES
            (1, 'Standard', 1),
            (2, 'Mitarbeiter', 2),
            (3, 'Professor', 3),
            (4, 'Dozent', 4);*/

        $columns = array('id', 'name', 'order');
        $values[] = array(1, $db->q('Standard'), 1);
        $values[] = array(2, $db->q('Mitarbeiter'), 2);
        $values[] = array(3, $db->q('Professor'), 3);
        $values[] = array(4, $db->q('Dozent'), 4);
        $query
            ->insert('#__thm_groups_profile')
            ->columns($db->qn($columns));

        foreach($values as $value)
        {
            $query
                ->values(implode(',', $value));
        }

        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage('createExampleProfiles ' . $e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    private static function createExampleProfileAttributes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        /* INSERT INTO '#__thm_groups_profile_attribute' ('ID', 'profileID', 'attributeID', 'order', 'params') VALUES
            (1, 1, 1, 1, '{ "label" : true, "wrap" : true}'),
            (2, 1, 2, 2, '{ "label" : true, "wrap" : true}'),
            (3, 1, 3, 3, '{ "label" : true, "wrap" : true}'),
            (4, 1, 4, 4, '{ "label" : true, "wrap" : true}'),
            (5, 1, 5, 5, '{ "label" : true, "wrap" : true}'),
            (6, 1, 6, 6, '{ "label" : true, "wrap" : true}'),
            (7, 1, 7, 7, '{ "label" : true, "wrap" : true}'),
            (8, 1, 8, 8, '{ "label" : true, "wrap" : true}');*/

        $columns = array('ID', 'profileID', 'attributeID', 'order', 'params');
        $values[] = array(1, 1, 1, 1, $db->q('{ "label" : true, "wrap" : true}'));
        $values[] = array(2, 1, 2, 2, $db->q('{ "label" : true, "wrap" : true}'));
        $values[] = array(3, 1, 3, 3, $db->q('{ "label" : true, "wrap" : true}'));
        $values[] = array(4, 1, 4, 4, $db->q('{ "label" : true, "wrap" : true}'));
        $values[] = array(5, 1, 5, 5, $db->q('{ "label" : true, "wrap" : true}'));
        $values[] = array(6, 1, 6, 6, $db->q('{ "label" : true, "wrap" : true}'));
        $values[] = array(7, 1, 7, 7, $db->q('{ "label" : true, "wrap" : true}'));
        $values[] = array(8, 1, 8, 8, $db->q('{ "label" : true, "wrap" : true}'));
        $query
            ->insert('#__thm_groups_profile_attribute')
            ->columns($db->qn($columns));

        $db->setQuery($query);

        foreach($values as $value)
        {
            $query
                ->values(implode(',', $value));
        }

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage('createExampleProfileAttributes ' . $e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    /**
     * copy_data_from_joomla25_thm_groups_tables
     */
    private static function copyData()
    {
        $db = JFactory::getDbo();
        $buffer = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/sql/updates/customUpdate.sql');
        if ($buffer === false)
        {
            JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), JLog::WARNING, 'jerror');

            return false;
        }
        // Create an array of queries from the sql file
        $queries = JDatabaseDriver::splitSql($buffer);

        // Process each query in the $queries array (split out of sql file).
        foreach ($queries as $query)
        {
            $query = trim($query);

            if ($query != '' && $query{0} != '#')
            {
                $db->setQuery($query);

                if (!$db->execute())
                {
                    JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');

                    return false;
                }
                else
                {
                    //$queryString = (string) $query;
                    //$queryString = str_replace(array("\r", "\n"), array('', ' '), substr($queryString, 0, 80));
                    //JLog::add(JText::sprintf('JLIB_INSTALLER_UPDATE_LOG_QUERY', $file, $queryString), JLog::INFO, 'Update');
                }
            }
        }

        return true;
    }
}