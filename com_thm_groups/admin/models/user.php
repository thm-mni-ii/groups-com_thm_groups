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
jimport('thm_groups.data.lib_thm_groups_quickpages');

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

    public function deleteRoleInGroupByUser()
    {

    }

    public function deleteAllRolesInGroupByUser()
    {
        $input = JFactory::getApplication()->input;

        // Get array of ids if divers users selected
        $uid = $input->getInt('u_id', 0);
        var_dump($uid);die;

    }

    /**
     * Toggles the user
     *
     * @param   String  $action  publish/unpublish
     *
     * @return  boolean  true on success, otherwise false
     */
    public function toggle($action = null)
    {
        $db = JFactory::getDBO();
        $input = JFactory::getApplication()->input;

        // Get array of ids if divers users selected
        $cid = $input->post->get('cid', array(), 'array');

        // A string with type of column in table
        $attribute = $input->get('attribute', '', 'string');

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

        // will used if buttons (Publish/Unpublish user) in toolbar clicked
        switch ($action)
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
            ->where("id IN ( $id )");

        switch($attribute) {
            case 'canEdit':
                $query->set("canEdit = '$value'");
                break;
            case 'qpPublished':
                $query->set("qpPublished = '$value'");
                $this->createQuickpageCategoryForUser(array($id));
                break;
            case 'published':
            default:
                $query->set("published = '$value'");
                break;
        }

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
   