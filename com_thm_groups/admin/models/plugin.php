<?php
/**
 * @version     v3.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelsPlugin
 * @description THMGroupsModelsPlugin class from com_thm_groups
 * @author      Florian Kolb,	<florian.kolb@mni.thm.de>
 * @author      Henrik Huller,	<henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf,	<iuliia.krauskopf@mni.thm.de>
 * @author      Paul Meier, 	<paul.meier@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

/**
 * THMGroupsModelPlugin class for component com_thm_groups
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelPlugin extends JModelLegacy
{
 
    /**
    * Not implemented yet
    * 
    * @return void
    */
    public function edit()
    {
 
    }
 
    /**
     * Method to enable plugins
     * 
     * @param	unknown  $cid  all extensionIDs from the pluginmanager 
     *
     * @return boolean true or false for selected plugin
     */

    public function enable($cid)
    {
        $db = JFactory::getDbo();
        
		if ($cid == null)
        {

            $cid = JRequest::getVar('cid', array(), 'post', 'array');

            JArrayHelper::toInteger($cid);

        }
        else
        {
        	$cid = array($cid);
        }
        
        // Ab hier alles beim alten
        $cids = implode('\',\'', $cid);
 
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'));
        $query->set('enabled = "1"');
        $query->where("extension_id IN  ( '" . $cids . "' )");
 
        $db->setQuery($query);

        if ($db->query()) 
        {
            $result = true;
        } 
        else 
        {
            $result = false;
        }
        return $result;
    }
 
    /**
     * Method to disable the plugins
     * 
     * @param	unknown  $cid  all extensionIDs from the pluginmanager
     *
     * @return	boolean  true or false for selected plugins
     */

    public function disable($cid)
    {

        $db = JFactory::getDbo();

        if ($cid == null)
        {

            $cid = JRequest::getVar('cid', array(), 'post', 'array');

            JArrayHelper::toInteger($cid);

        }
        else
        {
        	$cid = array($cid);
        }
        
        // Ab hier alles beim alten
        $cids = implode('\',\'', $cid);
 
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'));
        $query->set('enabled = "0"');
        $query->where("extension_id IN  ( '" . $cids . "' )");
 
        $db->setQuery($query);
 
        if ($db->query())
		{
            $result = true;
		}
        else
		{
            $result = false;
		}
        return $result;
    }
 
    /**
     * Toggle switch method to enable or disable a plugin
     * 
     * @param	unknown  &$val  Value for the toggleswitch
     *
     * @return boolean 
     */
    public function toggle(&$val)
    {

        $val = JRequest::getVar('value');
 
        $id = JRequest::getVar('id');
        
        if ($val == 1)
		{
			return $this->disable($id);
		}
		else
		{
           return $this->enable($id);
        }
    }
}
