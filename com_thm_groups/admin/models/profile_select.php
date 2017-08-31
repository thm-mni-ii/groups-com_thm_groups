<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelProfile_Select
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * Class compiling a list of users
 *
 * @category    Joomla.Component.Admin
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.admin
 * @link        www.thm.de
 */
class THM_GroupsModelProfile_Select extends JModelList
{
	protected $defaultOrdering = 'name';

	protected $defaultDirection = 'ASC';

	/**
	 * sets variables and configuration data
	 *
	 * @param   array $config the configuration parameters
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('username', 'name');
		}
		parent::__construct($config);
	}
}
