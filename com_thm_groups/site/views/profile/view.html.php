<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';
require_once JPATH_ROOT . "/media/com_thm_groups/helpers/profile.php";
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/template.php';

/**
 * THMGroupsViewProfile class for component com_thm_groups
 *
 * @category    Joomla.Component.Site
 * @package     thm_Groups
 * @subpackage  com_thm_groups.site
 */
class THM_GroupsViewProfile extends JViewLegacy
{
	public $profileID;

	protected $links;

	public $templateName;

	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$this->model = $this->getModel();

		$this->groupID    = $this->model->groupID;
		$this->menuID     = JFactory::getApplication()->input->get('Itemid', 0);
		$this->profile    = $this->model->profile;
		$this->profileID  = $this->model->profileID;
		$this->templateID = $this->model->templateID;

		$this->canEdit = THM_GroupsHelperComponent::canEditProfile($this->profileID);

		$this->templateName = JFilterOutput::stringURLSafe(THM_GroupsHelperTemplate::getName($this->templateID));

		// Adds the user name to the breadcrumb
		JFactory::getApplication()->getPathway()->addItem(THM_GroupsHelperProfile::getDisplayName($this->profileID), '');

		$this->modifyDocument();
		parent::display($tpl);
	}

	/**
	 * Renders the attributes of a profile
	 *
	 * @return void renders to the view
	 */
	public function renderAttributes()
	{
		$attributes          = '';
		$attributeContainers = [];
		$surname             = $this->profile[2]['value'];

		foreach ($this->profile as $attribute)
		{
			// These were already taken care of in the name/title containers
			$processed = in_array($attribute['structid'], [1, 2, 5, 7]);

			// Special indexes and attributes with no saved value are irrelevant
			$irrelevant = (empty($attribute['value']) OR empty(trim($attribute['value'])));

			if ($processed OR $irrelevant)
			{
				continue;
			}

			$attributeContainer = THM_GroupsHelperProfile::getAttributeContainer($attribute, $surname);

			if (($attribute['type'] == 'PICTURE'))
			{
				array_unshift($attributeContainers, $attributeContainer);
			}
			else
			{
				$attributeContainers[] = $attributeContainer;
			}
		}

		$attributes .= implode('', $attributeContainers);

		$attributes .= '<div class="clearFix"></div>';

		echo $attributes;
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
		$text        = empty($attribute['name']) ? '' : $attribute['name'];
		$isTextField = $attribute['type'] == 'TEXTFIELD';

		$showIconConfig = (!empty($attribute['params']['showIcon'] AND !empty($attribute['params']['icon'])));
		$showLabel      = (!empty($attribute['params']['showLabel']) AND !empty($text));
		$showIcon       = $isTextField ? ($showIconConfig AND !$showLabel) : $showIconConfig;
		$label          = '';

		if ($showIcon OR $showLabel)
		{
			$label .= '<div class="attribute-label">';

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
		switch (strtolower($attribute['dyntype']))
		{
			case "email":

				$emails = explode('|', $attribute['value']);

				if (count($emails) === 1)
				{
					$value = '<a href="mailto:' . $attribute['value'] . '">';
					$value .= JHTML::_('email.cloak', $attribute['value']) . '</a>';
				}
				else
				{
					$value = '<ul>';

					foreach ($emails as $email)
					{
						$value .= '<li><a href="mailto:' . $email . '">';
						$value .= JHTML::_('email.cloak', $email) . '</a></li>';
					}

					$value .= '</ul>';
				}

				break;

			case 'fax':
			case 'telephone':

				$numbers = explode('|', $attribute['value']);

				if (count($numbers) === 1)
				{
					$value = $attribute['value'];
				}
				else
				{
					$value = '<ul>';

					foreach ($numbers as $number)
					{
						$value .= "<li>$number</li>";
					}

					$value = '</ul>';
				}

				break;

			case "link":

				$value = "<a href='" . htmlspecialchars_decode($attribute['value']) . "'>";
				$value .= htmlspecialchars_decode($attribute['value']) . "</a>";

				break;

			case "picture":

				$position     = explode('images/', $attribute['params']['path'], 2);
				$relativePath = 'images/' . $position[1];

				$value = JHTML::image(
					JURI::root() . $relativePath . $attribute['value'],
					$surname,
					array('class' => 'profile-picture')
				);

				break;

			case "textfield":

				$text = trim(htmlspecialchars_decode($attribute['value']));

				// Normalize new lines
				if (stripos($text, '<li>') === false && stripos($text, '<table') === false)
				{
					$text = nl2br($text);
				}

				$value = $text;

				break;

			case "text":
			default:

				$value = nl2br(htmlspecialchars_decode($attribute['value']));

				break;
		}

		$html = '<div class="attribute-value">';
		$html .= $value;
		$html .= '</div>';

		return $html;
	}

	/**
	 * Gets a link to the profile edit view
	 *
	 * @params   mixed $attributes An associative array (or simple string) of attributes to add
	 *
	 * @return  string  the Link HTML markup
	 */
	public function getEditLink($attributes = null)
	{
		$editLink = "";

		if ($this->canEdit)
		{
			$fullName  = JFactory::getUser($this->profileID)->get('name');
			$nameArray = explode(" ", $fullName);
			$lastName  = array_key_exists(1, $nameArray) ? $nameArray[1] : "";

			$lastName = trim($lastName);
			$path     = "index.php?option=com_thm_groups&view=profile_edit";
			$path     .= "&groupID=$this->groupID&profileID=$this->profileID&name=$lastName&Itemid=$this->menuID";
			$url      = JRoute::_($path);
			$text     = '<span class="icon-edit"></span> ' . JText::_('COM_THM_GROUPS_EDIT');
			$editLink .= JHtml::_('link', $url, $text, $attributes);
		}

		return $editLink;
	}

	/**
	 * Redirects back to the previous
	 *
	 * @return  string  the Link HTML markup
	 */
	public function getBackLink()
	{
		if (JComponentHelper::getParams('com_thm_groups')->get('backButtonForProfile') == 1)
		{
			$text       = '<span class="icon-arrow-left-22"></span> ' . JText::_("COM_THM_GROUPS_BACK_BUTTON");
			$attributes = ['class' => 'btn', 'onclick' => 'window.history.back()'];

			return JHtml::link('#', $text, $attributes);
		}

		return '';
	}

	/**
	 * Adds css and javascript files to the document
	 *
	 * @return  void  modifies the document
	 */
	private function modifyDocument()
	{
		JFactory::getDocument()->addStyleSheet('media/com_thm_groups/css/profile_item.css');
		JHtml::_('bootstrap.framework');
	}
}
