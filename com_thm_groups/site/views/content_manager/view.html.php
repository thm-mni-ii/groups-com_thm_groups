<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewContent_Manager
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
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
 * Class displays content in the profile's content category
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewContent_Manager extends JViewLegacy
{

	public $items;

	public $pagination;

	public $state;

	public $batch;

	public $groups;

	public $pageTitle;

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

		$this->state = $this->get('State');
		$this->items = $this->get('Items');

		$this->model        = $this->getModel();
		$this->categoryID   = $this->model->categoryID;
		$this->menuID       = JFactory::getApplication()->input->getInt('Itemid', 0);

		$profileID    = JFactory::getApplication()->input->getInt('profileID', JFactory::getUser()->id);
		$profileName = THM_GroupsHelperProfile::getDisplayName($profileID);
		$contextTitle = $profileID == JFactory::getUser()->id ?
			JText::_('COM_THM_GROUPS_MY_CONTENT') : JText::sprintf('COM_THM_GROUPS_MANAGE_CONTENT', $profileName);

		if (!empty($this->menuID))
		{
			$thisMenu  = JFactory::getApplication()->getMenu()->getItem($this->menuID);
			$menuQuery = $thisMenu->get('query');
			$isMenu    = (empty($menuQuery['view']) OR $menuQuery['view'] != 'content_manager') ? false : true;
		}
		else
		{
			$isMenu = false;
		}

		if ($isMenu)
		{
			$params = JFactory::getApplication()->getParams();
			$showPageTitle   = $params->get('show_page_heading', false);
			$this->pageTitle = $showPageTitle? $params->get('page_title', '') : '';
		}
		else
		{
			$this->pageTitle = $contextTitle;
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
			$returnURL = base64_encode("index.php?option=com_thm_groups&view=content_manager&Itemid=$menuID");
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
			return '';
		}
	}

	/**
	 * Creates the HTML for a link to the user profile
	 *
	 * @return  string  the HTML string for the profile button
	 */
	public function getProfileButton()
	{
		$profileID   = JFactory::getUser()->id;
		$groupID     = THM_GroupsHelperProfile::getDefaultGroup($profileID);
		$surname     = THM_GroupsHelperProfile::getAttributeValue($profileID, 2);
		$buttonURL   = "index.php?view=profile&layout=default&profileID=$profileID&name=$surname";
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
	 * @param   object $item the content item
	 *
	 * @return  string  the HTML for the row to be rendered
	 */
	public function getRow($key, $item)
	{
		$sortIcon  = '<span class="sortable-handler" style="cursor: move;"><i class="icon-menu"></i></span>';
		$sortInput = '<input type="text" style="display:none" name="order[]" size="5" ';
		$sortInput .= 'value="' . (string) $item->ordering . '" class="width-20 text-area-order">';
		$sort      = '<td class="order nowrap center" style="width: 40px;">' . $sortIcon . $sortInput . '</td>';

		$title = '<td>' . $this->getTitle($item) . '</td>';

		$published = '<td>';
		$published .= THM_GroupsHelperContent::getStatusDropdown($key, $item);
		$published .= JHtml::_('grid.id', $key, $item->id);
		$published .= '</td>';

		$listed = '<td class="btn-column">';
		$listed .= $this->getToggle($item->id, $item->groups_featured, 'featured');
		$listed .= '</td>';

		return $sort . $title . $published . $listed;
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
				$spanClass = 'icon-archive red';
				$spanTip   = JText::_('COM_THM_GROUPS_ARCHIVED');
				break;
			case TRASHED:
				$spanClass = 'icon-trash red';
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
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'content.publish\')">';
			$select .= '<i class="icon-publish"></i> ' . JText::_('COM_THM_GROUPS_PUBLISH');
			$select .= '</a></li>';
		}
		if ($state != UNPUBLISHED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'content.unpublish\')">';
			$select .= '<i class="icon-unpublish"></i> ' . JText::_('COM_THM_GROUPS_UNPUBLISH');
			$select .= '</a></li>';
		}
		if ($state != ARCHIVED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'content.archive\')">';
			$select .= '<i class="icon-archive"></i> ' . JText::_('COM_THM_GROUPS_ARCHIVE');
			$select .= '</a></li>';
		}
		if ($state != TRASHED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'content.trash\')">';
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
		$profileID = JFactory::getUser()->id;
		$surname   = THM_GroupsHelperProfile::getAttributeValue(JFactory::getUser()->id, 2);
		$menuID    = JFactory::getApplication()->input->getInt('Itemid', 0);

		$returnURL    = base64_encode(JUri::current());
		$editURL      = 'index.php?option=com_content&task=article.edit';
		$editURL      .= "&Itemid=$menuID'&a_id=$item->id&return=$returnURL";
		$editRoute    = JRoute::_($editURL);
		$titleAttribs = array('title' => JText::_('COM_THM_GROUPS_EDIT'));
		$titleLink    = JHTML::_('link', $editRoute, $item->title, $titleAttribs);


		$viewText     = '<span class="icon-eye-open"></span>';
		$contentURL   = 'index.php?option=com_thm_groups&view=content';
		$contentURL   .= "&id=$item->id&alias=$item->alias&profileID=$profileID&name=$surname";
		$contentRoute = JRoute::_($contentURL, false);
		$editAttribs  = array('title' => JText::_('COM_THM_GROUPS_VIEW'), 'class' => 'jgrid', 'target' => '_blank');
		$editLink     = JHTML::_('link', $contentRoute, $viewText, $editAttribs);

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
			$colorClass = 'green';
			$iconClass  = 'publish';
			$tip        = 'COM_THM_GROUPS_PUBLISHED';
		}
		else
		{
			$colorClass = 'red';
			$iconClass  = 'unpublish';
			$tip        = 'COM_THM_GROUPS_UNPUBLISHED';
		}

		$attributes                = array();
		$attributes['title']       = JText::_($tip);
		$attributes['data-toggle'] = "tooltip";

		$icon = '<span class="icon-' . $iconClass . ' ' . $colorClass . '"></span>';

		$menuID = JFactory::getApplication()->input->getInt('Itemid', 0);

		$url = "index.php?option=com_thm_groups&task=content.toggle";
		$url .= "&id=$id&value=$value&Itemid=$menuID";
		$url .= empty($attribute) ? '' : "&attribute=$attribute";

		$link = JHtml::_('link', JRoute::_($url), $icon, $attributes);

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
		$document->addStyleSheet($this->baseurl . "/media/com_thm_groups/css/content_manager.css");

		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.tooltip');

		// Used for pseudo-select boxes with icons
		JHtml::_('behavior.modal');
	}
}
