<?php

/**
 * @package Duplicator
 */

use Duplicator\Libs\Snap\SnapIO;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="filter-files-tab-content">
    <?php
    $uploads      = wp_upload_dir();
    $upload_dir   = SnapIO::safePath($uploads['basedir']);
    $content_path = defined('WP_CONTENT_DIR') ? SnapIO::safePath(WP_CONTENT_DIR) : '';
    ?>

    <?php $tplMng->render('parts/filters/package_components'); ?>
</div>
