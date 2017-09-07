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
?>
<div itemtype="http://schema.org/Article" itemscope="" class="thm_groups-list">
	<meta content="de-DE" itemprop="inLanguage">
	<?php if ($this->params->get('show_title')) : ?>
		<div class="page-header">
			<h2 itemprop="headline">
				<?php echo $this->escape($this->title); ?>
			</h2>
		</div>
	<?php endif; ?>
	<div itemprop="articleBody" class="list-container">
		<?php
		if ($this->params->get('byLetter') == 0)
		{
			echo $this->loadTemplate('list');

		}
		else
		{
			echo $this->loadTemplate('letter');
		}
		?>
	</div>
</div>
