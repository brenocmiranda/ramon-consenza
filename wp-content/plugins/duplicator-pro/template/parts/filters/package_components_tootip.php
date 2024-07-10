<?php

/**
 * Duplicator package row in table packages list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Package\Create\BuildComponents;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<p>
    <?php _e('Package components allow you to include/exclude differents part of your WordPress installation in the package.', 'duplicator-pro'); ?>
</p>
<ul>
    <li>
        <b><?php echo BuildComponents::getLabel(BuildComponents::COMP_DB); ?></b>: 
        <?php _e('Include the database in the package.', 'duplicator-pro'); ?>
    </li>
    <li>
        <b><?php echo BuildComponents::getLabel(BuildComponents::COMP_CORE); ?></b>: 
        <?php _e('Includes wordpress core files in the package (e.g. wp-include, wp-admin wp-login.php and other.', 'duplicator-pro'); ?>
    </li>
    <li>
        <b><?php echo BuildComponents::getLabel(BuildComponents::COMP_PLUGINS); ?></b>: 
        <?php _e(
            'Include the plugins in the package. With the <b>active only</b> option enabled, only active plugins will be included in the package.',
            'duplicator-pro'
        ); ?>
    </li>
    <li>
        <b><?php echo BuildComponents::getLabel(BuildComponents::COMP_THEMES); ?></b>: 
        <?php _e(
            'Include the themes in the package. With the <b>active only</b> option enabled, only active themes will be included in the package.',
            'duplicator-pro'
        ); ?>
    </li>
    <li>
        <b><?php echo BuildComponents::getLabel(BuildComponents::COMP_UPLOADS); ?></b>: 
        <?php _e('Include the \'uploads\' folder.', 'duplicator-pro'); ?>
    </li>
    <li>
        <b><?php echo BuildComponents::getLabel(BuildComponents::COMP_OTHER); ?></b>: 
        <?php _e('Include non-WordPress files and folders in the root directory.', 'duplicator-pro'); ?>
    </li>
</ul>