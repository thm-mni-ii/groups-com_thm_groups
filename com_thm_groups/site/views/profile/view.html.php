<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @description THMGroupsViewProfile file from com_thm_groups
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
	 * Creates a container for the profile attribute
	 *
	 * @param   string $name      the name of the profile attribute
	 * @param   array  $attribute the profile attribute
	 *
	 * @return  array  contains the HTML for the begin and end of the attribute container
	 */
	private function getContainer($name, $attribute)
	{
		$safeName    = JFilterOutput::stringURLSafe($name);
		$paramsExist = !empty($attribute['params']);
		$isDiv       = ($paramsExist AND !empty($attribute['params']['wrap']));

		if ($isDiv)
		{
			$containerClass      = "field-container $safeName-container";
			$labelContainerClass = "field-container-label $safeName-label";
			$valueClass          = "field-container-value $safeName-value";
		}
		else
		{
			$containerClass      = "field-row $safeName-container";
			$labelContainerClass = "field-row-label $safeName-label";
			$valueClass          = "field-row-value $safeName-value";
		}

		$start = '';
		$start .= '<div class="' . $containerClass . '">';

		$start = $this->getIconLabelOutput($attribute, $start, $name, $labelContainerClass);

		$start .= '<div class="' . $valueClass . '">';

		$end = '</div></div>';

		return ['start' => $start, 'end' => $end];
	}

	/**
	 * Creates a profile date attribute
	 *
	 * @param   string $name      the name of the profile attribute
	 * @param   array  $attribute the profile attribute
	 *
	 * @return array
	 */
	public function getDATE($name, $attribute)
	{
		return $this->getTEXT($name, $attribute);
	}

	/**
	 * Creates a profile link attribute
	 *
	 * @param   string $name      the name of the profile attribute
	 * @param   array  $attribute the profile attribute
	 *
	 * @return array
	 */
	public function getLINK($name, $attribute)
	{
		$container = $this->getContainer($name, $attribute);
		$value     = "<a href='" . htmlspecialchars_decode($attribute['value']) . "'>";
		$value     .= htmlspecialchars_decode($attribute['value']) . "</a>";

		return $container['start'] . $value . $container['end'];
	}

	/**
	 * Creates a profile number attribute
	 *
	 * @param   string $name      the name of the profile attribute
	 * @param   array  $attribute the profile attribute
	 *
	 * @return array
	 */
	public function getNUMBER($name, $attribute)
	{
		return $this->getTEXT($name, $attribute);
	}

	/**
	 * Creates the HTML for an attribute of the type 'PICTURE'
	 *
	 * @param   string $name      the name of the profile attribute
	 * @param   array  $attribute the attribute being iterated
	 *
	 * @return  string  the HTML for the image to be displayed
	 */
	public function getPICTURE($name, $attribute)
	{
		$container = $this->getContainer($name, $attribute);
		$value     = '';
		$hasImage  = (!empty($attribute['value']));
		if ($hasImage)
		{
			$imgOptions = $attribute['options'];
			$path       = JUri::base() . $imgOptions['path'] . '/' . $attribute['value'];
			$value      .= JHtml::image($path, 'Profilbild');
		}

		return $container['start'] . $value . $container['end'];
	}

	/**
	 * Creates a profile link attribute
	 *
	 * @param   string $name      the name of the profile attribute
	 * @param   array  $attribute the profile attribute
	 *
	 * @return array
	 */
	public function getTEXT($name, $attribute)
	{
		$container = $this->getContainer($name, $attribute);
		switch (strtolower($attribute['dyntype']))
		{
			case 'email':
				$emails = explode('|', $attribute['value']);


				if (count($emails) === 1)
				{
					$value = JHtml::_('email.cloak', $attribute['value']);
				}
				else
				{
					$value = '<ul>';

					foreach ($emails as $email)
					{
						$value .= '<li>' . JHtml::_('email.cloak', $email) . '</li>';
					}

					$value .= '</ul>';
				}

				break;

			case 'telephone':
			case 'fax':
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

			default:
				$value = $attribute['value'];
				break;
		}

		return $container['start'] . $value . $container['end'];
	}

	/**
	 * Creates a profile link attribute
	 *
	 * @param   string $name      the name of the profile attribute
	 * @param   array  $attribute the profile attribute
	 *
	 * @return array
	 */
	public function getTEXTFIELD($name, $attribute)
	{
		$container = $this->getContainer($name, $attribute);
		$value     = htmlspecialchars_decode($attribute['value']);

		return $container['start'] . $value . $container['end'];
	}

	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$this->model     = $this->getModel();
		$this->profileID = $this->model->profileID;
		$this->groupID   = $this->model->groupID;
		$this->canEdit   = THM_GroupsHelperComponent::canEditProfile($this->profileID, $this->groupID);
		$this->menuID    = JFactory::getApplication()->input->get('Itemid', 0);
		$this->profile   = $this->get('Item');

		$templateName       = THM_GroupsHelperProfile::getTemplateNameByGroupID($this->groupID);
		$this->templateName = JFilterOutput::stringURLSafe($templateName);

		// Adds the user name to the breadcrumb
		JFactory::getApplication()->getPathway()->addItem(THM_GroupsHelperProfile::getDisplayName($this->profileID), '');

		$this->modifyDocument();
		parent::display($tpl);
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
			$path     .= "&groupID=$this->groupID&userID=$this->profileID&name=$lastName&Itemid=$this->menuID";
			$url      = JRoute::_($path);
			$text     = '<span class="icon-edit"></span> ' . JText::_('COM_THM_GROUPS_EDIT');
			$editLink .= JHtml::_('link', $url, $text, $attributes);
		}

		return $editLink;
	}

	/**
	 * Gets a link to the previous static content or webpage
	 *
	 * @params   mixed  $attributes  An associative array (or simple string) of attributes to add
	 *
	 * @return  string  the Link HTML markup
	 */
	public function getBackLink($attributes = null)
	{
		$defaultURL  = 'document.referrer';
		$defaultText = '<span class="icon-undo"></span> ' . JText::_('COM_THM_GROUPS_PROFILE_BACK');
		$defaultLink = JHtml::_('link', $defaultURL, $defaultText, $attributes);

		$menu = JFactory::getApplication()->getMenu()->getItem($this->menuID);
		if (empty($menu))
		{
			return $defaultLink;
		}

		$notGroupsComponent = ($menu->type != 'component' OR $menu->component != 'com_thm_groups');
		if ($notGroupsComponent)
		{
			return $defaultLink;
		}

		$url  = $menu->link . '&Itemid=' . $this->menuID;
		$text = '<span class="icon-list"></span> ' . JText::_('COM_THM_GROUPS_PROFILE_BACK_TO_LIST');

		return JHtml::_('link', $url, $text, $attributes);
	}

	/**
	 * Adds css and javascript files to the document
	 *
	 * @return  void  modifies the document
	 */
	private function modifyDocument()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet('media/com_thm_groups/css/profile_item.css');
		JHtml::_('bootstrap.framework');
		JHtml::_('behavior.modal');
		JHtml::_('behavior.modal', 'a.modal-button');
	}

	/**
	 * Creates the name to be displayed
	 *
	 * @return  string  the profile name
	 */
	public function getDisplayName()
	{
		return THM_GroupsHelperProfile::getDisplayNameWithTitle($this->profileID);
	}

	/**
	 * Checks icon and value and returns one of them
	 *
	 * @param   array  $attribute           Array with options for attribute
	 * @param   string $output              String which contains icon or label
	 * @param   string $name                Some necessary shit
	 * @param   string $labelContainerClass Other necessary shit
	 *
	 * @return  string
	 */
	private function getIconLabelOutput($attribute, $output, $name, $labelContainerClass)
	{
		$paramsExist = !empty($attribute['params']);
		$showIcon    = (!$paramsExist OR !empty($attribute['params']['showIcon']));
		$showLabel   = (!$paramsExist OR !empty($attribute['params']['showLabel']));
		$iconName    = isset($attribute['options']['icon']) ? $attribute['options']['icon'] : '';

		if ($showIcon AND !empty($iconName))
		{
			$output .= "<span class='$iconName'></span>";
		}
		elseif ($showLabel)
		{
			$output .= '<div class="' . $labelContainerClass . '"><span>' . $name . '</span></div>';

			return $output;
		}

		return $output;
	}
}
