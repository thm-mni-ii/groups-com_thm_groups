<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
$params = $this->params;

$input       = JFactory::getApplication()->input;
$shownLetter = $input->get('letter');
$menuID      = $input->get('Itemid');
$baseURL     = "index.php?option=com_thm_groups&Itemid=$menuID";

echo '<div class="alphabet-container"><ul class="alphabet-list">';

$alphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
                  'U', 'V', 'W', 'X', 'Y', 'Z');

foreach ($alphabet as $letter)
{
	if (array_key_exists($letter, $this->profiles))
	{
		if (empty($shownLetter))
		{
			$shownLetter = $letter;
		}

		echo (strtoupper($shownLetter) == $letter) ? '<li class="letter-active">' : '<li>';

		$attributes  = ['class' => ['letter-enabled']];

		$url = "$baseURL&letter=$letter";
		echo JHtml::_('link', $url, $letter, $attributes);
	}
	else
	{
		echo '<li><span class="letter-disabled">' . $letter . '</span>';
	}

	echo '</li>';
}
echo '</ul></div>';

$shownLetter = strtoupper($shownLetter);

$this->letterProfiles = array();
if (array_key_exists($shownLetter, $this->profiles))
{
	$this->letterProfiles[$shownLetter] = $this->profiles[$shownLetter];
}

echo $this->loadTemplate('list');

