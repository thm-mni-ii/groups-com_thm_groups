<?php
/**
 * @package     Joomla.Administrator
 * @extension   com_thm_groups
 *
 * @copyright   2018 TH Mittelhessen
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Category table
 */
class TableCategory extends JTableCategory
{
    /**
     * Method to delete a node and, optionally, its child nodes from the table.
     *
     * @param   integer $pk       The primary key of the node to delete.
     * @param   boolean $children True to delete child nodes, false to move them up a level.
     *
     * @return  boolean  True on success.
     */
    public function delete($pk = null, $children = false)
    {
        return parent::delete($pk, $children);
    }
}
