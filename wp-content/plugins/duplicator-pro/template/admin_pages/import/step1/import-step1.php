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
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */

$blur = $tplData['blur'];

if ($tplData['adminMessageViewModeSwtich'] && !$blur) {
    $tplMng->render('admin_pages/import/step1/message-view-mode-switch');
}

?>

<div class="dup-pro-import-header" >
    <h2 class="title">
        <i class="fas fa-arrow-alt-circle-down"></i> <?php printf(DUP_PRO_U::esc_html__("Step %s of 2: Upload Archive"), '<span class="red">1</span>'); ?>
    </h2>
    <?php if (!$blur) { ?>
        <div class="options" >
            <?php $tplMng->render('admin_pages/import/step1/views-and-options'); ?>
        </div>
    <?php } ?>
    <hr />
</div>

<div class="dup-import-header-content-wrapper <?php echo ($blur ? 'dup-mock-blur' : ''); ?>" >
    <?php $tplMng->render('admin_pages/import/step1/add-file-area'); ?>
    <?php $tplMng->render('admin_pages/import/step1/packages-list'); ?>
</div>


