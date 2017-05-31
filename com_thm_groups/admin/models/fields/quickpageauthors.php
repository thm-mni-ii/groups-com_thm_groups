<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldQuickpageauthors
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
jimport('thm_groups.data.lib_thm_groups_quickpages');

/**
 * Class JFormFieldQuickpageauthors which returns authors of specific content named Quickpages
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class JFormFieldQuickpageauthors extends JFormFieldList
{

    protected $type = 'quickpageauthors';

    /**
     * Cached array of the category items.
     *
     * @var    array
     */
    protected static $options = array();

    /**
     * Returns a list of all Quickpages authors, even they don't have
     * articles in their categories
     *
     * @return  mixed  array on success, otherwise false
     */
    public function getQPAuthors()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $rootCategory = THMLibThmQuickpages::getQuickpagesRootCategory();
        $query
            ->select('users.id, users.name')
            ->from('#__users AS users')
            ->leftJoin('#__categories AS cat on cat.created_user_id = users.id')
            ->where("cat.parent_id = $rootCategory")
            ->where("cat.published = 1")
            ->order('users.name')
            ->group('users.id');

        $dbo->setQuery($query);

        try
        {
            return $dbo->loadAssocList();
        }
        catch (Exception $exception)
        {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }
    }

    /**
     * Method to get the options to populate to populate list
     *
     * @return  array  The field option objects.
     *
     */
    protected function getOptions()
    {
        $options = array();

        $rootCategory = THMLibThmQuickpages::getQuickpagesRootCategory();

        if (empty($rootCategory))
        {
            return parent::getOptions();
        }

        $arrayOfModerators = $this->getQPAuthors();

        // Convert array to options
        foreach ($arrayOfModerators as $key => $value)
        {
            $options[] = JHTML::_('select.option', $value['id'], $value['name']);
        }

        return array_merge(parent::getOptions(), $options);
    }
}