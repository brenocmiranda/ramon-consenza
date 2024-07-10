<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

if (!isset($tplData['isInstallerCleanup']) || !$tplData['isInstallerCleanup']) {
    return;
}

?>
<div id="message" class="notice notice-success">
    <?php $tplMng->render('parts/migration/clean-installation-files'); ?>
</div>