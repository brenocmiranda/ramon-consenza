<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Core\Views\TplMng;

$tplMng = TplMng::getInstance();

$global = DUP_PRO_Global_Entity::getInstance();

$ui_css_archive = (DUP_PRO_UI_ViewState::getValue('dup-pack-archive-panel') ? 'display:block' : 'display:none');
$multisite_css  = is_multisite() ? '' : 'display:none';

$archive_format = ($global->getBuildMode() == DUP_PRO_Archive_Build_Mode::DupArchive ? 'daf' : 'zip');
?>

<!-- ===================
 META-BOX: ARCHIVE -->
<div class="dup-box dup-archive-filters-wrapper">
    <div class="dup-box-title" >
        <i class="far fa-file-archive fa-sm"></i> <?php DUP_PRO_U::esc_html_e('Archive') ?> 
        <sup class="dup-box-title-badge">
            <?php echo esc_html($archive_format); ?>
        </sup> &nbsp; &nbsp;
        <span class="dup-archive-filters-icons">
            <span id="dup-archive-filter-file" title="<?php DUP_PRO_U::esc_attr_e('Folder/File Filter Enabled') ?>">
                <span class="btn-separator"></span>
                <i class="fas fa-folder-open fa-fw"></i>
                <sup><i class="fas fa-filter fa-xs"></i></sup>
            </span>
            <span id="dup-archive-filter-db" title="<?php DUP_PRO_U::esc_attr_e('Database Table Filter Enabled') ?>">
                <span class="btn-separator"></span>
                <i class="fas fa-table fa-fw"></i>
                <sup><i class="fas fa-filter fa-xs"></i></sup>
            </span>
            <span id="dup-archive-db-only" title="<?php DUP_PRO_U::esc_attr_e('Archive Only the Database') ?>">
                <span class="btn-separator"></span>
                <i class="fas fa-database fa-fw"></i>
                <?php DUP_PRO_U::esc_html_e('Database Only') ?>
            </span>
            <span id="dup-archive-media-only" title="<?php DUP_PRO_U::esc_attr_e('Archive Only Media files') ?>">
                <span class="btn-separator"></span>
                <i class="fas fa-file-image fa-fw"></i>
                <?php DUP_PRO_U::esc_html_e('Media Only') ?>
            </span>
            <span id="dpro-install-secure-lock" title="<?php DUP_PRO_U::esc_attr_e('Archive password protection is on') ?>">
                <span class="btn-separator"></span>
                <i class="fas fa-lock fa-fw"></i>
                <?php DUP_PRO_U::esc_html_e('Requires Password') ?>
            </span>
        </span>
        <button class="dup-box-arrow">
            <span class="screen-reader-text"><?php DUP_PRO_U::esc_html_e('Toggle panel:') ?> <?php DUP_PRO_U::esc_html_e('Archive Settings') ?></span>
        </button>
    </div>
    
    <div class="dup-box-panel" id="dup-pack-archive-panel" style="<?php echo esc_attr($ui_css_archive); ?>">
        <input type="hidden" name="archive-format" value="ZIP" />

        <!-- ===================
        NESTED TABS -->
        <div data-dpro-tabs="true">
            <ul>
                <li class="filter-files-tab"><?php DUP_PRO_U::esc_html_e('Files') ?></li>
                <li class="filter-db-tab"><?php DUP_PRO_U::esc_html_e('Database') ?></li>
                <?php if (is_multisite()) { ?>
                <li class="filter-mu-tab" style="<?php echo $multisite_css ?>"><?php DUP_PRO_U::esc_html_e('Multisite') ?></li>
                <?php } ?>
                <li class="archive-setup-tab"><?php DUP_PRO_U::esc_html_e('Security') ?></li>
            </ul>

            <?php
                $tplMng->render('admin_pages/packages/setup/archive-filter-files-tab');
                $tplMng->render('admin_pages/packages/setup/archive-filter-db-tab');
            if (is_multisite()) {
                $tplMng->render('admin_pages/packages/setup/archive-filter-mu-tab');
            }
                $tplMng->render('admin_pages/packages/setup/archive-setup-tab');
            ?>
        </div>
    </div>
</div>

<div class="duplicator-error-container"></div>
<?php
    $alert1          = new DUP_PRO_UI_Dialog();
    $alert1->title   = DUP_PRO_U::__('ERROR!');
    $alert1->message = DUP_PRO_U::__('You can\'t exclude all sites.');
    $alert1->initAlert();
?>
<script>
//INIT
jQuery(document).ready(function($) 
{
    //MU-Transfer buttons
    $('#mu-include-btn').click(function() {
        return !$('#mu-exclude option:selected').remove().appendTo('#mu-include');  
    });

    $('#mu-exclude-btn').click(function() {
        var include_all_count = $('#mu-include option').length;
        var include_selected_count = $('#mu-include option:selected').length;

        if(include_all_count > include_selected_count) {
            return !$('#mu-include option:selected').remove().appendTo('#mu-exclude');
        } else {
            <?php $alert1->showAlert(); ?>
        }
    });

});
</script>
