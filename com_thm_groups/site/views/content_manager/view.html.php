<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once HELPERS . 'content.php';
require_once HELPERS . 'profiles.php';

/**
 * Class displays content in the profile's content category
 */
class THM_GroupsViewContent_Manager extends JViewLegacy
{
	const ARCHIVED = 2;
	const PUBLISHED = 1;
	const TRASHED = -2;
	const UNPUBLISHED = 0;

	private $canCreate;

	public $canEdit;

	private $categoryID;

	public $items;

	public $pageTitle;

	public $profileID;

	/**
	 * Method to get display
	 *
	 * @param   Object  $tpl  template
	 *
	 * @return void
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$user            = JFactory::getUser();
		$this->profileID = JFactory::getApplication()->input->getInt('profileID', $user->id);
		if (empty($this->profileID) or !THM_GroupsHelperProfiles::isPublished($this->profileID))
		{
			$exc = new Exception(JText::_('COM_THM_GROUPS_PROFILE_NOT_FOUND'), '404');
			JErrorPage::render($exc);
		}

		$this->categoryID = THM_GroupsHelperCategories::getIDByProfileID($this->profileID);
		$contentEnabled   = THM_GroupsHelperProfiles::contentEnabled($this->profileID);
		if (empty($this->categoryID) or empty($contentEnabled))
		{
			$exc = new Exception(JText::_('COM_THM_GROUPS_ERROR_412'), '412');
			JErrorPage::render($exc);
		}

		$this->canCreate = THM_GroupsHelperCategories::canCreate($this->categoryID);
		$this->canEdit   = THM_GroupsHelperCategories::canEdit($this->categoryID);
		$this->items     = $this->get('Items');
		$this->menuID    = JFactory::getApplication()->input->getInt('Itemid', 0);
		$this->modifyDocument();

		if ($this->profileID == $user->id)
		{
			$contextTitle = JText::_('COM_THM_GROUPS_MY_CONTENT');
		}
		elseif ($this->canEdit)
		{
			$contextTitle = JText::sprintf(
				'COM_THM_GROUPS_MANAGE_PROFILE_CONTENT',
				THM_GroupsHelperProfiles::getDisplayName($this->profileID)
			);
		}
		else
		{
			$contextTitle = JText::sprintf(
				'COM_THM_GROUPS_PROFILE_CONTENT',
				THM_GroupsHelperProfiles::getDisplayName($this->profileID)
			);
		}
		$this->pageTitle = $contextTitle;

		parent::display($tpl);
	}

	/**
	 * Returns a button for creating of a new article
	 *
	 * @return mixed|string
	 */
	public function getNewButton()
	{
		if ($this->canCreate)
		{
			$return  = base64_encode(Joomla\CMS\Uri\Uri::getInstance()->toString());
			$addURL  = JUri::base() . '?option=com_content&task=article.add';
			$addURL  .= "&catid={$this->categoryID}&return=$return";
			$attribs = ['title' => JText::_('COM_THM_GROUPS_NEW_ARTICLE'), 'class' => 'btn'];
			$text    = '<span class="icon-new"></span> ' . JText::_('COM_THM_GROUPS_NEW_ARTICLE');

			return JHTML::_('link', JRoute::_($addURL), $text, $attribs);
		}
		else
		{
			return '';
		}
	}

	/**
	 * Creates the output data for the table row
	 *
	 * @param   int     $key   the row id
	 * @param   object  $item  the content item
	 *
	 * @return  string  the HTML for the row to be rendered
	 * @throws Exception
	 */
	public function getRow($key, $item)
	{
		if ($this->canEdit)
		{
			$sortIcon  = '<span class="sortable-handler" style="cursor: move;"><i class="icon-menu"></i></span>';
			$sortInput = '<input type="text" style="display:none" name="order[]" ';
			$sortInput .= 'value="' . (string) $item->ordering . '" class="width-20 text-area-order">';
			$sort      = '<td class="order nowrap center btn-column" style="width: 40px;">' . $sortIcon . $sortInput;
			$sort      .= '</td>';

			$published = '<td class="publish-column">';
			$published .= THM_GroupsHelperContent::getStatusDropdown($key, $item);
			$published .= JHtml::_('grid.id', $key, $item->id);
			$published .= '</td>';

			$listed = '<td class="btn-column">';
			$listed .= $this->getToggle($item->id, $item->featured, 'featured');
			$listed .= '</td>';
		}
		else
		{
			$sort      = '';
			$published = '';
			$listed    = '';
		}

		$title = '<td class="title-column">' . $this->getTitle($key, $item) . '</td>';

		return $sort . $title . $published . $listed;
	}

	/**
	 * Creates the HMTL for the status select box
	 *
	 * @param   int  $key    the row id
	 * @param   int  $state  the content state
	 *
	 * @return string
	 */
	public function getStateSelect($key, $state)
	{
		$spanClass = '';
		$spanTip   = '';

		switch ($state)
		{
			case self::PUBLISHED:
				$spanClass = 'icon-publish green';
				$spanTip   = JText::_('COM_THM_GROUPS_PUBLISHED');
				break;
			case self::UNPUBLISHED:
				$spanClass = 'icon-unpublish red';
				$spanTip   = JText::_('COM_THM_GROUPS_UNPUBLISHED');
				break;
			case self::ARCHIVED:
				$spanClass = 'icon-archive red';
				$spanTip   = JText::_('COM_THM_GROUPS_ARCHIVED');
				break;
			case self::TRASHED:
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

		if ($state != self::PUBLISHED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'content.publish\')">';
			$select .= '<i class="icon-publish"></i> ' . JText::_('COM_THM_GROUPS_PUBLISH');
			$select .= '</a></li>';
		}
		if ($state != self::UNPUBLISHED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'content.unpublish\')">';
			$select .= '<i class="icon-unpublish"></i> ' . JText::_('COM_THM_GROUPS_UNPUBLISH');
			$select .= '</a></li>';
		}
		if ($state != self::ARCHIVED)
		{
			$select .= '<li><a href="javascript://" onclick="listItemTask(\'cb' . $key . '\', \'content.archive\')">';
			$select .= '<i class="icon-archive"></i> ' . JText::_('COM_THM_GROUPS_ARCHIVE');
			$select .= '</a></li>';
		}
		if ($state != self::TRASHED)
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
	 * @param   int     $key   the key of the item being iterated
	 * @param   object &$item  An object item
	 *
	 * @return  string the HTML for the title
	 * @throws Exception
	 */
	public function getTitle($key, &$item)
	{
		$contentLinkAttribs = [
			'class'  => 'view-link',
			'target' => '_blank',
			'title'  => JText::_('COM_THM_GROUPS_VIEW')
		];
		$lock               = '';
		$formLink           = '';

		if ($this->canEdit)
		{
			if (!empty($item->checked_out))
			{
				$author = empty($item->author_name) ? '' : $item->author_name;
				$lock   = JHtml::_('jgrid.checkedout', $key, $author, $item->checked_out_time, 'content.',
					true);
			}

			$returnURL = base64_encode(Joomla\CMS\Uri\Uri::getInstance()->toString());
			$formURL   = JUri::base() . "?option=com_content&task=article.edit&a_id=$item->id&return=$returnURL";
			$formURL   = JRoute::_($formURL);
			$formLink  .= JHTML::_('link', $formURL, $item->title, ['title' => JText::_('COM_THM_GROUPS_EDIT')]);

			$contentText = '<span class="icon-eye-open"></span>';
		}
		else
		{
			$contentText = $item->title;
		}

		$params     = ['id' => $item->id, 'profileID' => $this->profileID, 'view' => 'content'];
		$contentURL = THM_GroupsHelperRouter::build($params);

		return $lock . $formLink . JHTML::_('link', $contentURL, $contentText, $contentLinkAttribs);
	}

	/**
	 * Generates a toggle for the attribute in question
	 *
	 * @param   int     $id         the id of the database entry
	 * @param   bool    $value      the value currently set for the attribute (saves asking it later)
	 * @param   string  $attribute  the resource attribute to be changed (useful if multiple entries can be toggled)
	 *
	 * @return  string  a HTML string
	 * @throws Exception
	 */
	public function getToggle($id, $value, $attribute)
	{
		if ($value)
		{
			$iconClass = 'publish';
			$tip       = 'COM_THM_GROUPS_PUBLISHED';
		}
		else
		{
			$iconClass = 'unpublish';
			$tip       = 'COM_THM_GROUPS_UNPUBLISHED';
		}

		$menuID     = JFactory::getApplication()->input->getInt('Itemid');
		$url        = JUri::base() . "?option=com_thm_groups&task=content.toggle&id=$id&value=$value";
		$url        .= empty($menuID) ? '' : "&Itemid=$menuID";
		$url        .= empty($attribute) ? '' : "&attribute=$attribute";
		$icon       = '<span class="icon-' . $iconClass . '"></span>';
		$attributes = ['title' => JText::_($tip), 'class' => 'btn', 'data-toggle' => 'tooltip'];

		$link = JHtml::_('link', $url, $icon, $attributes);

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
