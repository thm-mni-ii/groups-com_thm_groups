<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerMembers
 * @description THMGroupsControllerMembers file from com_thm_groups
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Controller of the members.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsControllerMembers extends JControllerForm
{
    /**
     * constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The function, which returns input parameters. And also this function makes checkboxes.
     *
     * @return	 array    $temp 	 contains user information
     */
    public function getUsersOfGroup()
    {
        $temp = '';
        $id = JRequest::getVar('uid');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(" a.userid, a.value AS vorname, b.value AS nachname");
        $query->from("#__thm_groups_text AS a");
        $query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
        $query->innerJoin("#__thm_groups_groups_map AS c ON (c.uid = a.userid and gid = " . $id . ")");
        $query->where("a.publish = 1");
        $query->where("a.structid = 1");
        $query->where("b.structid = 2");
        $query->group("a.userid");
        $query->order("b.value");
        $db->setQuery($query);
        $list = $db->loadObjectList();

        foreach ($list as $user)
        {
            $temp .= "<option value ='" . $user->userid . "'>" . $user->nachname . " " . $user->vorname;
            $temp .= "</option>";

        }
        echo $temp;
    }

    /**
     * Returns a list with matches
     *
     * @return	 array    $temp 	 contains user information
     */
    public function getSearchAnswer()
    {
        $temp = '';

        $query_string = JRequest::getVar('query');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $array = array();

        $query->select("a.userid, a.value AS vorname, b.value AS nachname");
        $query->from("#__thm_groups_text AS a");
        $query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
        $query->where("a.publish = 1");
        $query->where("a.structid = 1");
        $query->where("b.structid = 2");
        $query->where("(a.value LIKE '" . $query_string . "%' OR a.value LIKE '%" . $query_string . "' OR b.value LIKE '%" . $query_string
                . "' OR b.value LIKE '" . $query_string . "%')");
        $query->group("a.userid");
        $query->order("b.value LIMIT 10");

        $db->setQuery($query);
        $request = $db->loadObjectList();

        if (count($request) == 0)
        {
            $temp .= "No results";
        }
        else
        {

            foreach ($request as $user)
            {
                $temp .= "<div class='advice_variant' title='" . $user->userid . "'>" . $user->vorname . " " . $user->nachname;
                $temp .= "</div>";
            }
        }
        echo $temp;
    }

    /**
     * Parse the Attribut
     *
     * @return  Array  $result
     */
    public function parseAttribut()
    {

        $attribut = JRequest::getVar('placeholder');
        $modus_temp = explode(':', $attribut);
        $modus = $modus_temp[1];
        if (strcmp($modus, 'list') != 0)
        {

            list($keys, $mode, $id, $link, $param, $struct, $userlist) = explode(':', $attribut);
            $result = array();

            // Keys Bearbeitung
            $temp = explode('{', $keys);
            $result['keys'] = $temp[1];


            // Mode Bearbeitung
            $result['mode'] = $mode;

            // Id Bearbeitung
            $result['id'] = $id;

            // Links Bearbeitung
            $temp = explode('(', $link);
            $temp = explode(')', $temp[1]);
            $tempX = explode(',', $temp[0]);

            // 0,1,2

            $result['links'] = $tempX;


            // Params Bearbeitung
            $result['param'] = self::paramsParse($param, $mode);


            // Structure bearbeitung
            $temp = explode('(', $struct);
            $temp = explode(')', $temp[1]);
            $tempX = explode(',', $temp[0]);
            $result['struct'] = $tempX;

            // Structure bearbeitung
            $temp = explode('(', $userlist);
            $temp = explode(')', $temp[1]);
            $tempX = explode(',', $temp[0]);
            $result['userlist'] = $tempX;

        }
        if ( strcmp($modus, 'list') == 0)
        {
            // {Schlüsselwort:list:gid:showlinks(0,1):viewall:ColumnNumber:ordering(1,2,3):showstructure()}

            list($keys, $modus, $gid, $showlinks, $showall, $column, $ordering, $showStructure) = explode(':', $attribut);
            $temp = explode('{', $keys);
            $result['keys'] = $temp[1];

            $result['mode'] = $modus;

            $result['gid'] = $gid;

            // Structure bearbeitung
            $temp = explode('(', $showlinks);
            $temp = explode(')', $temp[1]);
            $tempX = explode(',', $temp[0]);

            $result['showLinks'] = $tempX;

            $result['showAll']  = intval($showall);

            $result['columnCount'] = $column;

            $temp1 = explode('(', $ordering);
            $temp1 = explode(')', $temp1[1]);
            $result['orderingAttributes'] = $temp1[0];

            $temp = explode('}', $showStructure);
            $temp1 = explode('(', $temp[0]);
            $tempX = explode(')', $temp1[1]);

            $temp = explode(',', $tempX[0]);

            $result['showstructure'] = $temp;
            $result['linkTarget'] = 'profile';


        }

        echo json_encode($result);
    }

    /**
     * Parse die Parameter
     *
     * @param   Array    $parameter  contain many Parameter of  view
     * @param   Integer  $mode       contain the type of the view
     *
     * @return  Array  with Attributs
     */
    public function paramsParse($parameter, $mode)
    {
        // Person:{Schlüsselwort:person:uid:links(name,vorname):params(Max-Breite,Position,Rahmen,float):struct(...):userlist(none)}

        // Groups Advanced:{Schlüsselwort:advanced:gid:links(name,vorname):params(Max-breite,ColumnNumber,Position,Rahmen,float):struct(...):userlist(...)}
        $result = array();
        $temp = explode('(', $parameter);
        $temp = explode(')', $temp[1]);
        $tempX = explode(',', $temp[0]);
        $result['param'] = $tempX;
        if (strcmp($mode, 'person') == 0)
        {
            $result['param'][0] = $result['param'][0];
            $result['param'][1] = $result['param'][1];
            $result['param'][2] = $result['param'][2];
            $result['param'][3] = $result['param'][3];
        }
        if (strcmp($mode, 'advanced') == 0)
        {
            $result['param'][0] = $result['param'][0];
            $result['param'][1] = $result['param'][1];
            $result['param'][2] = $result['param'][2];
            $result['param'][3] = $result['param'][3];
            $result['param'][4] = $result['param'][4];
        }
        return $result['param'];

    }
    /**
     * search user Information
     *
     * @return void
     */
    public function getUserInfoById()
    {
        if (JRequest::getVar('id') != null)
        {
            $id = JRequest::getVar('id');
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $array = array();

            $query->select("a.userid, a.value AS vorname, b.value AS nachname");
            $query->from("#__thm_groups_text AS a");
            $query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
            $query->where("a.publish = 1");
            $query->where("a.structid = 1");
            $query->where("b.structid = 2");
            $query->where("a.userid = " . $id);
            $query->group("a.userid");
            $query->order("b.value LIMIT 10");

            $db->setQuery($query);
            $request = $db->loadObjectList();

            echo json_encode($request, JSON_FORCE_OBJECT);
        }
    }
}
