<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        attribute model
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
class THMGroupsModelAttribute extends JModelLegacy
{
    /**
     * saves the dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $dbo = JFactory::getDbo();

        $app = JFactory::getApplication();
        $data = $app->input->post->get('jform', array(), 'array');

        // dynamicType is a name of select field
        $data['dynamic_typeID'] = $app->input->post->get('dynamicType');

        // Cast to int, because the type in DB is int
        $data['dynamic_typeID'] = (int) $data['dynamic_typeID'];
        $data['description'] = $dbo->escape($data['description']);

        $dbo->transactionStart();

        $dynamicType = $this->getTable();

        $success = $dynamicType->save($data);

        if (!$success)
        {
            $dbo->transactionRollback();
            return false;
        }
        else
        {
            $dbo->transactionCommit();
            return $dynamicType->id;
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

        $query->delete($db->quoteName('#__thm_groups_attribute'));
        $query->where($conditions);

        $db->setQuery($query);


        return $result = $db->execute();
    }
}