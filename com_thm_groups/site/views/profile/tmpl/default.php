<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

$profile = $this->profile;
?>

<div class="item-page template-<?php echo $this->templateName; ?>">
	<meta content="de-DE" itemprop="inLanguage">
	<div class="page-header">
		<h2><?php echo THM_GroupsHelperProfile::getDisplayNameWithTitle($this->profileID); ?></h2>
	</div>
	<div class="toolbar">
		<?php echo $this->getEditLink('class="btn btn-toolbar-thm"'); ?>
	</div>
	<div class="profile">
		<?php
		foreach ($profile as $name => $attribute)
		{
			if (empty($attribute['publish']) OR !isset($attribute['value']))
			{
				continue;
			}

			$value = trim($attribute['value']);
			if (empty($value))
			{
				continue;
			}

			$functionName = 'get' . $attribute['type'];
			echo $this->$functionName($name, $attribute);
		}
		?>
	</div>
	<?php
	if (JComponentHelper::getParams('com_thm_groups')->get('backButtonForProfile') == 1)
	{
		echo '<input type="button" class="btn btn-thm" value="' . JText::_("COM_THM_GROUPS_BACK_BUTTON") . '" onclick="window.history.back()">';
	}
	?>
</div>




