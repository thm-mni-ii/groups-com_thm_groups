<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
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
        $item = $this->getSettings();
        return $item;
    }

    public function save($data)
    {
        $qp_enabled = $data['qp_enabled'];
        $qp_root_category = $data['qp_root_category'];
        $qp_move_subcategories = $data['qp_move_subcategories'];

        $item = $this->getSettings();

        // Save settings at first time
        if($item == null)
        {
            return($this->saveNewQPSettings($qp_enabled, $qp_root_category));
        }

        $qp_old_root_category = $item->qp_root_category;

        // TODO make if with &
        if($qp_move_subcategories == 1)
        {
            if($qp_root_category != $qp_old_root_category)
            {
                var_dump("TEST");
                $categories = JCategories::getInstance('Content');
                $cat = $categories->get($qp_old_root_category);

                $children = $cat->getChildren();

                $pks = [];
                foreach ($children as $child) {
                    array_push($pks, $child->id);
                }
                echo '<pre>';
                print_r($pks);
                echo '</pre>';
                $this->batchMove($qp_root_category, $pks, null);
            }
        }


        return($this->setNewQPSettings($qp_enabled, $qp_root_category));
    }

    /*
     * Update function
     */
    public function setNewQPSettings($qp_enabled, $qp_root_category)
    {

        // Get a new database query instance
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Build the query
        $query->update('#__thm_groups_quickpages_settings AS a');
        $query->set('a.qp_enabled = ' . $db->quote((string)$qp_enabled));
        $query->set('a.qp_root_category = ' . $db->quote((string)$qp_root_category));

        // Execute the query
        $db->setQuery($query);
        $success = $db->execute();

        if($success)
        {
            return true;
        }

        return false;
    }

    /*
     * Save function
     */
    public function saveNewQPSettings($qp_enabled, $qp_root_category)
    {

        // Get a new database query instance
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->insert('#__thm_groups_quickpages_settings');
        $query->columns('qp_enabled', 'qp_root_category');
        $query->values($qp_enabled, $qp_root_category);

        // Execute the query
        $db->setQuery($query);
        $success = $db->execute();

        if($success)
        {
            return true;
        }

        return false;
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
     * @since   1.6
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

            // Check that user has edit permission for every category being moved
            // Note that the entire batch operation fails if any category lacks edit permission
            /*foreach ($pks as $pk)
            {
                if (!$this->user->authorise('core.edit', $extension . '.category.' . $pk))
                {
                    // Error since user cannot edit this category
                    $this->setError(JText::_('COM_CATEGORIES_BATCH_CANNOT_EDIT'));
                    return false;
                }
            }*/
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

    public function getSettings()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery();

        $query
            ->select('qp_enabled, qp_root_category')
            ->from('#__thm_groups_quickpages_settings');
        $dbo->setQuery($query);

        return $dbo->loadObject();
    }
}