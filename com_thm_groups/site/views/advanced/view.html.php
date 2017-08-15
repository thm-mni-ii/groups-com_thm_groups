<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_data.php";

/**
 * THMGroupsViewAdvanced class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.thm.de
 */
class THM_GroupsViewAdvanced extends JViewLegacy
{
	public $columns;

	private $groupID;

	private $isAdmin;

	private $menuID;

	public $profiles;

	private $showRoles;

	public $sort;

	private $suppressText;

	public $title;

	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$input     = $app->input;
		$profileID = $input->get('profileID', 0);

		if ($profileID)
		{
			$this->addBreadCrumb($profileID);
		}

		$this->model = $this->getModel();
		$params      = $this->model->params;

		$this->columns      = $params->get('columns', 2);
		$this->groupID      = $params->get('groupID');
		$this->isAdmin      = empty(JFactory::getUser()->authorise('core.admin', 'com_thm_groups')) ? false : true;
		$this->menuID       = $input->get('Itemid', 0, 'get');
		$this->profiles     = $this->model->getProfiles();
		$this->showRoles    = $params->get('showRoles', true);
		$this->sort         = $params->get('sort', 1);
		$this->suppressText = $params->get('suppress', true);
		$this->title        = empty($params->get('show_page_heading')) ? '' : $params->get('page_title', '');

		$this->modifyDocument();

		parent::display($tpl);
	}

	/**
	 * Adds a the selected profile user's name to the path context (breadcrumbs)
	 *
	 * @param   int $profileID the profile id of the selected user
	 *
	 * @return void adds the selected username to the application's path context
	 */
	private function addBreadCrumb($profileID)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();

		$query = $dbo->getQuery(true);
		$query->select('ua.attributeID, ua.value');
		$query->from('#__thm_groups_users_attribute AS ua');
		$query->where('usersID = ' . $profileID);
		$query->where('attributeID IN (1,2)');

		$dbo->setQuery($query);

		$nameValues = $dbo->loadAssocList();
		$names      = array(0 => '', 1 => '');

		foreach ($nameValues as $nameValue)
		{
			if ((int) $nameValue['attributeID'] === 1)
			{
				$names[1] = $nameValue['value'];
			}
			if ((int) $nameValue['attributeID'] === 2)
			{
				$names[0] = $nameValue['value'];
			}
		}

		$name = implode(", ", $names);
		$app->getPathway()->addItem($name, '');
	}

	/**
	 * Modifies the document by adding script and style declarations.
	 *
	 * @return void modifies the document
	 */
	private function modifyDocument()
	{
		JHtml::_('bootstrap.framework');

		$document = JFactory::getDocument();
		$document->addStyleSheet($this->baseurl . '/media/com_thm_groups/css/advanced.css');

		// Truncate Long Info Text
		if ($this->suppressText)
		{
			$hide = JText::_('COM_THM_GROUPS_ACTION_HIDE');
			$read = JText::_('COM_THM_GROUPS_ACTION_DISPLAY');
			$document->addScriptOptions('com_thm_groups', array('hide' => $hide, 'read' => $read));
			require_once JPATH_ROOT . "/media/com_thm_groups/js/toggle_text.js.php";
		}
	}

	private function getActionContainer($profileID, $lastName)
	{
		$container  = '';
		$canEditOwn = (bool) JComponentHelper::getParams('com_thm_groups')->get('editownprofile', 0);
		$ownProfile = JFactory::getUser()->id == $profileID;
		$canEdit    = (($canEditOwn AND $ownProfile) OR $this->isAdmin);

		if ($canEdit)
		{
			$container .= '<div class="action-container">';

			$linkTitle = JText::_('COM_THM_GROUPS_EDIT');
			$data      = ['option'    => 'com_thm_groups',
			              'view'      => 'profile_edit',
			              'groupID'   => $this->groupID,
			              'profileID' => $profileID,
			              'name'      => $lastName,
			              'Itemid'    => $this->menuID
			];

			$link      = 'index.php?' . http_build_query($data);
			$container .= JHtml::link(JRoute::_($link), '<span class="icon-edit"></span>', $linkTitle);
			$container .= "</div>";
			$container .= '<div class="clearFix"></div>';
		}

		return $container;
	}

	/**
	 * Creates the container for the attribute
	 *
	 * @param array  $attribute the profile attribute being iterated
	 * @param string $surname   the surname of the profile being iterated
	 *
	 * @return string the HTML for the value container
	 */
	private function getAttributeContainer($attribute, $surname)
	{
		$container = '';

		$params     = empty($attribute['params']) ? [] : $attribute['params'];
		$dynOptions = empty($attribute['dynOptions']) ? [] : $attribute['dynOptions'];
		$options    = empty($attribute['options']) ? [] : $attribute['options'];

		$attribute['params'] = array_merge($params, $dynOptions, $options);
		$label               = '';

		if (($attribute['type'] == 'PICTURE'))
		{
			$container .= '<div class="attribute-picture">';
		}
		else
		{
			if (($attribute['type'] == 'TEXTFIELD'))
			{
				$container .= '<div class="attribute-textfield">';
			}
			elseif (!empty($attribute['params']['wrap']))
			{
				$container .= '<div class="attribute-wrap">';
			}
			else
			{
				$container .= '<div class="attribute-inline">';
			}

			$label .= $this->getLabelContainer($attribute);
		}

		$container .= $label;

		// Empty values or undesired
		if (empty($label))
		{
			$labeled = 'none';
		}

		// The icon label consists solely of tags
		elseif (empty(strip_tags($label)))
		{
			$labeled = 'icon';
		}
		else
		{
			$visibleLength = strlen(strip_tags($label));
			$labeled       = $visibleLength > 10 ? 'label-long' : 'label';
		}

		$container .= $this->getValueContainer($attribute, $surname, $labeled);

		$container .= "</div>";

		return $container;
	}

	/**
	 * Creates the container for the attribute label
	 *
	 * @param array $attribute the profile attribute being iterated
	 *
	 * @return string the HTML for the label container
	 */
	private function getLabelContainer($attribute)
	{
		$showIcon  = (!empty($attribute['params']['showIcon'] AND !empty($attribute['params']['icon'])));
		$text      = empty($attribute['name']) ? '' : $attribute['name'];
		$showLabel = (!empty($attribute['params']['showLabel']) AND !empty($text));
		$label     = '';

		if ($showIcon OR $showLabel)
		{
			$long  = (!$showIcon AND strlen($text) > 10);
			$label .= $long ? '<div class="attribute-label attribute-label-long">' : '<div class="attribute-label">';

			if ($showIcon)
			{
				$label .= '<span class="' . $attribute['params']['icon'] . '" title="' . $text . '"></span>';
			}
			elseif ($showLabel)
			{
				$label .= JText::_($attribute['name']);
			}

			$label .= '</div>';
		}

		return $label;
	}

	/**
	 * Creates the HTML for the name container
	 *
	 * @param array $attributes the attributes of the profile
	 *
	 * @return string the HTML string containing name information
	 */
	private function getNameContainer($attributes)
	{
		$text = '';

		if (!empty($attributes[1]['value']))
		{
			$text .= '<span class="attribute-name">' . $attributes[1]['value'] . '</span>';
		}

		$text .= '<span class="attribute-name">' . $attributes[2]['value'] . '</span>';

		return '<div class="attribute-inline">' . JHtml::link($attributes['URL'], $text) . '</div>';
	}

	/**
	 * Creates a HTML container with profile information
	 *
	 * @param int   $profileID  the profile's id
	 * @param array $attributes the profile's attributes
	 * @param bool  $half       whether or not the profile should only take half the row width
	 * @param int   $groupID    the id of the profile's group
	 *
	 * @return string the HTML of the profile container
	 */
	public function getProfileContainer($profileID, $attributes, $half, $groupID = null)
	{
		$container = '';

		// Open Column Wrapper Tag - Only for float-attribute, now is easy to work with width:100%
		if ($half)
		{
			$container .= '<div class="profile-container half">';
		}
		else
		{
			$container .= '<div class="profile-container">';
		}

		$lastName = $attributes[2]['value'];

		$container .= $this->getActionContainer($profileID, $lastName);

		$attributeContainers   = [];
		$attributeContainers[] = $this->getNameContainer($attributes);

		$titleContainer = $this->getTitleContainer($attributes);

		if (!empty($titleContainer))
		{
			$attributeContainers[] = $titleContainer;
		}

		if ($this->showRoles AND !empty($attributes['roles']) AND !empty($this->sort))
		{
			$attributeContainers[] = '<div class="attribute-wrap attribute-roles">' . $attributes['roles'] . '</div>';
		}

		foreach ($attributes as $attributeID => $attribute)
		{
			// These were already taken care of in the name/title containers
			$processed = in_array($attributeID, [1, 2, 5, 7]);

			// Special indexes and attributes with no saved value are irrelevant
			$irrelevant = empty($attribute['value']);

			if ($processed OR $irrelevant)
			{
				continue;
			}

			$attributeContainer = $this->getAttributeContainer($attribute, $lastName);

			if (($attribute['type'] == 'PICTURE'))
			{
				array_unshift($attributeContainers, $attributeContainer);
			}
			else
			{
				$attributeContainers[] = $attributeContainer;
			}
		}

		$container .= implode('', $attributeContainers);

		$container .= '<div class="clearFix"></div>';
		$container .= "</div>";

		return $container;
	}

	/**
	 * Creates the HTML for the title container
	 *
	 * @param array $attributes the attributes of the profile
	 *
	 * @return string the HTML string containing title information
	 */
	private function getTitleContainer($attributes)
	{
		$text = '';

		$title = empty($attributes[5]['value']) ? '' : nl2br(htmlspecialchars_decode($attributes[5]['value']));
		$title .= empty($attributes[7]['value']) ? '' : ', ' . nl2br(htmlspecialchars_decode($attributes[7]['value']));

		if (empty($title))
		{
			return $text;
		}

		$text .= '<span class="attribute-title">' . $title . '</span>';

		return '<div class="attribute-inline">' . JHtml::link($attributes['URL'], $text) . '</div>';
	}

	/**
	 * Creates the container for the attribute value
	 *
	 * @param array  $attribute the profile attribute being iterated
	 * @param string $surname   the surname of the profile being iterated
	 * @param string $labeled   how the attribute will be labeled. determines additional classes for style references.
	 *
	 * @return string the HTML for the value container
	 */
	private function getValueContainer($attribute, $surname, $labeled)
	{
		switch ($attribute['type'])
		{
			case "Email":

				$value = '<a href="mailto:' . $attribute['value'] . '">';
				$value .= JHTML::_('email.cloak', $attribute['value']) . '</a>';

				break;

			case "LINK":

				$value = "<a href='" . htmlspecialchars_decode($attribute['value']) . "'>";
				$value .= htmlspecialchars_decode($attribute['value']) . "</a>";

				break;

			case "PICTURE":

				$position     = explode('images/', $attribute['params']['path'], 2);
				$relativePath = 'images/' . $position[1];

				$value = JHTML::image(
					JURI::root() . $relativePath . $attribute['value'],
					$surname,
					array('class' => 'thm_groups_profile_container_profile_image')
				);

				break;

			case "TEXTFIELD":

				$text = trim(htmlspecialchars_decode($attribute['value']));

				// Normalize new lines
				if (stripos($text, '<li>') === false && stripos($text, '<table') === false)
				{
					$text = nl2br($text);
				}

				// The closing div for the toggled container is added later
				if ($this->suppressText AND strlen(strip_tags($text)) > 50)
				{
					$value = '<span class="toggled-text-link">' . JText::_('COM_THM_GROUPS_ACTION_DISPLAY') . '</span></div>';
					$value .= '<div class="toggled-text-container" style="display:none;">' . $text;
				}
				else
				{
					$value = $text;
				}

				break;

			case "TEXT":
			default:

				$value = nl2br(htmlspecialchars_decode($attribute['value']));

				break;
		}

		$classes = ['attribute-value'];

		$visibleLength = strlen(strip_tags($value));

		// Used to force width on long texts
		if ($visibleLength > 30)
		{

			if ($labeled == 'icon')
			{
				$classes[] = 'attribute-iconed';
			}
			elseif ($labeled == 'label')
			{
				$classes[] = 'attribute-labeled';
			}

		}

		$html = '<div class="' . implode(' ', $classes) . '">';
		$html .= $value;
		$html .= '</div>';

		return $html;
	}
}
