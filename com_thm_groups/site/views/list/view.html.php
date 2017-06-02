<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @description THMGroupsViewList file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Unfortunately these have nothing to do with the attribute IDS
define('PRETITLE', 0);
define('FORENAME', 1);
define('SURNAME', 2);
define('POSTTITLE', 3);

/**
 * THMGroupsViewList class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.thm.de
 */
class THM_GroupsViewList extends JViewLegacy
{
	public $model = null;

	public $params = array();

	public $groupID = 0;

	public $profileLink = '';

	public $title = '';

	public $profiles = array();

	public $letterProfiles = array();

	/**
	 * Creates a link to the parametrized profile target using the profile name
	 *
	 * @param   object $profile object with user profile information
	 *
	 * @return  string  the HTML output for the profile link
	 */
	public function getProfileLink($profile)
	{
		$attributeOrder = empty($this->params['orderingAttributes']) ?
			array() : explode(",", $this->params['orderingAttributes']);
		Joomla\Utilities\ArrayHelper::toInteger($attributeOrder);
		$attributeOrder = array_flip($attributeOrder);
		ksort($attributeOrder);

		$attributes = $this->params['showstructure'];
		Joomla\Utilities\ArrayHelper::toInteger($attributes);

		foreach ($attributeOrder AS $key => $value)
		{
			if (!in_array($value, $attributes))
			{
				unset($attributeOrder[$key]);
			}
		}

		$menuID     = JFactory::getApplication()->input->get('Itemid', 0);
		$linkTarget = "index.php?option=com_thm_groups&Itemid=$menuID";

		// When a user is clicked should parameters be passed to the profile module or should the profile view open
		switch ($this->params['linkTarget'])
		{
			case "profile":
				$linkTarget .= '&view=profile';
				break;
			case "module":
			default:
				$linkTarget .= '&view=list';
		}

		$displayedText = '';

		// Write name
		foreach ($attributeOrder as $index => $attribute)
		{
			switch ($attribute)
			{
				case PRETITLE:
					$displayedText .= $profile->title . ' ';
					break;
				case FORENAME:
					$displayedText .= $profile->forename . ' ';
					break;
				case 2:
					$forenameIndex  = array_search(FORENAME, $attributeOrder);
					$displayedText  .= $profile->surname;
					$naturalOrder   = $index > $forenameIndex;
					$forenameExists = !empty($profile->forename);
					$displayedText  .= ($naturalOrder OR !$forenameExists) ? ' ' : ', ';
					break;
				case 3:
					$displayedText .= $profile->posttitle . ' ';
					break;
			}
		}

		$url = "$linkTarget&userID=$profile->id&groupID=$this->groupID&name=" . trim($profile->surname);

		return JHtml::link(JRoute::_($url), $displayedText);
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
		$app = JFactory::getApplication();

		// Calling this first ensures helpers are loaded
		$this->model = $this->getModel();

		$this->params  = $app->getParams();
		$this->groupID = $this->model->getGroupNumber();

		$menuID            = $app->input->get('Itemid', 0);
		$this->profileLink = "index.php?option=com_thm_groups&Itemid=$menuID";

		// When a user is clicked should parameters be passed to the profile module or should the profile view open
		switch ($this->params['linkTarget'])
		{
			case "profile":
				$this->profileLink .= '&view=profile';
				break;
			case "module":
			default:
				$this->profileLink .= '&view=list';
		}

		// Sizing attributes
		$this->totalUsers    = THM_GroupsHelperGroup::getUserCount($this->groupID);
		$columns             = $this->params->get('columnCount', 4);
		$this->maxColumnSize = ceil(($this->totalUsers) / $columns);

		// Title handling
		$heading     = $this->params->get('page_heading', '');
		$title       = $this->params->get('page_title', JText::_('COM_THM_GROUPS_LIST_TITLE'));
		$this->title = empty($heading) ? $title : $heading;

		$this->profiles = $this->model->getProfilesByLetter($this->groupID);
		$this->setPathway();
		$this->modifyDocument();
		parent::display($tpl);
	}

	/**
	 * Alters the breadcrumbs to reflect user profile selection
	 *
	 * @return  void
	 */
	private function setPathway()
	{
		$app    = JFactory::getApplication();
		$userID = $app->input->getInt('userID', 0);

		if (empty($userID))
		{
			return;
		}

		$pathway = $app->getPathway();
		$name    = THM_GroupsHelperProfile::getDisplayName($userID);
		$pathway->addItem($name, '');
	}

	/**
	 * Adds css and javascript files to the document
	 *
	 * @return  void  modifies the document
	 */
	private function modifyDocument()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet('media/com_thm_groups/css/list.css');
		JHtml::_('bootstrap.framework');
	}

	/**
	 * Translates various umlaut encodings to the corresponding HTML Entities
	 *
	 * @param   string $text the text to be processed
	 *
	 * @return  string  the text with HTML umlaut encodings
	 */
	public function umlaut2HTML($text)
	{
		$text = str_replace("Ãƒâ€“", "&Ouml;", $text);
		$text = str_replace("ÃƒÂ¶", "&ouml;", $text);
		$text = str_replace("Ãƒâ€ž", "&Auml;", $text);
		$text = str_replace("ÃƒÂ¤", "&auml;", $text);
		$text = str_replace("ÃƒÅ“", "&Uuml;", $text);
		$text = str_replace("ÃƒÂ¼", "&uuml;", $text);
		$text = str_replace("ÃƒÆ’Ã‚Â¶", "&Ouml;", $text);
		$text = str_replace("ÃƒÆ’Ã‚Â¶", "&ouml;", $text);
		$text = str_replace("ÃƒÆ’Ã‚Â¤", "&auml;", $text);
		$text = str_replace("ÃƒÆ’Ã‚Â¤", "&Auml;", $text);
		$text = str_replace("ÃƒÆ’Ã‚Â¼", "&uuml;", $text);
		$text = str_replace("ÃƒÆ’Ã‚Â¼", "&Uuml;", $text);

		return $text;
	}
}
