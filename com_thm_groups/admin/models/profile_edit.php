<?php
/**
 * @version     v3.2.6
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelProfile_Edit
 * @description Edit Data of a Profile
 * @author      Dieudonne Timma, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('thm_core.edit.model');
jimport('thm_groups.data.lib_thm_groups_user');


/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelProfile_Edit extends THM_CoreModelEdit
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to get a table object, load it if necessary. Can't be generalized because of irregular english plural
     * spelling. :(
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  JTable object
     */
    public function getTable($name = 'Profile', $prefix = 'Table', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }


    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        $ids = $app->input->get('cid', array(), 'array');

        // Input->get because id is in url
        $id = (empty($ids)) ? $app->input->get->get('id') : $ids[0];
        return $this->getItem($id);
    }


    /**
     *  get the Attribut  for a profile
     *
     * @param   int  $profilID  The ID of a profile
     *
     * @return  Object
     */
    public function getNoSelectAttribute($profilID)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        if($profilID ==0)
        {
            $query->select("A.id,A.name, A.description")
                ->from("#__thm_groups_attribute as A ");

        }else{
        $query->select("A.id,A.name, A.description")
            ->from("#__thm_groups_attribute as A ")
             ->where(" a.id not in (select attributeID from #__thm_groups_profile_attribute as N where profileID =" . $profilID
                . " order by N.order)");

        }
        $db->setQuery($query);
        return $db->loadObjectList();
    }


    /**
     *  Transform a List of Attribute with Database format in
     * Json format
     *
     * @param   int  $profilID  The ID of a profile
     *
     * @return  Object
     */
    public function getAllAttribute($profilID)
    {
        $db = JFactory::getDBO();
        $setParam = $db->getQuery(true);
        $setParam = 'SET group_concat_max_len = 100000;';
        $db->setQuery($setParam);
        $db->execute();
        $jsonquery = $db->getQuery(true);
        $query = $db->getQuery(true);
        $query->select(" A.attributeID as attrid")
            ->select(" A.order as attrorder")
            ->select(" A.params as attrParam")
            ->select(" B.name as attrname")
            ->from(" #__thm_groups_profile_attribute as A ")
            ->leftJoin("#__thm_groups_attribute as B on B.id = A.attributeID")
            ->where("A.profileID = " . $profilID)
            ->order("A.order");

        $jsonquery->select("CONCAT('{',GROUP_CONCAT('\"',attrid,'\"' , ':{',
            '\"name\":','\"',attrname,'\"' ,
            ',\"order\":','\"',attrorder,'\"' ,
            ',\"param\":',IF(attrParam IS NULL or attrParam = '', ' ', attrParam), '',
            '}'), '}') as json ")
            ->from('(' . $query . ')as result');

        $db->setQuery($jsonquery);
        return $db->loadObjectList();
    }

}