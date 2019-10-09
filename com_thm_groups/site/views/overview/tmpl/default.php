<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

$tolerance            = $this->columnCount;
$currentProfilesCount = 0;
$currentRowCount      = 1;
$totalLettersOutput   = 0;
$totalRowsOutput      = 0;

echo $this->getHeaderImage();
?>
<div class="thm_groups-overview">
	<?php if ($this->params->get('show_page_heading') or empty($this->params->get('groupID'))) : ?>
        <div class="page-header">
            <h2 itemprop="headline">
				<?php echo $this->escape($this->title); ?>
            </h2>
        </div>
	<?php endif; ?>
    <div class="overview-container">
        <div class="profiles-container">
			<?php
			foreach ($this->profiles as $letter => $profiles)
			{
				foreach ($profiles as $profile)
				{
					if ($currentRowCount == 1)
					{
						echo '<div class="ym-g33 ym-gl">';
					}

					$showLetter = ($currentProfilesCount == 0 or ($currentRowCount == 1 and $currentProfilesCount));

					if ($showLetter)
					{
						echo '<div class="letter">' . $letter . '</div>';
						echo '<div class="profiles"><ul>';
					}

					echo '<li>';
					echo $this->getProfileLink($profile->id);
					echo '</li>';

					$currentProfilesCount++;
					$letterDone     = $currentProfilesCount == count($profiles);
					$maxRowsReached = $this->maxColumnSize == $currentRowCount;
					if ($letterDone or $maxRowsReached)
					{
						echo '</ul></div>';
					}

					if ($letterDone)
					{
						$currentProfilesCount = 0;
						$totalLettersOutput++;

						// A little more complicated then it should be because of associative array use
						$temp     = array_slice($this->profiles, $totalLettersOutput, 1);
						$next     = array_shift($temp);
						$nextSize = count($next);
					}

					$currentRowCount++;
					$totalRowsOutput++;
					$rowsAvailable    = $this->maxColumnSize - $currentRowCount;
					$nextFitsInColumn = (!empty($nextSize) and $nextSize <= $rowsAvailable);
					$nextBreaksWell   = (!empty($nextSize) and $nextSize >= $tolerance * 2);
					$startNextLetter  = (($nextFitsInColumn or $nextBreaksWell) and $rowsAvailable >= $tolerance);
					$done             = $totalLettersOutput == count($this->profiles);

					if (($letterDone and !$startNextLetter) or $maxRowsReached or $done)
					{
						echo '</div>';
						$currentRowCount = 1;
					}
				}
			}
			?>
        </div>
    </div>
</div>
