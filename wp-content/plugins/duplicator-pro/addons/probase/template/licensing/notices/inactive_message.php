<?php

/**
 * @package Duplicator
 */

use Duplicator\Addons\ProBase\LicensingController;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$img_url           = plugins_url('duplicator-pro/assets/img/warning.png');
$problem_text      = $tplData['problem'];
$licensing_tab_url = ControllersManager::getMenuLink(ControllersManager::SETTINGS_SUBMENU_SLUG, LicensingController::L2_SLUG_LICENSING);
?>
<span class='dashicons dashicons-warning'></span>
<div class="dup-sub-content">
    <h3>
        <?php
        printf(
            _x('Your Duplicator Pro license key is %1$s ...', '%1$s represent the license status', 'duplicator-pro'),
            $tplData['problem']
        );
        ?>
    </h3>
    <?php _e('You\'re currently missing:', 'duplicator-pro'); ?>
    <ul class="dup-pro-simple-style-disc" >
        <li><?php _e('Access to Advanced Features', 'duplicator-pro'); ?></li>
        <li><?php _e('New Features', 'duplicator-pro'); ?></li>
        <li><?php _e('Important Updates for Security Patches', 'duplicator-pro'); ?></li>
        <li><?php _e('Bug Fixes', 'duplicator-pro'); ?></li>
        <li><?php _e('Support Requests', 'duplicator-pro'); ?></li>
    </ul>
    <b>Please <a href="<?php echo esc_url($licensing_tab_url); ?>">
        Activate Your License
    </a></b>.&nbsp;
    If you do not have a license key go to <a target='_blank' href='<?php echo DUPLICATOR_PRO_BLOG_URL; ?>dashboard'>
        duplicator.com
    </a> to get it.
</div>
