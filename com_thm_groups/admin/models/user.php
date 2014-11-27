<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        user model
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
class THM_GroupsModelUser extends JModelLegacy
{

    /**
     * Key character to identify the ID in the mapping table as user ID
     */
    const TABLE_USER_ID_KIND = 'U';

    /**
     * Name of request parameter for a user ID
     */
    const PROFILE_USER_ID_PARAM = 'gsuid';

    /**
     * Delete user
     *
     * @return bool true on success, otherwise false
     */
    public function delete()
    {
        $categoryIDs = JFactory::getApplication()->input->get('cid', array(), 'array');
        if (count($categoryIDs))
        {
            $dbo = $this->getDbo();
            $dbo->transactionStart();

            // Remove events / event resources / content dependent upon this category
            $query = $dbo->getQuery(true);
            $query->select("DISTINCT (id)");
            $query->from("#__thm_organizer_events");
            $query->where("categoryID IN ( '" . implode("', '", $categoryIDs) . "' )");
            $dbo->setQuery((string) $query);

            try
            {
                $eventIDs = $dbo->loadColumn();
            }
            catch (runtimeException $e)
            {
                throw new Exception(JText::_("COM_THM_ORGANIZER_DATABASE_EXCEPTION"), 500);
            }

            if (count($eventIDs))
            {
                $eventsModel = new THM_OrganizerModelEvent;
                foreach ($eventIDs as $eventID)
                {
                    $success = $eventsModel->delete($eventID);
                    if (!$success)
                    {
                        $dbo->transactionRollback();
                        return false;
                    }
                }
            }

            $category = JTable::getInstance('categories', 'thm_organizerTable');
            foreach ($categoryIDs as $categoryID)
            {
                $success = $category->delete($categoryID);
                if (!$success)
                {
                    $dbo->transactionRollback();
                    return false;
                }
            }
            $dbo->transactionCommit();
            return true;
        }
        return true;
    }

    /**
     * Toggles the user
     *
     * @param   String  $attribute  publish/unpublish
     *
     * @return  boolean  true on success, otherwise false
     */
    public function toggle($attribute)
    {
        $db = JFactory::getDBO();
        $input = JFactory::getApplication()->input;

        // Get array of ids if divers users selected
        //$cid = JRequest::getVar('cid', array(), 'post', 'array');
        $cid = $input->post->get('cid', array(), 'array');

        // If array is empty, the toggle button was clicked
        if (empty($cid))
        {
            $id = $input->getInt('id', 0);
        }
        else
        {
            JArrayHelper::toInteger($cid);

            $id = implode(',', $cid);
        }

        if (empty($id))
        {
            return false;
        }

        switch ($attribute)
        {
            case 'publish':
                $value = 1;
                break;
            case 'unpublish':
                $value = 0;
                break;
            default:
                $value = $input->getInt('value', 1)? 0 : 1;
                break;
        }

        $query = $db->getQuery(true);

        $query
            ->update('#__thm_groups_users')
            ->set("published = '$value'")
            ->where("id IN ( $id )");

        echo "<pre>";
        echo $query;
        echo "</pre>";

        $db->setQuery((string) $query);

        try
        {
            return (bool) $db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Create quickpage category
     *
     * @param   $cid  Array  array with ids
     *
     * @access  public
     * @return  void
     */
    public function createQuickpageCategoryForUser($cid)
    {
        foreach ($cid as $id)
        {
            $profileData['Id'] = $id;
            $profileData['IdKind'] = self::TABLE_USER_ID_KIND;
            $profileData['ParamName'] = self::PROFILE_USER_ID_PARAM;

            // Check if user's quickpage category exist and if not, create it
            if (!THMLibThmQuickpages::existsQuickpageForProfile($profileData))
            {
                THMLibThmQuickpages::createQuickpageForProfile($profileData);
            }
        }
    }
}
   