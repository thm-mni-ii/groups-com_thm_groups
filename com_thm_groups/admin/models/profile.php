<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        profile model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
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
class THM_GroupsModelProfile extends JModelLegacy
{
    /**
     * saves the dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $db = JFactory::getDbo();
        $db->transactionStart();
        $result = false;
        $data = JFactory::getApplication()->input->post->get('jform', array(), 'array');


        $attributeList = JFactory::getApplication()->input->post->get('attributeList', '', 'string');
        $profilID = intval($data['id']);
        $attributeJSON = json_decode($attributeList);
        if($profilID == 0)
        {
            $data['order'] = $this->getLastPosition()+1;
        }
        $profile = JTable::getInstance('Profile', 'Table');

        $success = $profile->save($data);

        if (!$success)
        {
            $db->transactionRollback();
            $result = false;
        }
        else
        {

            if (isset($attributeJSON))
            {
                $db->transactionCommit();

                $profilID = ($profilID == 0)? $profile->id: $profilID;
                $putquery = $db->getQuery(true);

                    $deletequery = $db->getQuery(true);
                    $deletequery->delete('#__thm_groups_profile_attribute')
                                ->where('profileID =' . $profilID);
                    $columnTable = array('profileID', 'attributeID', 'order', 'params');
                    $db->setQuery($deletequery);
                    $success = $db->execute();

                $putquery->insert('#__thm_groups_profile_attribute');
                $putquery->columns($db->quoteName($columnTable));
                  foreach ($attributeJSON as $index => $value )
                  {
                        $params = $db->quote(json_encode($value->params));
                      $columsValue = array($profilID, intval($index), intval($value->order), $params);
                      $putquery->values(implode(',', $columsValue));
                  }

                $db->setQuery($putquery);
                $success = $db->execute();
                if (!$success)
                {
                    return false;
                }
            }
            return $profile->id;
        }

    }

    /**
     * Delete item
     *
     * @return mixed
     */
    public function delete()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $conditions = array(
            $db->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
        );

        $query->delete($db->quoteName('#__thm_groups_profile'));
        $query->where($conditions);

        $db->setQuery($query);

        return $result = $db->execute();
    }
    /**
     * get Max  Position
     *
     * @return Integer
     */
    public function getLastPosition()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(" MAX(`order`)  as 'order'")
            ->from('#__thm_groups_profile');

        $db->setQuery($query);
        $lastPosition = $db->loadObject();

        return $lastPosition->order;
    }

    /**
     * get all Attribute Of a Profile
     *
     * @param  Int  $profileID  a profile ID
     *
     * @return mixed
     */
    public function getProfileAttributes($profileID)
    {

    }
}