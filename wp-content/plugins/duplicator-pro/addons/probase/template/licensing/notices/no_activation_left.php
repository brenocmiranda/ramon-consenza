<?php

/**
 * @package Duplicator
 */

use Duplicator\Addons\ProBase\LicensingController;
use Duplicator\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$licensing_tab_url = ControllersManager::getMenuLink(ControllersManager::SETTINGS_SUBMENU_SLUG, LicensingController::L2_SLUG_LICENSING);
$dashboard_url     = DUPLICATOR_PRO_BLOG_URL . 'dashboard';
$img_url           = plugins_url('duplicator-pro/assets/img/warning.png');

?>
<span class='dashicons dashicons-warning'></span>
<div class="dup-sub-content">
    <h3>
        <?php _e('Duplicator Pro\'s license is deactivated because you\'re out of site activations.', 'duplicator-pro'); ?>
    </h3>
    <?php _e('You\'re currently missing:', 'duplicator-pro'); ?>
    <ul class="dup-pro-simple-style-disc" >
        <li><?php _e('Access to Advanced Features', 'duplicator-pro'); ?></li>
        <li><?php _e('New Features', 'duplicator-pro'); ?></li>
        <li><?php _e('Important Updates for Security Patches', 'duplicator-pro'); ?></li>
        <li><?php _e('Bug Fixes', 'duplicator-pro'); ?></li>
        <li><?php _e('Support Requests', 'duplicator-pro'); ?></li>
    </ul>
    Upgrade your license using the 
    <a href="<?php echo esc_url($dashboard_url); ?>" target="_blank">
        Snap Creek Dashboard
    </a> 
    or deactivate plugin on old sites.<br/>
    After making necessary changes <a href="<?php echo esc_url($licensing_tab_url); ?>">refresh the license status.</a>
</div>