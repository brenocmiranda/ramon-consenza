<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Core\MigrationMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

if (!isset($tplData['isMigrationSuccessNotice']) || !$tplData['isMigrationSuccessNotice']) {
    $tplMng->render('parts/migration/tool-cleanup-installer-files');
    return;
}

$safeMsg       = MigrationMng::getSaveModeWarning();
$cleanupReport = MigrationMng::getCleanupReport();

?>
<div class="notice notice-success dpro-admin-notice dup-migration-pass-wrapper">
    <div class="dup-migration-pass-title">
        <i class="fa fa-check-circle"></i> <?php
        if (MigrationMng::getMigrationData('restoreBackupMode')) {
            DUP_PRO_U::_e('This site has been successfully restored!');
        } else {
            DUP_PRO_U::_e('This site has been successfully migrated!');
        }
        ?>
    </div>
    <p>
        <?php printf(DUP_PRO_U::__('The following installation files are stored in the folder <b>%s</b>'), DUPLICATOR_PRO_SSDIR_PATH_INSTALLER); ?>
    </p>
    <ul class="dup-stored-minstallation-files">
        <?php foreach (MigrationMng::getStoredMigrationLists() as $path => $label) { ?>
            <li>
                - <?php echo esc_html($label); ?>
            </li>
        <?php } ?>
    </ul>

    <?php
    if (isset($tplData['isInstallerCleanup']) && $tplData['isInstallerCleanup']) {
        $tplMng->render('parts/migration/clean-installation-files');
    } else {
        if (count($cleanupReport['instFile']) > 0) { ?>
            <p>
                <?php _e('Security actions:', 'duplicator-pro'); ?>
            </p>
            <ul class="dup-stored-minstallation-files">
                <?php
                foreach ($cleanupReport['instFile'] as $html) { ?>
                    <li>
                        <?php echo $html; ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        <p>
            <b><?php DUP_PRO_U::_e('Final step:'); ?></b><br>
            <span id="dpro-notice-action-remove-installer-files" class="link-style" onclick="DupPro.Tools.removeInstallerFiles();">
                <?php DUP_PRO_U::esc_html_e('Remove Installation Files Now!'); ?>
            </span>
        </p>
        <?php if (strlen($safeMsg) > 0) { ?>
            <div class="notice-safemode">
                <?php echo esc_html($safeMsg); ?>
            </div>
        <?php } ?>

        <p class="sub-note">
            <i><?php
                DUP_PRO_U::_e('Note: This message will be removed after all installer files are removed.'
                    . ' Installer files must be removed to maintain a secure site.'
                    . ' Click the link above to remove all installer files and complete the migration.');
                ?><br>
                <i class="fas fa-info-circle"></i>
                <?php
                DUP_PRO_U::_e('If an archive.zip/daf file was intentially added to the root directory to '
                    . 'perform an overwrite install of this site then you can ignore this message.')
                ?>
            </i>
        </p>
        <?php
    }

    echo apply_filters(MigrationMng::HOOK_BOTTOM_MIGRATION_MESSAGE, '');
    ?>
</div>