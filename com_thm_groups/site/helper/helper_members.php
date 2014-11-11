<?php
/**
 * HelperClass class
 *
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelMembers
 * @description THMGroupsModelMembers file from com_thm_groups
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die('Restricted access');

/**
 * THMGroupsModelMembers class for component com_thm_groups
 *
 * @category  Joomla.Library
 * @package   lib_thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.0
 */
class THMGroupsModelMembers
{
    /**
     * The function, which returns a structure, that will be shown.
     *
     * @return	array	 $strucitems contains sturcture with elements like Name, Lastname and so on
     */
    public  function getStrucktur()
    {
        $strucitems = array();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

// 		$temp = 'SELECT a.id, a.field FROM `#__thm_groups_structure` as a Order by a.order';

        $query->select("a.id,a.field");
        $query->from("#__thm_groups_structure as a");
        $query->order("a.order");
        $db->setQuery($query);
        $data = $db->loadObjectList();

        foreach ($data as $structur)
        {
            $element = new stdClass;
            $element->id = $structur->id;
            $element->value = $structur->field;
            array_push($strucitems, $element);
        }
        return $strucitems;
    }

    /**
     * The function, which returns input parameters. And also this function makes checkboxes.
     *
     * @param   int  $count  1-person,2-group,3-list
     *
     * @return	 array    $db 	 contains user information
     */
    public function getInputParams($count)
    {
        switch ($count)
        {
            case 1: $columnA = "'cola'";
                    $columnB = "'colb'";
                    $columnC = "'colc'";
                    break;
            case 2: $columnA = "'cold'";
                    $columnB = "'cole'";
                    $columnC = "'colf'";
                    break;
        }
        $strucitems = self::getStrucktur();
        $result = '<table width= "50%" align="center"><tr>' .
        '<th description = "Displays attributes">' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ATTRIBUTE') . '</th>' .
        '<th description = "Displays a label of an attribute ">' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SHOW') . '</th>' .
        '<th description = "Displays a value of a label">' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_NAME') . '</th>' .
        '<th description = "Line break"> ' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_WRAP') . '</th>' .
        '<tr></tr>' .
        '<tr>' .
        '<td></td>' .

        /*
        ' <td><input type="checkbox" name="checkAll1" id="checkAll1" onclick="jqCheckAll2( this.id,'. $columnA .' )"/></td>'.
        ' <td><input type="checkbox" name="checkAll2" id="checkAll2" onclick="jqCheckAll2( this.id,'. $columnB .' )"/></td>'.
        ' <td><input type="checkbox" name="checkAll3" id="checkAll3" onclick="jqCheckAll2( this.id,'. $columnC .' )"/></td>'.
        */

        '</tr>';
        $check = "checkbox";
        foreach ($strucitems as $element)
        {
            /*
             *  id is a number of a structure, see a table thm_groups_structure
             */
            $id = $element->id;
            $item = $element->value;

            /*
             * Ich halte es für Blödsinn, aber nix anderes habe nicht ausgedacht
             * Id of checkboxes für person und group unterscheiden sich, weil checkboxes mit gleichen ID verboten sind.
             * Person
             * Show Name Wrap
             * 100	110	 111
             *
             * Group
             * Show 	Name 	Wrap
             * 10000	11000	11100
             *
             * Bei dem Checkbox Value lesen, werden die Werte von Group durch 10000 geteilt
             *
             * Das ist ein "Kludge"
             *
             */
            switch ($count)
            {
                case 1:
                    $id = $id * 100;
                    $idOfNameCheckbox = $id + 10;
                    $idOfWrapCheckbox = $id + 1;
                    break;
                case 2:
                    $id = $id * 100000;
                    $idOfNameCheckbox = $id + 10000;
                    $idOfWrapCheckbox = $id + 1000;
                    break;
            }

            $output
                = "<tr>

            <!-- item is a list of attributes -->

            <td>" . $item . "</td>

            <!-- checkboxes for Attributes -->

            <td><input  type=" . $check . " id=" . $id . " name=" . $columnA . " value=" . $id
            . " onclick= 'onname(" . $count . "," . $id . ")' /></td>" .

            "<td><input type=" . $check . " id=" . $idOfNameCheckbox . " name= " . $columnB
            . " disabled=true onclick='incrementOnTheShow(" . $count . "," . $id . ")' value=" . $idOfNameCheckbox . " /></td>" .

            "<td><input type=" . $check . " id=" . $idOfWrapCheckbox . " name= " . $columnC
            . " disabled=true onclick='incrementOnTheWrap(" . $count . "," . $id . ")' value=" . $idOfWrapCheckbox . " /></td>

            </tr>";

            $result .= $output;

        }
        $result .= '</table><br>';
        return $result;
    }


    /**
     * Function, which returns input parameters
     *
     * @return	array	 $db contains user information
     */
    public function getInput()
    {

        // SQL-Request which returns all staff
        $selected = $this->value;
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select(" a.userid, a.value AS vorname, b.value AS nachname");
        $query->from("#__thm_groups_text AS a");
        $query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
        $query->where("a.publish = 1");
        $query->where("a.structid = 1");
        $query->where("b.structid = 2");
        $query->group("a.userid");
        $query->order("b.value");
        $db->setQuery($query);
        $list = $db->loadObjectList();

        $html = '<select name="' . $this->name . '" id="sel" size="1" id="paramsdefault_user" class="styled">' . "<option value=''>"
                . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOICE') . "</option>";
        foreach ($list as $user)
        {
            $sel = '';
            if ($user->userid == $selected)
            {
                $sel = "selected";
            }

            $html .= "<option value=" . $user->userid . " $sel>" . $user->nachname . " " . $user->vorname . " </option>";
        }

            $html .= '</select>';
            return $html;
    }

    /**
     * Function, which returns input parameters
     *
     * @return	array	 $db contains user information
     */
    public function getKeyword()
    {

                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('params');
                $query->from('#__extensions');
                $query->where('element = \'plg_thm_groups_content_members\'');
                $db->setQuery($query);
                $data = $db -> loadObjectList();
                $parameters = $data[0]->params;
                $dec = json_decode($parameters, true);
                $keyword = $dec['Keywords'];

                echo '<input type="hidden" id="keyword" name="keyword" value="' . $keyword . '">';
    }

    /**
     * Returns a list of MNI groups
     *
     * @param   int  $count  1-person,2-group,3-list
     *
     * @return html
     */
    public function getListOfGroups($count)
    {
        $name = "";
        switch ($count)
        {
            case 2:
                $name = "groups";
                break;
            case 3:
                $name = "groups_list";
                break;
        }

        // Warning? value is not defined

        $selected = $this->value;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id,name');
        $query->from('#__thm_groups_groups');
        $db->setQuery($query);
        $list = $db->loadObjectList();

        $html = '<select name="' . $name . '" id="' . $name . '" size="1" id="paramsdefault_user" class="styled">'
                . "<option value=''>" . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_GROUPS_LIST') . "</option>";
        foreach ($list as $group)
        {
            $sel = '';
            if ($group->id == $selected)
            {
                $sel = "selected";
            }

            $html .= "<option value=" . $group->id . " $sel>" . $group->name . " </option>";
        }

        $html .= '</select>';
        return $html;
    }

    /**
     * Method to get select options
     *
     * @return select options
     */
    public function getGroupSelectOptions()
    {

        $groups = $this->getGroupsHirarchy();

        // $jgroups = $this->getJoomlaGroups(); Alte Methode, kann gelöscht werden...
        $jgroups = $this->getUsergroups(true);

        $injoomla = false;
        $wasinjoomla = false;
        $selectOptions = array();

        foreach ($groups as $group)
        {
            $injoomla = $group->injoomla == 1 ? true : false;
            if ($injoomla != $wasinjoomla)
            {
                $selectOptions[] = JHTML::_('select.option', -1, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - -', 'value', 'text', true);
            }

            $tempgroup = $group;
            $hirarchy = "";
            while ($tempgroup->parent_id != 0)
            {
                $hirarchy .= "- ";
                foreach ($jgroups as $actualgroup)
                {
                    if ($tempgroup->parent_id == $actualgroup->id)
                    {
                        $tempgroup = $actualgroup;
                    }
                }
            }
            foreach ($jgroups as $jgroup)
            {
                if ($group->id == $jgroup->id)
                {
                    $selectOptions[] = JHTML::_('select.option', $group->id, $hirarchy . $group->name);
                }
            }

            $wasinjoomla = $injoomla;
        }
        return $selectOptions;
    }

    /**
     * Gets list of all groups.
     *
     * @access  public
     * @return	bool|array  "false" on error|indexed rows with associative colums.
     */
    public function getGroupsHirarchy()
    {
        $db = JFactory::getDBO();

        // Create SQL query string

        /*
            $queryold = "SELECT thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
        $queryold .= "FROM #__usergroups AS joo ";
        $queryold .= "RIGHT JOIN (";
                $queryold .= "  SELECT * ";
                $queryold .= "  FROM #__thm_groups_groups ";
                $queryold .= "  WHERE injoomla = 0 ";
                $queryold .= "  ORDER BY name";
                $queryold .= ") AS thm ";
        $queryold .= "ON joo.id = thm.id ";
        $queryold .= "UNION ";
        $queryold .= "SELECT joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
        $queryold .= "FROM #__usergroups AS joo ";
        $queryold .= "LEFT JOIN (";
                $queryold .= "  SELECT * ";
                $queryold .= "  FROM #__thm_groups_groups ";
                $queryold .= ") AS thm ";
        $queryold .= "ON joo.id = thm.id ";
        $queryold .= "ORDER BY lft";
        */

        $nestedQuery = $db->getQuery(true);
        $nestedQuery->select('*');
        $nestedQuery->from("#__thm_groups_groups");
        $nestedQuery->where("injoomla = 0");
        $nestedQuery->order("name");

        $nestedQuery1 = $db->getQuery(true);
        $nestedQuery1->select('*');
        $nestedQuery1->from("#__thm_groups_groups");

        $nestedQuery2 = $db->getQuery(true);
        $nestedQuery2->select('joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla');
        $nestedQuery2->from("#__usergroups AS joo");
        $nestedQuery2->leftJoin("($nestedQuery1) AS thm ON joo.id = thm.id");
        $nestedQuery2->order("lft");

        $query = $db->getQuery(true);
        $query->select('thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla');
        $query->from("#__usergroups AS joo");
        $query->rightJoin("($nestedQuery) AS thm ON joo.id = thm.id UNION $nestedQuery2");

        $db->setQuery($query);
        $db->query();
        return $db->loadObjectList();
    }

    /**
     * Returns a UL list of user groups with check boxes
     *
     * @param   boolean  $checkSuperAdmin  If false only super admins can add to super admin groups
     *
     * @return  bool|array  "false" on error|indexed rows with associative colums.
     *
     * @since   11.1
     */
    public function getUsergroups($checkSuperAdmin = false)
    {
        static $count;

        $count++;

        $isSuperAdmin = JFactory::getUser()->authorise('core.admin');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.*, COUNT(DISTINCT b.id) AS level');
        $query->from($db->quoteName('#__usergroups') . ' AS a');
        $query->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
        $query->group('a.id, a.title, a.lft, a.rgt, a.parent_id');
        $query->order('a.lft ASC');
        $db->setQuery($query);
        $groups = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum())
        {
            JError::raiseNotice(500, $db->getErrorMsg());
            return null;
        }

        $res = array();

        for ($i = 0, $n = count($groups); $i < $n; $i++)
        {
        $item = &$groups[$i];

        // If checkSuperAdmin is true, only add item if the user is superadmin or the group is not super admin
        if ((!$checkSuperAdmin) || $isSuperAdmin || (!JAccess::checkGroup($item->id, 'core.admin')))
        {
                $res[] = $item;
        }
        }
        return $res;
    }
}
