<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Controllers\ImportPageController;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

if (DUP_PRO_Global_Entity::getInstance()->import_chunk_size == 0) {
    $footerChunkInfo = sprintf(DUP_PRO_U::__('<b>Chunk Size:</b> N/A &nbsp;|&nbsp; <b>Max Size:</b> %s'), size_format(wp_max_upload_size()));
    $toolTipContent  = DUP_PRO_U::__('If you need to upload a larger file, go to [Settings > Import] and set Upload Chunk Size');
} else {
    $footerChunkInfo = sprintf(
        DUP_PRO_U::__(
            '<b>Chunk Size:</b> %s &nbsp;|&nbsp; <b>Max Size:</b> No Limit'
        ),
        size_format(ImportPageController::getChunkSize() * 1024)
    );
    $toolTipContent  = DUP_PRO_U::__('The max file size limit is ignored when chunk size is enabled.  '
            . 'Use a large chunk size with fast connections and a small size with slower connections.  '
            . 'You can change the chunk size by going to [Settings > Import].');
}

/** @var string */
$openTab = $tplData['defSubtab'];

$hlpUpload = DUP_PRO_U::__('Upload speeds can be affected by various server connections and setups.  Additionally, chunk size can influence the '
    . 'upload speed [Settings > Import].  If changing the chunk size is still slow, try uploading the archive manually with these steps:');

$hlpUpload .= '<ul>' .
    '<li>' . DUP_PRO_U::__('1. Cancel current upload') . '</li>' .
    '<li>' . DUP_PRO_U::__('2. Manually upload archive to:<br/> &nbsp; &nbsp; <i>/wp-content/backups-dup-pro/imports/</i>') . '</li>' .
    '<li>' . DUP_PRO_U::__('3. Refresh the Import screen') . '</li>' .
    '</ul>';
?>
<!-- ==============================
DRAG/DROP AREA -->
<div id="dup-pro-import-upload-tabs-wrapper" class="dup-pro-tabs-wrapper margin-bottom-2">
    <div id="dup-pro-import-mode-tab-header" class="clearfix margin-bottom-2" >
        <div 
            id="dup-pro-import-mode-upload-tab" 
            class="<?php echo ($openTab == ImportPageController::L2_TAB_UPLOAD ? 'active' : ''); ?>" 
            data-tab-target="dup-pro-import-upload-file-tab" 
        >
            <i class="far fa-file-archive"></i> <?php _e('Import File', 'duplicator-pro'); ?>
        </div>
        <div 
            id="dup-pro-import-mode-remote-tab" 
            class="<?php echo ($openTab == ImportPageController::L2_TAB_REMOTE_URL ? 'active' : ''); ?>" 
            data-tab-target="dup-pro-import-remote-file-tab" 
        >
            <i class="fas fa-link"></i> <?php _e('Import Link', 'duplicator-pro'); ?>
        </div>
    </div>
    <div 
        id="dup-pro-import-upload-file-tab" 
        class="tab-content <?php echo ($openTab == ImportPageController::L2_TAB_UPLOAD ? '' : 'no-display'); ?>" 
    >
        <div id="dup-pro-import-upload-file" class="dup-pro-import-upload-box" ></div>
        <div class="no_display" >
            <div id="dup-pro-import-upload-file-content" class="center-xy" >
                <i class="fa fa-download fa-2x"></i>            
                <span class="dup-drag-drop-message" >
                    <?php esc_html_e("Drag & Drop Archive File Here", 'duplicator-pro'); ?>
                </span>
                <input 
                    id="dup-import-dd-btn" 
                    type="button" 
                    class="button button-large button-default dup-import-button" 
                    name="dpro-files" 
                    value="<?php DUP_PRO_U::esc_attr_e("Select File..."); ?>"
                >
            </div>
        </div>
        <div id="dup-pro-import-upload-file-footer" >
            <i 
                class="fas fa-question-circle fa-sm" 
                data-tooltip-title="<?php DUP_PRO_U::esc_html_e("Upload Chunk Size"); ?>" 
                data-tooltip="<?php echo esc_attr($toolTipContent); ?>"
            ></i>&nbsp;<?php echo $footerChunkInfo; ?>&nbsp;|&nbsp;
            <span 
                class="pointer link-style" 
                data-tooltip-title="<?php DUP_PRO_U::esc_html_e("Improve Upload Speed"); ?>" 
                data-tooltip="<?php echo esc_attr($hlpUpload); ?>" 
            >
                <i><?php DUP_PRO_U::esc_html_e('Slow Upload'); ?></i>&nbsp;<i class="fas fa-question-circle fa-sm" ></i>
            </span>
        </div>
    </div>
    <div 
        id="dup-pro-import-remote-file-tab" 
        class="tab-content <?php echo ($openTab == ImportPageController::L2_TAB_REMOTE_URL ? '' : 'no-display'); ?>"
    >
        <div class="dup-pro-import-upload-box">
            <div class="center-xy" >
                <i class="fa fa-download fa-2x"></i>            
                <span class="dup-drag-drop-message" >
                    <?php esc_html_e("Import From Link", 'duplicator-pro'); ?>
                </span>
                <input 
                    type="text" 
                    id="dup-pro-import-remote-url"
                    placeholder="<?php _e('Enter Full URL to Archive', 'duplicator-pro'); ?>" />
                <button id="dup-pro-import-remote-upload" type="button" class="button button-large button-default dup-import-button" >
                    <?php esc_html_e("Upload", 'duplicator-pro'); ?>
                </button> <br/>
                <small>
                    <?php
                        echo sprintf(
                            '%s <a href="' . DUPLICATOR_PRO_DUPLICATOR_DOCS_URL . 'how-to-handle-import-install-upload-launch-issues" target="_blank">%s</a>.',
                            __('This feature is in beta, for additional help visit the', 'duplicator-pro'),
                            __('online faq', 'duplicator-pro')
                        );
                        ?>
                </small>
            </div>
        </div>
    </div>
</div>