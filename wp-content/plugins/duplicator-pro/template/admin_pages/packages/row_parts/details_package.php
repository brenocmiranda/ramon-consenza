<?php

/**
 * @package Duplicator
 */

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;

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

$pack_dbonly       = $package->isDBOnly();
$pack_format       = strtolower($package->Archive->Format);
$packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->ID);
$txt_DBOnly        = __('DB Only', 'duplicator-pro');
$archive_exists    = ($package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Archive) != false);
$isRecoveable      = DUP_PRO_Package_Recover::isPackageIdRecoveable($package->ID);

?>
<td colspan="8">
    <div  class="dup-package-row-details-wrapper" >
        <div class="dup-ovr-hdr">
            <label  onclick="DupPro.Pack.openLinkDetails()">
                <i class="fas fa-archive"></i>
                <?php _e('Package Overview', 'duplicator-pro'); ?>
            </label>
        </div>

        <div class="dup-ovr-bar-flex-box">
            <div class="divider">
                <label><?php DUP_PRO_U::esc_html_e('WordPress');?></label><br/>
                <?php echo ($package->VersionWP); ?> &nbsp;
            </div>
            <div>
                <label><?php DUP_PRO_U::esc_html_e('Format');?></label><br/>
                <?php echo strtoupper($pack_format); ?>
            </div>
            <div>
                <label><?php DUP_PRO_U::esc_html_e('Files');?></label><br/>
                <?php echo ($pack_dbonly)
                    ? "<i>{$txt_DBOnly}</i>"
                    : number_format($package->Archive->FileCount); ?>
            </div>
            <div class="divider">
                <label><?php DUP_PRO_U::esc_html_e('Folders');?></label><br/>
                <?php echo ($pack_dbonly)
                    ? "<i>{$txt_DBOnly}</i>"
                    :  number_format($package->Archive->DirCount) ?>
            </div>
            <div class="divider">
                <label><?php DUP_PRO_U::esc_html_e('Tables');?></label><br/>
                <?php echo "{$package->Database->info->tablesFinalCount} of {$package->Database->info->tablesBaseCount}"; ?>
            </div>
        </div>

        <div class="dup-ovr-ctrls-flex-box">

            <div class="flex-item">
            <?php
            if (CapMng::can(CapMng::CAP_EXPORT, false)) {
                $tplMng->render('admin_pages/packages/row_parts/details_download_block');
            }
            ?>
            </div>

            <!-- OPTIONS -->
            <div class="flex-item dup-ovr-opts">
                <div class="dup-ovr-ctrls-hdrs">
                    <br/><b><?php DUP_PRO_U::esc_html_e('Options');?></b>
                </div>
                <a
                    aria-label="<?php DUP_PRO_U::esc_attr_e("Go to package details screen") ?>"
                    class="button dup-details"
                    href="<?php echo esc_url($packageDetailsURL); ?>"
                >
                    <span><i class="fas fa-search"></i> <?php DUP_PRO_U::esc_html_e("View Details") ?></span>
                </a>
                <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
                    <?php if ($archive_exists) : ?>
                        <button class="button dup-transfer"
                            aria-label="<?php _e('Go to package transfer screen', 'duplicator-pro') ?>"
                            onclick="DupPro.Pack.OpenPackTransfer(<?php echo "$package->ID"; ?>); return false;">
                            <span><i class="fa fa-exchange-alt fa-fw"></i> <?php DUP_PRO_U::esc_html_e("Transfer Package") ?></span>
                        </button>
                    <?php else : ?>
                        <span title="<?php _e('Transfer packages requires the use of built-in default storage!', 'duplicator-pro') ?>">
                            <button class="button disabled" >
                                <span><i class="fa fa-exchange-alt fa-fw"></i> <?php DUP_PRO_U::esc_html_e("Transfer Package") ?></span>
                            </button>
                        </span>
                    <?php endif; ?>
                <?php } ?>

                <?php if (CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) { ?>
                    <?php $recovetBoxContent = $tplMng->render('admin_pages/packages/recovery_info/row_recovery_box', array(), false); ?>
                    <button
                        aria-label="<?php DUP_PRO_U::esc_attr_e("Recover this Package") ?>"
                        class="button dpro-btn-open-recovery-box <?php echo ($isRecoveable) ? '' : 'maroon'?>"
                        data-package-id="<?php echo $package->ID; ?>"
                        data-recovery-box="<?php echo esc_attr($recovetBoxContent); ?>">
                        <?php
                            echo ($isRecoveable)
                                ? '<i class="fas fa-undo-alt fa-fw"></i>&nbsp;'
                                : '<i class="fa fa-info-circle fa-fw"></i>&nbsp;';
                                _e("Recovery Point...", 'duplicator-pro') ?>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
</td>
