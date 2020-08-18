<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once HELPERS . 'profiles.php';

use \JFactory as Factory;

/**
 * THMGroupsViewProfile class for component com_thm_groups
 */
class THM_GroupsViewProfile extends JViewLegacy
{
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
		$profileID = JFactory::getApplication()->input->getint('profileID', 0);
		$published = empty($profileID) ? false : THM_GroupsHelperProfiles::isPublished($profileID);

		if (!$published)
		{
			$exc = new Exception(JText::_('COM_THM_GROUPS_PROFILE_NOT_FOUND'), '404');
			JErrorPage::render($exc);
		}

		$addressIdentifiers   = ['ADDRESS', 'ADRESSE', 'ANSCHRIFT'];
		$address              = '';
		$cellIdentifiers      = ['CELL PHONE', 'HANDY', 'MOBILE'];
		$cell                 = '';
		$fax                  = '';
		$fixedAttributes      = [EMAIL_ATTRIBUTE, FORENAME, POSTTITLE, SURNAME, TITLE];
		$homepage             = '';
		$hpIdentifiers        = ['HOMEPAGE', 'WEBPAGE', 'WEBSEITE', 'WEB'];
		$image                = '';
		$officeIdentifiers    = ['BÜRO', 'OFFICE', 'RAUM'];
		$office               = '';
		$profile              = THM_GroupsHelperProfiles::getRawProfile($profileID);
		$telephoneIdentifiers = ['TELEFON', 'TELEPHONE'];
		$telephone            = '';
		foreach ($profile as $attributeID => $attribute)
		{

			$fieldID = $attribute['fieldID'];

			// Fixed attributes can be accessed directly and file attributes are irrelevant
			if (in_array($attributeID, $fixedAttributes) or $fieldID == FILE)
			{
				continue;
			}

			$ucLabel = strtoupper($attribute['label']);

			switch ($fieldID)
			{
				case EDITOR:
					if (in_array($ucLabel, $addressIdentifiers) and empty($address))
					{
						$address = $this->cleanAddress($attribute['value']);
					}
					break;
				case TELEPHONE:
					if (in_array($ucLabel, $cellIdentifiers) and empty($cell))
					{
						$cell = $attribute['value'];
					}

					if ($ucLabel === 'FAX' and empty($fax))
					{
						$fax = $attribute['value'];
					}

					if (in_array($ucLabel, $telephoneIdentifiers) and empty($telephone))
					{
						$telephone = $attribute['value'];
					}
					break;
				case TEXT:
					if (in_array($ucLabel, $officeIdentifiers) and empty($office))
					{
						$office = $attribute['value'];
						continue 2;
					}
					break;
				case URL:
					if (in_array($ucLabel, $hpIdentifiers) and empty($homepage))
					{
						$homepage = $attribute['value'];
						continue 2;
					}
					break;
			}
		}

		$addressText = (!empty($address) and !empty($office)) ? "$office\\n$address" : "$office$address";
		$cardName    = THM_GroupsHelperProfiles::getDisplayName($profileID);
		$email       = (!empty($profile[EMAIL_ATTRIBUTE]) and !empty($profile[EMAIL_ATTRIBUTE]['value'])) ?
			$profile[EMAIL_ATTRIBUTE]['value'] : '';
		$forenames   = (!empty($profile[FORENAME]) and !empty($profile[EMAIL_ATTRIBUTE]['value'])) ?
			explode(' ', $profile[FORENAME]['value']) : [];
		$forename    = count($forenames) ? array_shift($forenames) : '';
		$middleNames = implode(' ', $forenames);
		$title       = empty($profile[TITLE]['value']) ? '' : $profile[TITLE]['value'];
		$title       .= empty($profile[POSTTITLE]['value']) ? '' : $profile[POSTTITLE]['value'];

		Factory::getDocument()->setMimeEncoding('text/directory', true);

		$headerValue = 'attachment; filename="' . $cardName . '.vcf"';
		Factory::getApplication()->setHeader('Content-disposition', $headerValue, true);

		$vcard   = [];
		$vcard[] .= 'BEGIN:VCARD';
		$vcard[] .= 'VERSION:3.0';
		$vcard[] = "N:{$profile[SURNAME]['value']};$forename;$middleNames";
		$vcard[] = "FN:$cardName";
		$vcard[] = "TITLE:$title";
		$vcard[] = "PHOTO;$image";
		$vcard[] = "TEL;TYPE=WORK,VOICE:$telephone";
		$vcard[] = "TEL;TYPE=WORK,FAX:$fax";
		$vcard[] = "TEL;TYPE=WORK,MOBILE:$cell";
		$vcard[] = "ADR;TYPE=WORK:;;$addressText;;;;";
		$vcard[] = "LABEL;TYPE=WORK:$addressText";
		$vcard[] = "EMAIL;TYPE=PREF,INTERNET:$email";
		$vcard[] = "URL:$homepage";
		$vcard[] = 'REV:' . date('c') . 'Z';
		$vcard[] = 'END:VCARD';

		echo implode("\n", $vcard);
	}

	/**
	 * Cleans the address of html and superfluous white space.
	 *
	 * @param   string  $address  the address to be cleaned
	 *
	 * @return string the cleaned address
	 */
	private function cleanAddress($address)
	{
		$address      = str_replace(['<br>', '<br/>', '<br />', '</p><p>'], "XXX", $address);
		$address      = strip_tags($address);
		$address      = preg_replace("/\n/", "XXX", $address);
		$address      = preg_replace("/ +/", " ", $address);
		$addressParts = explode("XXX", $address);
		foreach ($addressParts as &$addressPart)
		{
			$addressPart = trim($addressPart);
		}

		return implode("\\n", array_filter($addressParts));
	}
}
