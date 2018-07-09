<?php
/**
 * @package     Joomla.Site
 * @extension   com_thm_groups
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

// Including fallback code for HTML5 non supported browsers.
JHtml::_('jquery.framework');
JHtml::_('script', 'system/html5fallback.js', false, true);
?>
<fieldset id="<?php echo $id; ?>" class="<?php echo trim($class . ' radio'); ?>">
    <?php
    foreach ($options as $key => $option) {
        $checked = $default == $option['value'] ? 'checked="checked"' : '';
        ?>
        <input type="radio" id="<?php echo $id . $key; ?>" name="<?php echo $name; ?>"
               value="<?php echo $option['value']; ?>" <?php echo $checked; ?>>
        <label for="<?php echo $id . $key; ?>" class="btn"><?php echo JText::_($option['text']); ?></label>
        <?php
    }
    ?>
</fieldset>
