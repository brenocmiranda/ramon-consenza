<?php

/**
 * @package Duplicator
 */

use Duplicator\Addons\ProBase\License\License;
use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Views\TplMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var ?DUP_PRO_Package $package
 */
$package = $tplData['package'];
$global  = DUP_PRO_Global_Entity::getInstance();
global $packagesViewData;


$isRecoveable      = DUP_PRO_Package_Recover::isPackageIdRecoveable($package->ID);
$isRecoverPoint    = (DUP_PRO_Package_Recover::getRecoverPackageId() === $package->ID);
$pack_name         = $package->Name;
$pack_archive_size = $package->Archive->Size;
$pack_namehash     = $package->NameHash;
$pack_dbonly       = $package->isDBOnly();
$brand             = $package->Brand;

//Links
$uniqueid         = $package->NameHash;
$archive_exists   = ($package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Archive) != false);
$installer_exists = ($package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Installer) != false);
$progress_error   = '';

//ROW CSS
$rowClasses   = array('');
$rowClasses[] = ($package->Status >= DUP_PRO_PackageStatus::COMPLETE) ? 'dup-row-complete' : 'dup-row-incomplete';
$rowClasses[] = ($packagesViewData['rowCount'] % 2 == 0) ? 'dup-row-alt-dark' : 'dup-row-alt-light';
$rowClasses[] = ($isRecoverPoint) ? 'dup-recovery-package' : '';
$rowCSS       = trim(implode(' ', $rowClasses));


//ArchiveInfo
$archive_name         = $package->Archive->File;
$archiveDownloadURL   = $package->getLocalPackageFileURL(DUP_PRO_Package_File_Type::Archive);
$installerDownloadURL = $package->getLocalPackageFileURL(DUP_PRO_Package_File_Type::Installer);
$installerFullName    = $package->Installer->getInstallerName();

//Lang Values
$txt_DatabaseOnly = __('Database Only', 'duplicator-pro');

switch ($package->Type) {
    case DUP_PRO_PackageType::MANUAL:
        $package_type_string = DUP_PRO_U::__('Manual');
        break;
    case DUP_PRO_PackageType::SCHEDULED:
        $package_type_string = DUP_PRO_U::__('Schedule');
        break;
    case DUP_PRO_PackageType::RUN_NOW:
        $lang_schedule       = DUP_PRO_U::__('Schedule');
        $lang_title          = DUP_PRO_U::__('This package was started manually from the schedules page.');
        $package_type_string = "{$lang_schedule}<span><sup>&nbsp;<i class='fas fa-cog fa-sm pointer' title='{$lang_title}'></i>&nbsp;</sup><span>";
        break;
    default:
        $package_type_string = DUP_PRO_U::__('Unknown');
        break;
}

$packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->ID);

//===============================================
//COMPLETED: Rows with good data
//===============================================
if ($package->Status >= DUP_PRO_PackageStatus::COMPLETE) :?>
    <tr class="<?php echo $rowCSS; ?>" id="dup-row-pack-id-<?php echo $package->ID; ?>">
        <td class="dup-cell-chk">
            <label for="<?php echo $package->ID; ?>">
            <input 
                name="delete_confirm" 
                type="checkbox" 
                id="<?php echo $package->ID; ?>" 
                data-archive-name="<?php echo esc_attr($archive_name); ?>" 
                data-installer-name="<?php echo esc_attr($installerFullName); ?>" />
            </label>
        </td>
        <td>
            <?php
            echo $package_type_string;
            if ($pack_dbonly) {
                echo "<sup title='{$txt_DatabaseOnly}'>&nbsp;&nbsp;DB</sup>";
            }
            if ($isRecoveable) {
                $title = ($isRecoverPoint ? DUP_PRO_U::esc_attr__('Active Recovery Point') : DUP_PRO_U::esc_attr__('Recovery Point Capable'));
                echo "<sup>&nbsp;&nbsp;<i class='dup-pro-recoverable-status fas fa-undo-alt' data-tooltip='{$title}'></i></sup>";
            }
            ?>
        </td>
        <td><?php echo DUP_PRO_Package::format_and_get_local_date_time($package->Created, $packagesViewData['package_ui_created']); ?></td>
        <td><?php echo DUP_PRO_U::byteSize($pack_archive_size); ?></td>
        <td class="dup-cell-name">
            <?php
            echo esc_html($pack_name);
            if ($isRecoverPoint) {
                echo ' ';
                $recoverPackage = DUP_PRO_Package_Recover::getRecoverPackage();
                require(DUPLICATOR____PATH . '/views/tools/recovery/recovery-small-icon.php');
            }
            ?>
        </td>
        <?php
            $tplMng->render('admin_pages/packages/row_parts/download_buttons');
            $tplMng->render('admin_pages/packages/row_parts/storages_buttons');
        ?>
        <td class="dup-cell-btns dup-cell-toggle-btn dup-toggle-details">
            <span class="button button-link">
                <i class="fas fa-chevron-left"></i>
            </span>
        </td>
    </tr>

    <tr id="dup-row-pack-id-<?php echo $package->ID; ?>-details" class="dup-row-details">
        <?php $tplMng->render('admin_pages/packages/row_parts/details_package'); ?>
    </tr>
<?php else :
    //===============================================
    //INCOMPLETE: Progress/Failures/Cancelations
    //===============================================

    $cellErrCSS = '';

    if ($package->Status < DUP_PRO_PackageStatus::COPIEDPACKAGE) {
        // In the process of building
        $size      = 0;
        $tmpSearch = glob(DUPLICATOR_PRO_SSDIR_PATH_TMP . "/{$pack_namehash}_*");

        if (is_array($tmpSearch)) {
            $result = @array_map('filesize', $tmpSearch);
            $size   = array_sum($result);
        }
        $pack_archive_size = $size;
    }

    // If its in the pending cancels consider it stopped
    if (in_array($package->ID, $packagesViewData['pending_cancelled_package_ids'])) {
        $status = DUP_PRO_PackageStatus::PENDING_CANCEL;
    } else {
        $status = $package->Status;
    }

    $progress_html    = "<span style='display:none' id='status-{$package->ID}'>{$status}</span>";
    $stop_button_text = DUP_PRO_U::__('Stop');

    if ($status >= 0) {
        if ($status >= 75) {
            $stop_button_text = DUP_PRO_U::__('Stop Transfer');
            $progress_html    = "<i class='fa fa-sync fa-sm fa-spin'></i>&nbsp;<span id='status-progress-{$package->ID}'>0</span>%"
            . "<span style='display:none' id='status-{$package->ID}'>{$status}</span>";
        } elseif ($status > 0) {
            $stop_button_text = DUP_PRO_U::__('Stop Build');
            $progress_html    = "<i class='fa fa-cog fa-sm fa-spin'></i>&nbsp;<span id='status-{$package->ID}'>{$status}</span>%";
        } else {
            // In a pending state
            $stop_button_text = DUP_PRO_U::__('Cancel Pending');
            $progress_html    = "<span style='display:none' id='status-{$package->ID}'>{$status}</span>";
        }
    } else {
        //FAILURES AND CANCELLATIONS
        switch ($status) {
            case DUP_PRO_PackageStatus::ERROR:
                $cellErrCSS     = 'dup-cell-err';
                $progress_error = '<div class="progress-error">'
                . '<a type="button" class="dup-cell-err-btn button" href="' . esc_url($packageDetailsURL) . '">'
                . '<i class="fa fa-exclamation-triangle fa-xs"></i>&nbsp;'
                .  DUP_PRO_U::__('Error Processing') . "</a></div><span style='display:none' id='status-" . $package->ID . "'>$status</span>";
                break;

            case DUP_PRO_PackageStatus::BUILD_CANCELLED:
                $cellErrCSS     = 'dup-cell-cancelled';
                $progress_error = '<div class="progress-error"><i class="fas fa-info-circle  fa-sm"></i>&nbsp;'
                . DUP_PRO_U::__('Build Cancelled') . "</div><span style='display:none' id='status-" . $package->ID . "'>$status</span>";
                break;

            case DUP_PRO_PackageStatus::PENDING_CANCEL:
                $progress_error = '<div class="progress-error"><i class="fas fa-info-circle  fa-sm"></i> '
                . DUP_PRO_U::__('Cancelling Build') . "</div><span style='display:none' id='status-"
                . $package->ID . "'>$status</span>";
                break;

            case DUP_PRO_PackageStatus::REQUIREMENTS_FAILED:
                $package_id            = $package->ID;
                $package               = DUP_PRO_Package::get_by_id($package_id);
                $package_log_store_dir = trailingslashit(dirname($package->StorePath));
                $is_txt_log_file_exist = file_exists("{$package_log_store_dir}{$package->NameHash}_log.txt");
                if ($is_txt_log_file_exist) {
                    $link_log = "{$package->StoreURL}{$package->NameHash}_log.txt";
                } else {
                    // .log is for backward compatibility
                    $link_log = "{$package->StoreURL}{$package->NameHash}.log";
                }
                $progress_error = '<div class="progress-error"><a href="' . esc_url($link_log) . '" target="_blank">'
                . '<i class="fas fa-info-circle"></i> '
                . DUP_PRO_U::__('Requirements Failed') . "</a></div>"
                . "<span style='display:none' id='status-" . $package->ID . "'>$status</span>";
                break;
        }
    }
    ?>

    <tr class="<?php echo $rowCSS; ?>" id="dup-row-pack-id-<?php echo $package->ID; ?>">
        <td class="dup-cell-chk">
            <label for="<?php echo $package->ID; ?>">
            <input name="delete_confirm"
                   type="checkbox" id="<?php echo $package->ID;?>"
                   <?php echo ($status >= DUP_PRO_PackageStatus::PRE_PROCESS) ? 'disabled="disabled"' : ''; ?> />
            </label>
        </td>
        <td>
            <?php
                echo (($package->Type == DUP_PRO_PackageType::MANUAL) ? DUP_PRO_U::__('Manual') : DUP_PRO_U::__('Schedule'));
                echo ($pack_dbonly) ? "<sup title='{$txt_DatabaseOnly}'>&nbsp;&nbsp;<i>DB</i></sup>" : '';
            ?>
        </td>
        <td><?php echo DUP_PRO_Package::format_and_get_local_date_time($package->Created, $packagesViewData['package_ui_created']); ?></td>
        <td><?php echo $package->get_display_size(); ?></td>
        <td class="dup-cell-name"><?php echo esc_html($pack_name); ?></td>
        <td class="dup-cell-incomplete <?php echo $cellErrCSS; ?> no-select" colspan="3">
            <?php if ($status >= DUP_PRO_PackageStatus::STORAGE_PROCESSING) : ?>
                <?php if (CapMng::can(CapMng::CAP_EXPORT, false)) { ?>
                <button
                    id="<?php echo "{$uniqueid}_{$global->installer_base_name}" ?>" 
                    <?php DUP_PRO_UI::echoDisabled(!$installer_exists); ?> 
                    class="button button-link no-select dup-dnload-btn-single"
                    onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>'); return false;">
                    <i class="fa <?php echo ($installer_exists ? 'fa-bolt' : 'fa-exclamation-triangle maroon') ?>"></i>
                    <?php DUP_PRO_U::esc_html_e("Installer") ?>
                </button>
                <button 
                    id="<?php echo "{$uniqueid}_archive.zip" ?>" 
                    <?php DUP_PRO_UI::echoDisabled(!$archive_exists); ?> 
                    class="button button-link no-select dup-dnload-btn-single"
                    onclick="location.href = '<?php echo $package->Archive->getURL(); ?>'; return false;">
                    <i class="<?php echo ($archive_exists ? 'far fa-file-archive' : 'fa fa-exclamation-triangle maroon') ?>"></i>&nbsp;
                    <?php DUP_PRO_U::esc_html_e("Archive") ?>
                </button>
                <?php } ?>
            <?php else : ?>
                <?php if ($status == 0) : ?>
                    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                    <button onclick="DupPro.Pack.StopBuild(<?php echo $package->ID; ?>); return false;" class="button button-large dup-build-stop-btn">
                        <i class="fa fa-times fa-sm"></i> &nbsp; <?php echo $stop_button_text; ?>
                    </button>
                    <?php } ?>
                    <?php echo $progress_html; ?>
                <?php else : ?>
                    <?php
                        echo ($status > 0)
                            ? '<i>' . DUP_PRO_U::__('Building Package Files...') . '</i>'
                            : $progress_error;
                    ?>
                <?php endif; ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php if ($status == DUP_PRO_PackageStatus::PRE_PROCESS) : ?>
    <!--   NO DISPLAY -->
    <?php elseif ($status > DUP_PRO_PackageStatus::PRE_PROCESS) :
        //===============================================
        //PROGRESS BAR DISPLAY AREA
        //=============================================== ?>
    <tr class="dup-row-progress">
        <td colspan="8">
            <div class="wp-filter dup-build-msg">
                <?php if ($status < DUP_PRO_PackageStatus::STORAGE_PROCESSING) : ?>
                    <!-- BUILDING PROGRESS-->
                    <div id='dpro-progress-status-message-build'>
                        <div class='status-hdr'>
                            <?php _e('Building Package', 'duplicator-pro'); ?>&nbsp;<?php echo $progress_html; ?>
                        </div>
                        <small>
                            <?php _e('Please allow it to finish before creating another one.', 'duplicator-pro'); ?>
                        </small>
                    </div>
                <?php else : ?>
                    <!-- TRANSFER PROGRESS -->
                    <div id='dpro-progress-status-message-transfer'>
                        <div class='status-hdr'>
                            <?php _e('Transferring Package', 'duplicator-pro'); ?>&nbsp;<?php echo $progress_html; ?>
                        </div>
                        <small id="dpro-progress-status-message-transfer-msg">
                            <?php _e('Getting Transfer State...', 'duplicator-pro'); ?>
                        </small>
                    </div>
                <?php endif; ?>
                <div id="dup-progress-bar-area">
                    <div class="dup-pro-meter-wrapper">
                        <div class="dup-pro-meter blue dup-pro-fullsize">
                            <span></span>
                        </div>
                        <span class="text"></span>
                    </div>
                </div>
                <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                <button onclick="DupPro.Pack.StopBuild(<?php echo $package->ID; ?>); return false;" class="button button-large dup-build-stop-btn">
                    <i class="fa fa-times fa-sm"></i> &nbsp; <?php echo $stop_button_text; ?>
                </button>
                <?php } ?>
            </div>
        </td>
    </tr>
    <?php else : ?>
    <!--   NO DISPLAY -->
    <?php endif; ?>
<?php endif; ?>
<?php
$packagesViewData['rowCount']++;
