<?php
/**
 * HelperClass class
 *
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelWAI
 * @description THMGroupsModelWAI file from com_thm_groups
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die();

/**
 * THMGroupsModelWai class for component com_thm_groups
 *
 * @category  Joomla.Library
 * @package   lib_thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.0
 */
class THMGroupsModelWai
{
    /**
     * Returns all users
     *
     * @return string
     */
    public function getInput()
    {
        // SQL-Request which returns all staff

        // Comment $selected = $this->value;

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

        $html = '<select name="user_select" id="sel" size="1" id="paramsdefault_user" style="display:block">' . "<option value=''>"
                . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOICE') . "</option>";
        foreach ($list as $user)
        {
            /*
            $sel = '';

            if ($user->userid == $selected)
            {
                $sel = "selected";
            }
            */

            $html .= "<option value=" . $user->userid . ">" . $user->nachname . " " . $user->vorname . " </option>";
        }

        $html .= '</select>';
        return $html;
    }

    /**
     * Prints keyword, that will be replaced
     *
     * @return void
     */
    public function getKeyword()
    {
        $db = JFactory::getDBO();

        // 'SELECT params  FROM `#__extensions` WHERE element = \'plg_thm_groups_content_wai\'';

        $query = $db->getQuery(true);

        $query->select("params");
        $query->from("#__extensions");
        $query->where("element = 'plg_thm_groups_content_wai'");

        $db->setQuery($query);
        $data = $db->loadObjectList();

        $parameters = $data[0]->params;

        $dec = json_decode($parameters, true);

        $keyword = $dec['Keyword'];

        echo '<input type="hidden" id="keyword" name="keyword" value="' . $keyword . '">';

    }
}
