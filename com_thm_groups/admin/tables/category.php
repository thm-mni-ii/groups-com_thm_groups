<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_thm_groups
 *
 * @copyright   2016 TH Mittelhessen
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Category table
 *
 * @package     Joomla.Administrator
 * @subpackage  com_categories
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
	 *
	 * @see     http://docs.joomla.org/JTableNested/delete
	 */
	public function delete($pk = null, $children = false)
	{
		return parent::delete($pk, $children);
	}
}
