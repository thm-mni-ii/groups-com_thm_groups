<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewPlugin_Members_Select
 * @description THM_GroupsViewPlugin_Members_Select class for editors xtd plugin
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Mehmet Ali Pamukci, <mehmet.ali.pamukci@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
JHtml::_('formbehavior.chosen', 'select');

/**
 * THM_GroupsViewPlugin_Members_Select class for editors xtd plugin
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewPlugin_Members_Select extends JViewLegacy
{
    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $lang = JFactory::getLanguage();
        $lang->load('plg_thm_groups_editors_xtd_members', JPATH_PLUGINS . "/editors-xtd/plg_thm_groups_editors_xtd_members/", $lang->getTag(), true);
        JText::script('PLG_ALERT_USERS');
        JText::script('PLG_ALERT_SUFFIX');
        JText::script('PLG_ALERT_GROUPS');
        JText::script('PLG_ALERT_PID');

        $ename = 'jform_articletext';
        $script = "
            function hasClass(element, cls) {
                return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
            }

            function insert() {
                var $ = jQuery.noConflict();
                var temp = $('#uid').val();
                var suffixUsers=$('#suffixUsers').val();
                var suffixGroups=$('#suffixGroups').val();
                var gid=$('#gid').val();
                var pid=$('#pid').val();
                var ename = '". $ename . "';

                    if(hasClass(document.getElementById('slider_1'),'tab-pane active')==1){
                     if(temp==null ){
                         window.alert(Joomla.JText._('PLG_ALERT_USERS'));
                     }else if(pid==null){
                        window.alert(Joomla.JText._('PLG_ALERT_PID'));
                    }else{

                    if(suffixUsers==''){
                   window.alert(Joomla.JText._('PLG_ALERT_SUFFIX'));
                    suffixUsers='Standard';

                    }

                         window.parent.jInsertEditorText('{'+'contact'+suffixUsers+' '+'uid='+temp+'|'+'pid='+pid+'}', ename);
                            window.parent.jModalClose();
                         }
                    }else if(hasClass(document.getElementById('slider_2'),'tab-pane active')==1){

                       if(gid==null){
                       window.alert(Joomla.JText._('PLG_ALERT_GROUPS'));
                       }else{

                         if(suffixGroups==''){
                   window.alert(Joomla.JText._('PLG_ALERT_SUFFIX'));
                    suffixGroups='Standard';

                    }
                       window.parent.jInsertEditorText('{'+'contact'+suffixGroups+' '+'gid='+gid+'}', ename);
                       window.parent.jModalClose();
                       }
                    }
            }
        ";

        JFactory::getDocument()->addScriptDeclaration($script);
        $plugin = JPluginHelper::getPlugin('editors-xtd', 'plg_thm_groups_editors_xtd_members');
        $params = new JRegistry($plugin->params);

        parent::display($tpl);
    }
}
