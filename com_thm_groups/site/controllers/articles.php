<?php

/**
 * @version     v0.1.0
 * @category    Joomla component
 * @package	    THM_Groups
 * @subpackage  com_thm_groups.site
 * @author	    Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link		www.mni.thm.de
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Articles list controller class.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsControllerArticles extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		// Articles default form can come from the articles or featured view.
		// Adjust the redirect view on the value of 'view' in the request.
		if (JRequest::getCmd('view') == 'featured')
		{
			$this->view_list = 'featured';
		}
		parent::__construct($config);

		/* $this->registerTask('unfeatured',	'featured'); */
		/* $this->registerTask('untrash',	'trash'); */
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Configuration
	 *
	 * @return	JModel
	 */
	public function getModel($name = 'Article', $prefix = 'THMGroupsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}

