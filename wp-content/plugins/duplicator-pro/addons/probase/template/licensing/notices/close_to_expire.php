<?php

/**
 * @package Duplicator
 */

use Duplicator\Addons\ProBase\License\License;

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
<span class='dashicons dashicons-info'></span>
<div class="dup-sub-content">
    <h3>
        <?php
        printf(
            _n(
                'Warning! Your Duplicator Pro license will expire in one day',
                'Warning! Your Duplicator Pro license will expire in %d days',
                License::getExpirationDays(),
                'duplicator-pro'
            ),
            License::getExpirationDays()
        );
        ?>
    </h3>
    <?php _e('Renew your license before it expires so you don\'t lose:', 'duplicator-pro'); ?><br/>
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
