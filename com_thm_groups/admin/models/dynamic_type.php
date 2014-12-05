<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/assets/helpers/static_type_options_helper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelDynamic_Type extends JModelLegacy
{
    /**
     * Save element of dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $options = THM_GroupsHelperOptions::getOptions();

        $dbo = JFactory::getDbo();
        $app = JFactory::getApplication();
        $data = $app->input->post->get('jform', array(), 'array');

        // Selected item
        $data['static_typeID'] = $app->input->post->get('staticType');

        // Cast to int, because the type in DB is int
        $data['static_typeID'] = (int) $data['static_typeID'];
        $data['description'] = $dbo->escape($data['description']);

        // Get Options from input
        switch ($data['static_typeID'])
        {
            case "1":
                /**
                 * $app->input->getHtml() => could use JRequest(deprecated) or ...->input->get->post(..)
                 * but chars like ": ; ..." would be lost
                 */
                $options['1'] = '{ "length" : "' . $app->input->getHtml('TEXT_length') . '" }';
                break;
            case "2":
                $options['2'] = '{ "length" : "' . $app->input->getHtml('TEXTFIELD_length') . '" }';
                break;
            case "4":
                $options['4'] = '{ "filename" : "' . $app->input->getHtml('PICTURE_name') . '", "path" : "'
                    . $app->input->getHtml('PICTURE_path') . '" }';
                break;
            case "5":
                $options['5'] = '{ "options" : "' . $app->input->getHtml('MULTISELECT_options') . '" }';
                break;
            case "6":
                $options['6'] = '{ "columns" : "' . $app->input->getHtml('TABLE_columns') . '" }';
                break;
        }
        $data['options'] = $options[$data['static_typeID']];

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
     * Delete element from list
     *
     * @return bool|mixed
     */
    public function delete()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $conditions = array(
            $db->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
        );

        $query->delete($db->quoteName('#__thm_groups_dynamic_type'));
        $query->where($conditions);

        $db->setQuery($query);
        $result = $db->execute();

        // Joomla 3.x Error handling style
        if ($db->getErrorNum())
        {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');

            return false;
        }

        return $result;
    }
}