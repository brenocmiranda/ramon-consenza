<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$renewal_url = $tplData['renewal_url'];
?>
<span class='dashicons dashicons-warning'></span>
<div class="dup-sub-content">
    <h3>
        <?php _e('Warning! Your Duplicator Pro license has expired...', 'duplicator-pro');?>
    </h3>
    <?php _e('You\'re currently missing:', 'duplicator-pro'); ?>
    <ul class="dup-pro-simple-style-disc" >
        <li><?php _e('Access to Advanced Features', 'duplicator-pro'); ?></li>
        <li><?php _e('New Features', 'duplicator-pro'); ?></li>
        <li><?php _e('Important Updates for Security Patches', 'duplicator-pro'); ?></li>
        <li><?php _e('Bug Fixes', 'duplicator-pro'); ?></li>
        <li><?php _e('Support Requests', 'duplicator-pro'); ?></li>
    </ul>
    <a class="button" target="_blank" href="<?php echo $renewal_url; ?>">
        <?php _e('Renew Now!', 'duplicator-pro'); ?>
    </a>
</div>