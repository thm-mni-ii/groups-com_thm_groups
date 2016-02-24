<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.categories');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelQp_Settings extends JModelAdmin
{
    const TYPE = 'quickpages';

    /**
     * Method to get the form
     *
     * @param   Array    $data      Data         (default: Array)
     * @param   Boolean  $loadData  Load data  (default: true)
     *
     * @return  mixed  JForm object on success, False on error.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        $option = $this->get('option');
        $name = $this->get('name');
        $form = $this->loadForm("$option.$name", $name, array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {
        $objParams = $this->getQPParams();
        if (empty($objParams))
        {
            return false;
        }
        $item = json_decode($objParams->params);

        return $item;
    }

    /**
     * Saves quickpages settings into database
     *
     * @param   array  $data  Data to save
     *
     * @return bool
     */
    public function save($data)
    {
        $qp_enabled = $data['qp_enabled'];
        $qp_show_all_categories = $data['qp_show_all_categories'];
        $qp_root_category = $data['qp_root_category'];
        $qp_move_subcategories = $data['qp_move_subcategories'];

        $params = array();
        $params['qp_enabled'] = $qp_enabled;
        $params['qp_show_all_categories'] = $qp_show_all_categories;
        $params['qp_root_category'] = $qp_root_category;

        $objParams = $this->getQPParams();

        // Save settings at first time
        if (empty($objParams))
        {
            return($this->saveNewQPSettings($params));
        }

        $oldParams = json_decode($objParams->params);
        $qp_old_root_category = $oldParams->qp_root_category;

        if ($qp_move_subcategories == 1)
        {
            if ($qp_root_category != $qp_old_root_category)
            {
                $categories = JCategories::getInstance('Content');
                $cat = $categories->get($qp_old_root_category);

                $children = $cat->getChildren();

                $pks = [];
                foreach ($children as $child)
                {
                    array_push($pks, $child->id);
                }
                $this->batchMove($qp_root_category, $pks, null);
            }
        }

        return($this->setNewQPSettings($params));
    }

    /**
     * Updates params in the database
     *
     * @param   array  $params  An array with params, which will be updated
     *
     * @return bool
     *
     * @throws Exception
     */
    public function setNewQPSettings($params)
    {

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->update('#__thm_groups_settings')
            ->set('params = ' . $db->q(json_encode($params)))
            ->where('type = ' . $db->q($this::TYPE));

        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    /**
     * Saves params if there are no params in database
     *
     * @param   array  $params  An array with params, which will be saved
     *
     * @return bool
     */
    public function saveNewQPSettings($params)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $params = $db->q(json_encode($params));
        $columns = array('type', 'params');
        $values = array($db->q($this::TYPE), $params);

        $query
            ->insert('#__thm_groups_settings')
            ->columns($db->qn($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    /**
     * Batch move categories to a new category.
     *
     * @param   integer  $value     The new category ID.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  boolean  True on success.
     *
     */
    protected function batchMove($value, $pks, $contexts)
    {
        $parentId = (int) $value;
        $this->table = JTable::getInstance('Category', 'Table');

        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $extension = JFactory::getApplication()->input->get('extension', '', 'word');

        // Check that the parent exists.
        if ($parentId)
        {
            if (!$this->table->load($parentId))
            {
                if ($error = $this->table->getError())
                {
                    // Fatal error
                    $this->setError($error);

                    return false;
                }
                else
                {
                    // Non-fatal error
                    $this->setError(JText::_('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
                    $parentId = 0;
                }
            }

            // Check that user has create permission for parent category
            //$canCreate = ($parentId == $this->table->getRootId()) ? $this->user->authorise('core.create', $extension) : $this->user->authorise('core.create', $extension . '.category.' . $parentId);
            $canCreate = true;
            if (!$canCreate)
            {
                // Error since user cannot create in parent category
                $this->setError(JText::_('COM_CATEGORIES_BATCH_CANNOT_CREATE'));
                return false;
            }
        }

        // We are going to store all the children and just move the category
        $children = array();

        // Parent exists so let's proceed
        foreach ($pks as $pk)
        {
            // Check that the row actually exists
            if (!$this->table->load($pk))
            {
                if ($error = $this->table->getError())
                {
                    // Fatal error
                    $this->setError($error);
                    return false;
                }
                else
                {
                    // Not fatal error
                    $this->setError(JText::sprintf('JGLOBAL_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // Set the new location in the tree for the node.
            $this->table->setLocation($parentId, 'last-child');

            // Check if we are moving to a different parent
            if ($parentId != $this->table->parent_id)
            {
                // Add the child node ids to the children array.
                $query->clear()
                    ->select('id')
                    ->from($db->quoteName('#__categories'))
                    ->where($db->quoteName('lft') . ' BETWEEN ' . (int) $this->table->lft . ' AND ' . (int) $this->table->rgt);
                $db->setQuery($query);

                try
                {
                    $children = array_merge($children, (array) $db->loadColumn());
                }
                catch (RuntimeException $e)
                {
                    $this->setError($e->getMessage());
                    return false;
                }
            }

            // Store the row.
            if (!$this->table->store())
            {
                $this->setError($this->table->getError());
                return false;
            }

            // Rebuild the tree path.
            if (!$this->table->rebuildPath())
            {
                $this->setError($this->table->getError());
                return false;
            }
        }

        // Process the child rows
        if (!empty($children))
        {
            // Remove any duplicates and sanitize ids.
            $children = array_unique($children);
            JArrayHelper::toInteger($children);
        }

        return true;
    }

    /**
     * Returns quickpages parameters
     *
     * @return bool|object with params
     *
     * @throws Exception
     */
    public function getQPParams()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('params')
            ->from('#__thm_groups_settings')
            ->where('type = "quickpages"');

        $db->setQuery($query);

        try
        {
            $result = $db->loadObject();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        return $result;
    }
}