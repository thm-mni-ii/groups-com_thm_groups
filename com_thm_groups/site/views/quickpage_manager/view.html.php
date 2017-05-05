<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewQuickpage_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

define('PUBLISHED', 1);
define('UNPUBLISHED', 0);
define('ARCHIVED', 2);
define('TRASHED', -2);

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';

/**
 * THMGroupsViewUserManager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewQuickpage_Manager extends JViewLegacy
{

	public $items;

	public $pagination;

	public $state;

	public $batch;

	public $groups;

	public $url;

	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$this->modifyDocument();

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		$this->model      = $this->getModel();
		$this->categoryID = $this->model->categoryID;
		$this->menuID     = JFactory::getApplication()->input->getInt('Itemid', 0);

		$this->pageTitle = '';
		$params          = JFactory::getApplication()->getParams();
		$showPageTitle   = $params->get('show_page_heading', 0);
		if ($showPageTitle)
		{
			$defaultPageTitle = JText::_('COM_THM_GROUPS_QUICKPAGE_CONTENT_MANAGER');
			$menuTitle        = $params->get('page_title', '');
			$this->pageTitle  .= empty($menuTitle) ? $defaultPageTitle : $menuTitle;
		}

		parent::display($tpl);
	}

	/**
	 * Returns a button for creating of a new article
	 *
	 * @return mixed|string
	 */
	public function getNewButton()
	{
		$canCreate = $this->model->hasUserRightToCreateArticle();

		if ($canCreate)
		{
			$menuID    = JFactory::getApplication()->input->getInt('Itemid', 0);
			$returnURL = base64_encode("index.php?option=com_thm_groups&view=quickpage_manager&Itemid=$menuID");
			$addURL    = JRoute::_('index.php?option=com_content&view=form&layout=edit&catid='
				. $this->categoryID . '&return=' . $returnURL
			);

			$attribs = array(
				'title' => JText::_('COM_THM_GROUPS_NEW_ARTICLE'),
				'class' => 'btn'
			);

			$text = '<span class="icon-new"></span> ' . JText::_('COM_THM_GROUPS_NEW_ARTICLE');

			return JHTML::_('link', $addURL, $text, $attribs);

		}
		else
		{
			return '<span class="qp_icon_big qp_create_icon_disabled"><span class="qp_invisible_text">' .
				JText::_('COM_THM_GROUPS_NEW_ARTICLE') . '</span></span>';
		}
	}

	/**
	 * Creates the HTML for a link to the user profile
	 *
	 * @return  string  the HTML string for the profile button
	 */
	public function getProfileButton()
	{
		$userID      = JFactory::getUser()->id;
		$groupID     = THM_GroupsHelperProfile::getDefaultGroup(JFactory::getUser()->id, 2);
		$surname     = THM_GroupsHelperProfile::getAttributeValue(JFactory::getUser()->id, 2);
		$buttonURL   = "index.php?view=profile&layout=default&userID=$userID&name=$surname";
		$buttonURL   .= empty($groupID) ? '' : "&groupID=$groupID";
		$buttonRoute = JRoute::_($buttonURL);

		$buttonText = '<span class="icon-user"></span>' . JText::_('COM_THM_GROUPS_MY_PROFILE');

		$attribs = array(
			'class' => 'btn'
		);

		return JHTML::_('link', $buttonRoute, $buttonText, $attribs);
	}

	/**
	 * Creates the output data for the table row
	 *
	 * @param   int    $key  the row id
	 * @param   object $item the quickpage object
	 *
	 * @return  string  the HTML for the row to be rendered
	 */
	public function getRow($key, $item)
	{
		$title = '<td>' . $this->getTitle($item) . '</td>';

		$published = '<td>';
		$published .= $this->getStateSelect($key, $item->state);
		$published .= JHtml::_('grid.id', $key, $item->id);
		$published .= '</td>';

		$listed = '<td class="btn-column">';
		$listed .= $this->getToggle($item->id, $item->qp_featured, 'featured');
		$listed .= '</td>';

		return $title . $published . $listed;
	}

	/**
	 * Creates the HMTL for the status select box
	 *
	 * @param   int $key   the row id
	 * @param   int $state the content state
	 *
	 * @return string
	 */
	public function getStateSelect($key, $state)
	{

		$spanClass = '';
		$spanTip   = '';
		switch ($state)
		{
			case PUBLISHED:
				$spanClass = 'icon-publish green';
				$spanTip   = JText::_('COM_THM_GROUPS_PUBLISHED');
				break;
			case UNPUBLISHED:
				$spanClass = 'icon-unpublish red';
				$spanTip   = JText::_('COM_THM_GROUPS_UNPUBLISHED');
				break;
			case ARCHIVED:
				$spanClass = 'icon-archive';
				$spanTip   = JText::_('COM_THM_GROUPS_ARCHIVED');
				break;
			case TRASHED:
				$spanClass = 'icon-trash';
				$spanTip   = JText::_('COM_THM_GROUPS_TRASHED');
				break;
		}

		$select = '<span class="status-container ' . $spanClass . ' hasTip" title="' . $spanTip . '"></span>';
		$select .= '<div class="btn-group">';
		$select .= '<a class="btn dropdown-toggle stateid" data-toggle="dropdown" href="#">';
		$select .= JText::_('COM_THM_GROUPS_CHANGE_STATUS');
		$select .= '<span class="icon-arrow-down-3 pull-right"></span></a>';
		$select .= '<ul id="category" class="dropdown-menu">';

		if ($state != PUBLISHED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'quickpage.publish\')">';
			$select .= '<i class="icon-publish"></i> ' . JText::_('COM_THM_GROUPS_PUBLISH');
			$select .= '</a></li>';
		}
		if ($state != UNPUBLISHED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'quickpage.unpublish\')">';
			$select .= '<i class="icon-unpublish"></i> ' . JText::_('COM_THM_GROUPS_UNPUBLISH');
			$select .= '</a></li>';
		}
		if ($state != ARCHIVED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'quickpage.archive\')">';
			$select .= '<i class="icon-archive"></i> ' . JText::_('COM_THM_GROUPS_ARCHIVE');
			$select .= '</a></li>';
		}
		if ($state != TRASHED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'quickpage.trash\')">';
			$select .= '<i class="icon-trash"></i> ' . JText::_('COM_THM_GROUPS_TRASH');
			$select .= '</a></li>';
		}
		$select .= '</ul>';
		$select .= '</div>';

		return $select;
	}

	/**
	 * Returns a title of an article
	 *
	 * @param   object &$item An object item
	 *
	 * @return  string
	 */
	public function getTitle(&$item)
	{
		$userID  = JFactory::getUser()->id;
		$surname = THM_GroupsHelperProfile::getAttributeValue(JFactory::getUser()->id, 2);
		$menuID  = JFactory::getApplication()->input->getInt('Itemid', 0);

		$returnURL    = base64_encode("index.php?option=com_thm_groups&view=quickpage_manager&Itemid=$menuID");
		$editURL      = 'index.php?option=com_content&task=article.edit';
		$editURL      .= "&Itemid=$menuID'&a_id=$item->id&return=$returnURL";
		$editRoute    = JRoute::_($editURL);
		$titleAttribs = array('title' => JText::_('COM_THM_GROUPS_EDIT'));
		$titleLink    = JHTML::_('link', $editRoute, $item->title, $titleAttribs);


		$viewText    = '<span class="icon-eye-open"></span>';
		$qpURL       = 'index.php?option=com_thm_groups&view=singlearticle';
		$qpURL       .= "&id=$item->id&nameqp=$item->alias&userID=$userID&name=$surname";
		$qpRoute     = JRoute::_($qpURL, false);
		$editAttribs = array('title' => JText::_('COM_THM_GROUPS_VIEW'), 'class' => 'jgrid');
		$editLink    = JHTML::_('link', $qpRoute, $viewText, $editAttribs);

		$category = "<div class='small'>" . JText::_('JCATEGORY') . ": " . $item->category_title . "</div>";

		return $titleLink . $editLink . $category;
	}

	/**
	 * Generates a toggle for the attribute in question
	 *
	 * @param   int    $id        the id of the database entry
	 * @param   bool   $value     the value currently set for the attribute (saves asking it later)
	 * @param   string $attribute the resource attribute to be changed (useful if multiple entries can be toggled)
	 *
	 * @return  string  a HTML string
	 */
	public function getToggle($id, $value, $attribute)
	{
		if ($value)
		{
			$colorClass  = 'green';
			$iconClass   = 'publish';
			$tip         = 'COM_THM_GROUPS_PUBLISHED';
			$toggleValue = '0';
		}
		else
		{
			$colorClass  = 'red';
			$iconClass   = 'unpublish';
			$tip         = 'COM_THM_GROUPS_UNPUBLISHED';
			$toggleValue = '1';
		}

		$attributes                = array();
		$attributes['title']       = JText::_($tip);
		$attributes['data-toggle'] = "tooltip";

		$icon = '<span class="icon-' . $iconClass . ' ' . $colorClass . '"></span>';

		$menuID = JFactory::getApplication()->input->getInt('Itemid', 0);
		$url    = "index.php?option=com_thm_groups&task=quickpage.toggle&id=$id&value=$toggleValue&Itemid=$menuID";
		$url    .= empty($attribute) ? '' : "&attribute=$attribute";
		$link   = JHtml::_('link', $url, $icon, $attributes);

		return $link;
	}

	/**
	 * Adds styles and scripts to the document
	 *
	 * @return  void  modifies the document
	 */
	protected function modifyDocument()
	{
		$document = Jfactory::getDocument();
		$document->addStyleSheet($this->baseurl . "/media/com_thm_groups/css/quickpage_manager.css");

		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.tooltip');

		// Used for pseudo-select boxes with icons
		JHtml::_('behavior.modal');
	}
}
