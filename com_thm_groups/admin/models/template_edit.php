<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelTemplate_Edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/edit.php';
require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_user_data.php";
/** @noinspection MissingSinceTagDocInspection */


/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelTemplate_Edit extends THM_GroupsModelEdit
{
    /**
     * Method to get a table object, load it if necessary. Can't be generalized because of irregular english plural
     * spelling. :(
     *
     * @param   string $name    The table name. Optional.
     * @param   string $prefix  The class prefix. Optional.
     * @param   array  $options Configuration array for model. Optional.
     *
     * @return  JTable object
     */
    public function getTable($name = 'Template', $prefix = 'Table', $options = array())
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
     * @param   int $profilID The ID of a profile
     *
     * @return  mixed array on success, false otherwise
     */
    public function getNoSelectAttribute($profilID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        if ($profilID == 0)
        {
            $query->select("A.id,A.name, A.description")
                ->from("#__thm_groups_attribute as A ");
        }
        else
        {
            $query->select("A.id,A.name, A.description")
                ->from("#__thm_groups_attribute as A ")
                ->where(" A.id not in (select attributeID from #__thm_groups_profile_attribute as N where profileID =" . $profilID
                    . " order by N.order)");
        }

        $dbo->setQuery($query);

        try
        {
            return $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }
    }

    // TODO REFACTOR

    /**
     * Transform a List of Attribute with Database format in
     * Json format
     *
     * @param   int $profilID The ID of a profile
     *
     * @return  mixed array on success, false otherwise
     */
    public function getAllAttribute($profilID)
    {
        $dbo = JFactory::getDBO();
        $dbo->setQuery('SET group_concat_max_len = 1000000000;');
        $dbo->execute();
        $jsonquery = $dbo->getQuery(true);
        $query     = $dbo->getQuery(true);
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
        $dbo->setQuery($jsonquery);

        try
        {
            return $dbo->loadObjectList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }
    }
}
