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

    /**
     * Facade
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
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
                self::restoreData(true, 'customUpdate.sql');
                break;
            case 'copy_data_for_w_page_from_joomla25_thm_groups_tables':
                self::restoreData(false, 'customUpdateForW.sql');
                break;
            case 'fix_tables':
                self::fixTables();
                break;
            case 'convert_tables_in_new_textfields':
                self::convertTablesInTextFields();
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * We copied with update script the data from _thm_groups_table_extra and _thm_groups_table.
     * Table functionality will no more supported, because you can create, edit and delete tables
     * with help of integrated editors for text fields.
     *
     * This function needs two tables from the old structure:
     *  prefix_thm_groups_table
     *  prefix_thm_groups_table_extra
     *
     * The function converts the old self-defined structure for tables into normal
     * HTML tables, which will be saved as HTML-Code in new Attributes with the
     * static type TEXTFIELD (see also dynamic type -> TEXTFIELD)
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
    private static function convertTablesInTextFields()
    {
        $tableStructures = self::getTableStructures();
        if (self::createNewTextFieldFromTableStructure($tableStructures))
        {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('DB_Data_Manager -> convertTablesInTextFields', 'error');
        return false;
    }

    /**
     * Creates TEXTFIELD Attributes from the table types
     *
     * @param   array  $tableStructures  Array with all structures from type TABLE
     *
     * @return  bool   true in success, false otherwise
     */
    private static function createNewTextFieldFromTableStructure($tableStructures)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $values = array();
        $header = '';

        foreach ($tableStructures as $structure)
        {
            $query
                ->insert("#__thm_groups_attribute (dynamic_typeID, name, options, published)");
            // 2 as dynamicType, because by update it's a standard value for TEXTFIELD
            $value = array(2, $db->q($structure->field . 'COPY'), $db->q('{ "length" : "80", "required" : "false" }'), 1);
            $query
                ->values(implode(',', $value));
            $db->setQuery($query);
            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage('createNewTextFieldFromTableStructure ' . $e->getMessage(), 'error');
                return false;
            }
            $attrID = $db->insertid();

            // Get rows from table #__thm_groups_table
            $tableContent = self::getTableContent($structure->id);
            $header = self::getTableHeader($structure->id);
            foreach($tableContent as $table)
            {
                if (!empty($table->value) && $table->value != '[]' && $table->userid != 0)
                {
                    $newTableContent = self::convertTableContent($header, $table->value);
                    $query = $db->getQuery(true);
                    $query
                        ->insert('#__thm_groups_users_attribute (usersID, attributeID, value, published)');
                    $value = array($table->userid, $attrID, $db->q($newTableContent), $table->publish);
                    $query
                        ->values(implode(',', $value));
                    $db->setQuery($query);
                    try
                    {
                        $db->execute();
                    }
                    catch (Exception $e)
                    {
                        JFactory::getApplication()->enqueueMessage('createNewTextFieldFromTableStructure #2 ' . $e->getMessage(), 'error');
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Return header of a table by structure ID
     *
     * @param   int  $structID  structure ID
     *
     * @return  object
     */
    private static function getTableHeader($structID)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('value')
            ->from('#__thm_groups_table_extra')
            ->where("structid = $structID");
        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Converts old table structure in new one
     *
     * @param   object  $tableHeader   Object with header string semicolon separated
     *
     * @param   json    $tableContent  JSON string with table's content
     *
     * @return  string  New table representation
     */
    private static function convertTableContent($tableHeader, $tableContent)
    {
        $head = explode(';', $tableHeader->value);
        $arrValue = json_decode($tableContent);
        $newContent = '<table class="table table-striped">';
        $newContent .= '<thead>';
        $newContent .= '<tr>';

        foreach($head as $headItem)
            $newContent .= "<th>$headItem</th>";

        $newContent .= '</tr>';
        $newContent .= '</thead>';

        $newContent .= '<tbody>';
        foreach($arrValue as $row)
        {
            $newContent .= '<tr>';
            foreach ($row as $rowItem)
                $newContent .= '<td>' . $rowItem . '</td>';
            $newContent .= '</tr>';
        }
        $newContent .= '</tbody>';
        $newContent .= '</table>';
        return $newContent;
    }

    /**
     * Retrieve content of tables
     *
     * @param   int  $structID  TABLE-Structure ID
     *
     * @return ObjectList
     */
    private static function getTableContent($structID)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('userid, structid, value, publish')
            ->from('#__thm_groups_table')
            ->where("structid = $structID");

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Returns all structures of type TABLE
     *
     * @return ObjectList
     */
    private static function getTableStructures()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Get all structures of type TABLE
        $query
            ->select('id, field, type, `order`')
            ->from('#__thm_groups_structure')
            ->where('type LIKE "TABLE"');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Fixes a problem with the not copied users after migration
     * Example:
     * You migrated some Joomla instance, but the old joomla instance become new users
     * You copy this new users from the old joomla instance with J2XML to your new instance.
     * You have users only in Joomla, but not in THM Groups.
     *
     * This method uses the algorithm of sync plugin to copy all basic user attributes like first name,
     * second name, email and username. It creates also all other existing attributes, but without information.
     * It assigns also group-role mapping to users.
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
    private static function fixTables()
    {
        $idsAndGroups = self::getUserIDsAndGroups();
        if(!empty($idsAndGroups))
        {
            if(self::copyUserData($idsAndGroups))
            {
                return true;
            }
        }

        JFactory::getApplication()->enqueueMessage('DB_Data_Manager -> fixTables', 'error');
        return false;
    }

    /**
     * Retrieves user IDs and groups
     *
     * @return AssocList
     */
    private static function getUserIDsAndGroups()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id, a.name, a.username, a.email, group_concat(c.group_id) AS groups')
            ->from('#__users AS a')
            ->leftJoin('#__thm_groups_users AS b ON a.id = b.id')
            ->innerJoin('#__user_usergroup_map AS c ON a.id = c.user_id')
            ->where('b.id is NULL')
            ->group('a.id');

        $db->setQuery($query);
        return $db->loadAssocList();
    }

    /**
     * Copies users from Joomla into THM Groups using sync-plugin algorithm
     *
     * @param   array  $users  Array with users and groups
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws  Exception
     */
    private static function copyUserData($users)
    {
        $db = JFactory::getDbo();

        foreach ($users as $user)
        {
            $userid = $user['id'];
            $name = $user['name'];
            $login = $user['username'];
            $email = $user['email'];
            $groups = explode(',', $user['groups']);

            // Cut the Name
            $nameArray = explode(" ", $name);
            $lastName = end($nameArray);
            array_pop($nameArray);

            $deletefromname = array("(", ")", "Admin", "Webmaster");
            $namesplit = explode(" ", str_replace($deletefromname, '', $name));
            array_pop($namesplit);
            $firstName = implode(" ", $nameArray);

            $query = $db->getQuery(true);
            $query
                ->insert("`#__thm_groups_users` (`id`, `published`, `injoomla`, `canEdit`)")
                ->values("'" . $userid . "', '0', '1', '0'");

            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage('copyUserData #1 ' . $e->getMessage(), 'error');
                return false;
            }

            $arr_attribute = array(1, 2, 3 ,4);
            $attribute_query = $db->getQuery(true);
            $attribute_query
                ->select('id')
                ->from('#__thm_groups_attribute')
                ->where('id NOT IN (' . implode(",", $arr_attribute) . ')');
            $db->setQuery($attribute_query);

            $attributes = $db->loadObjectList();

            // Update InnoDB
            $query = $db->getQuery(true);

            $query
                ->insert("#__thm_groups_users_attribute (usersID, attributeID, value,published)")
                ->values(" $userid , 1 , '" . ucfirst($firstName) . "',1")
                ->values(" $userid , 2 , '" . ucfirst($lastName) . "',1")
                ->values(" $userid , 3 , '" . $login . "',1")
                ->values(" $userid , 4 , '" . $email . "',1");

            foreach ($attributes AS $attribute)
            {
                $query->values("$userid , $attribute->id , ' ', 0");
            }

            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage('copyUserData #2 ' . $e->getMessage(), 'error');
                return false;
            }

            self::insertUserGroups($userid, $groups);
        }

        return true;
    }

    /**
     * Method to insert the User groups
     *
     * @param   int    $user_id      User id
     *
     * @param   array  $user_groups  User groups
     *
     * @return void
     */
    public function insertUserGroups($user_id, $user_groups)
    {
        $db = JFactory::getDBO();
        $userGroups_ID_List = array();
        foreach ($user_groups as $index => $value )
        {
            $query = $db->getQuery(true);
            $query->select("ID AS id")
                ->from("#__thm_groups_usergroups_roles")
                ->where("rolesID = 1")
                ->where("usergroupsID =" . $value);
            $db->setQuery($query);
            $groups_roles_id = $db->loadObject()->id;
            if (empty($groups_roles_id))
            {
                $insertGroups_query = $db->getQuery(true);
                $insertGroups_query->insert("#__thm_groups_usergroups_roles (usergroupsID,rolesID)");
                $insertGroups_query->values("$value, 1");
                $db->setQuery($insertGroups_query);
                $db->execute();
                $groups_roles_id = $db->insertid();
            }
            $userGroups_ID_List[] = $groups_roles_id;
        }

        if ($userGroups_ID_List)
        {
            $set_User_groups = $db->getQuery(true);
            $set_User_groups->insert('#__thm_groups_users_usergroups_roles (usersID, usergroups_rolesID)');
            foreach ($userGroups_ID_List as $id => $value)
            {
                $set_User_groups->values("$user_id, $value");
            }
            $db->setQuery($set_User_groups);
            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage('insertUserGroups ' . $e->getMessage(), 'error');
            }
        }
    }

    /**
     * @param   bool    $fixCategoriesTable  Fix an old bug with categories for quickpages
     *
     * @param   string  $scriptName          SQL-Script which will be launched, e.g. "customUpdate.sql", place script in /admin/sql/updates/
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws  Exception
     */
    private static function restoreData($fixCategoriesTable = true, $scriptName)
    {
        if ($fixCategoriesTable)
        {
            if (self::fixCategoriesTable()
                && self::copyData($scriptName)
                && THM_Groups_Update_Script::update())
            {
                return true;
            }
        }
        else
        {
            if (self::copyData($scriptName)
                && THM_Groups_Update_Script::update())
            {
                return true;
            }
        }


        JFactory::getApplication()->enqueueMessage('DB_Data_Manager -> Restore Data', 'error');
        return false;
    }

    /**
     * Changes created_user_id for all quickpages categories before data's import.
     * It's an old bug by creation of categories for users
     *
     * @return  bool   true on success, false otherwise
     */
    private static function fixCategoriesTable()
    {
        $db = JFactory::getDbo();

        // Get QP main category
        $query = $db->getQuery(true);
        $query
            ->select('id')
            ->from('#__categories')
            ->where('path = "persoenliche-seiten" OR path = "quickpages"');
        $db->setQuery($query);
        $mainCat = $db->loadObject();

        // Get all qp users categories
        $query = $db->getQuery(true);
        $query
            ->select('id, path')
            ->from('#__categories')
            ->where("parent_id = $mainCat->id")
            ->where('published = 1');
        $db->setQuery($query);
        $categories = $db->loadObjectList();

        foreach ($categories as $cat)
        {
            // persoenliche-seiten/max-mustermann-90
            $temp = explode('/', $cat->path);

            // $temp[1] = max-mustermann-90
            $temp = $temp[1];
            $temp = explode('-', $temp);

            // $userID = 90
            $userID = $temp[count($temp) - 1];

            // Change create_user_id for all qp categories
            $query = $db->getQuery(true);
            $query
                ->update('#__categories')
                ->set("created_user_id = $userID")
                ->from('#__categories')
                ->where("id = $cat->id");
            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage('fixCategoriesTable ' . $e->getMessage(), 'error');
                return false;
            }
        }
        return true;
    }

    /**
     * Installs example data
     *
     * @return  bool   true on success, false otherwise
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

    /**
     * Creates example dynamic types
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
    private static function createExampleDynamicTypes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        /* INSERT INTO '#__thm_groups_dynamic_type' ('id', 'static_typeID', 'name', 'regex', 'options') VALUES
           (1, 1, 'Name', '^[0-9a-zA-Z������]+$', '{ "length" : "40" }'),
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

    /**
     * Creates example attributes
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
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

    /**
     * Creates example roles
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
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

    /**
     * Creates example profiles
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
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

    /**
     * Creates example attributes for profile
     *
     * @return  bool   true on success, false otherwise
     *
     * @throws Exception
     */
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
     * Launches the given SQL-Script
     *
     * @param   string  $scriptName  SQL-Script name
     *
     * @return  bool   true on success, false otherwise
     */
    private static function copyData($scriptName)
    {
        $db = JFactory::getDbo();
        $buffer = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/sql/updates/' . $scriptName);
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