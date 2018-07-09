<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing functions usefull to multiple component files
 */
class THM_GroupsHelperComponent
{
    /**
     * Configure the Linkbar.
     *
     * @param   object &$view the view context calling the function
     *
     * @return void
     */
    public static function addSubmenu(&$view)
    {
        $thisName = $view->get('name');

        // No submenu creation while editing a resource
        if (strpos($thisName, 'edit')) {
            return;
        }

        $baseURL    = 'index.php?option=com_thm_groups&view=';
        $viewNames  = [
            'abstract_attribute_manager',
            'attribute_manager',
            'content_manager',
            'group_manager',
            'profile_manager',
            'role_manager',
            'template_manager'
        ];
        $otherViews = [];
        foreach ($viewNames as $viewName) {
            $otherViews[JText::_('COM_THM_GROUPS_' . strtoupper($viewName))]
                = ['name' => $viewName, 'link' => "$baseURL$viewName"];
        }
        ksort($otherViews);

        $component = 'thm_groups';
        $home      = [JText::_('COM_THM_GROUPS_HOME') => ['name' => $component, 'link' => "$baseURL$component"]];
        $menuItems = $home + $otherViews;

        foreach ($menuItems as $displayedText => $viewData) {
            JHtmlSidebar::addEntry($displayedText, $viewData['link'], $thisName == $viewData['name']);
        }

        $view->sidebar = JHtmlSidebar::render();
    }

    /**
     * Checks access for edit views
     *
     * @param   object &$model the model checking permissions
     * @param   int    $itemID the id if the resource to be edited (empty for new entries)
     *
     * @return  bool  true if the user can access the edit view, otherwise false
     */
    public static function allowEdit(&$model, $itemID = 0)
    {
        // Admins can edit anything. Department and monitor editing is implicitly covered here.
        $isAdmin = $model->actions->{'core.admin'};
        if ($isAdmin) {
            return true;
        }

        $name = $model->get('name');

        // Views accessible with component create/edit access
        $resourceEditViews = [
            'attribute_edit',
            'abstract_attribute_edit',
            'profile_edit',
            'role_edit',
            'template_edit'
        ];
        if (in_array($name, $resourceEditViews)) {
            if ((int)$itemID > 0) {
                return $model->actions->{'core.edit'};
            }

            return $model->actions->{'core.create'};
        }

        return false;
    }

    /**
     * Calls the appropriate controller
     *
     * @param boolean $isAdmin whether the file is being called from the backend
     *
     * @return  void
     */
    public static function callController($isAdmin = true)
    {
        $basePath = $isAdmin ? JPATH_COMPONENT_ADMINISTRATOR : JPATH_COMPONENT_SITE;

        $handler = explode(".", JFactory::getApplication()->input->getCmd('task', ''));

        if (count($handler) > 1) {
            $task = $handler[1];
        } else {
            $task = $handler[0];
        }

        /** @noinspection PhpIncludeInspection */
        require_once $basePath . '/controller.php';
        $controllerObj = new THM_GroupsController;
        $controllerObj->execute($task);
        $controllerObj->redirect();
    }

    /**
     * Method to check if the current user can edit the profile
     *
     * @param   int $profileID the id of the profile user
     *
     * @return  boolean  true if the current user is authorized to edit the profile, otherwise false
     */
    public static function canEditProfile($profileID)
    {
        $user = JFactory::getUser();

        if (empty($user->id)) {
            return false;
        }

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if ($isAdmin or $isComponentManager) {
            return true;
        }

        $params  = JComponentHelper::getParams('com_thm_groups');
        $allowed = (!empty($profileID) and $params->get('editownprofile', 0) == 1 and $user->id == $profileID);

        return $allowed;
    }

    /**
     * Clean the cache
     *
     * @return  void
     */
    public static function cleanCache()
    {
        $conf = JFactory::getConfig();

        $options = [
            'defaultgroup' => 'com_thm_groups',
            'cachebase'    => JFactory::getApplication()->isClient('administrator') ?
                JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
            'result'       => true,
        ];

        try {
            $cache = JCache::getInstance('callback', $options);
            $cache->clean();
        } catch (JCacheException $exception) {
            $options['result'] = false;
        }
        // Set the clean cache event
        if (isset($conf['event_clean_cache'])) {
            $event = $conf['event_clean_cache'];
        } else {
            $event = 'onContentCleanCache';
        }

        // Trigger the onContentCleanCache event.
        JEventDispatcher::getInstance()->trigger($event, $options);
    }

    /**
     * Cleans a given collection. Converts to array as necessary. Removes duplicate values. Enforces int type. Removes
     * 0 value indexes.
     *
     * @param mixed $array the collection to be cleaned (array|object)
     *
     * @return array the converted array
     */
    public static function cleanIntCollection($array)
    {
        if (!is_array($array)) {
            if (!is_object($array)) {
                return [];
            }

            $array = Joomla\Utilities\ArrayHelper::fromObject($array);
        }

        $array = Joomla\Utilities\ArrayHelper::toInteger(array_filter(array_unique($array)));

        return $array;
    }

    /**
     * Removes empty tags or tags with &nbsp; recursively
     *
     * @param string $original the original text
     *
     * @return string the text without empty tags
     */
    public static function cleanText($original)
    {
        $pattern = "/<[^\/>]*>([\s|\&nbsp;]?)*<\/[^>]*>/";
        $cleaned = preg_replace($pattern, '', $original);

        // If the text remains unchanged there is no more to be done => bubble up
        if ($original == $cleaned) {
            return $original;
        }

        // There could still be further empty tags which encased the original empties.
        return self::cleanText($cleaned);
    }

    /**
     * Redirects to the homepage and displays a message about missing access rights
     *
     * @return  void
     */
    public static function noAccess()
    {
        $app  = JFactory::getApplication();
        $msg  = JText::_('JLIB_RULES_NOT_ALLOWED');
        $link = JRoute:: _('index.php');
        $app->Redirect($link, $msg);
    }
}
