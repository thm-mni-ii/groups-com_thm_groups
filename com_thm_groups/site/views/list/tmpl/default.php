<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @description THMGroupsViewList file from com_thm_groups
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
?>
<div itemtype="http://schema.org/Article" itemscope="" class="thm_groups-list">
    <meta content="de-DE" itemprop="inLanguage">
    <div class="page-header">
        <?php echo "<h2>" . $this->title . "</h2>"; ?>
    </div>
    <div itemprop="articleBody" class="list-container">
        <?php
        if ($this->params->get('showAll') == 1) {
            echo $this->loadTemplate('list');
            
        }
        else
        {
            echo $this->loadTemplate('letter');
        }
        ?>
    </div>
</div>
