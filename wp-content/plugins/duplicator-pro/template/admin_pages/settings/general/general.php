<?php

/**
 * @package Duplicator
 */

use Duplicator\Addons\ProBase\License\License;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Utils\ExpireOptions;
use Duplicator\Controllers\SettingsPageController;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global = DUP_PRO_Global_Entity::getInstance();

$trace_log_enabled       = (bool) get_option('duplicator_pro_trace_log_enabled');
$send_trace_to_error_log = (bool) get_option('duplicator_pro_send_trace_to_error_log');

if ($trace_log_enabled) {
    $logging_mode = ($send_trace_to_error_log) ?  'enhanced' : 'on';
} else {
    $logging_mode = 'off';
}

?>

<form id="dup-settings-form" action="<?php echo ControllersManager::getCurrentLink(); ?>" method="post" data-parsley-validate>
    <?php $tplData['actions'][SettingsPageController::ACTION_GENERAL_SAVE]->getActionNonceFileds(); ?>
    <?php
    $duplicator_pro_settings_message = ExpireOptions::get(DUPLICATOR_PRO_SETTINGS_MESSAGE_TRANSIENT);
    if ($duplicator_pro_settings_message) {
        ?>
        <div class="notice notice-success is-dismissible dpro-wpnotice-box">
            <p><?php echo esc_html($duplicator_pro_settings_message); ?></p>
        </div>
        <?php
        ExpireOptions::delete(DUPLICATOR_PRO_SETTINGS_MESSAGE_TRANSIENT);
    }
    ?>

    <!-- ===============================
PLUG-IN SETTINGS -->
    <h3 class="title"><?php DUP_PRO_U::esc_html_e("Plugin") ?> </h3>
    <hr size="1" />
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <label>
                    <?php
                        DUP_PRO_U::esc_html_e("Version");
                    ?>
                </label>
            </th>
            <td>
                <?php
                    echo DUPLICATOR_PRO_VERSION;
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Uninstall"); ?></label></th>
            <td>
                <input
                    type="checkbox"
                    name="uninstall_settings"
                    id="uninstall_settings"
                    value="1"
                    <?php checked($global->uninstall_settings); ?>
                >
                <label for="uninstall_settings"><?php DUP_PRO_U::esc_html_e("Delete plugin settings"); ?> </label><br />

                <input
                    type="checkbox"
                    name="uninstall_packages"
                    id="uninstall_packages"
                    value="1"
                    <?php checked($global->uninstall_packages); ?>
                >
                <label for="uninstall_packages"><?php DUP_PRO_U::esc_html_e("Delete entire storage directory"); ?></label><br />

            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Encrypt Settings"); ?></label></th>
            <td>
                <input
                    type="checkbox"
                    name="crypt"
                    id="crypt"
                    value="1"
                    <?php checked($global->crypt); ?>
                >
                <label for="crypt"><?php DUP_PRO_U::esc_html_e("Enable settings encryption"); ?> </label><br />
                <p class="description">
                    <?php DUP_PRO_U::esc_html_e("Only uncheck if machine doesn't support PCrypt."); ?>
                </p>
            </td>
        </tr>
    </table><br />

    <!-- ===============================
DEBUG SETTINGS -->
    <h3 class="title"><?php DUP_PRO_U::esc_html_e('Debug') ?> </h3>
    <hr size="1" />

    <table class="form-table">
        <tr>
            <th scope="row"><label><?php echo DUP_PRO_U::__("Trace Log"); ?></label></th>
            <td>
                <select name="_logging_mode">
                    <option value="off" <?php DUP_PRO_UI::echoSelected($logging_mode == 'off'); ?>>
                        <?php DUP_PRO_U::esc_html_e('Off'); ?>
                    </option>
                    <option value="on" <?php DUP_PRO_UI::echoSelected($logging_mode == 'on'); ?>>
                        <?php DUP_PRO_U::esc_html_e('On'); ?>
                    </option>
                    <option value="enhanced" <?php DUP_PRO_UI::echoSelected($logging_mode == 'enhanced'); ?>>
                        <?php DUP_PRO_U::esc_html_e('On (Enhanced)'); ?>
                    </option>
                </select>
                <p class="description">
                    <?php
                    DUP_PRO_U::esc_html_e("Turning on log initially clears it out. The enhanced setting writes to both trace and PHP error logs.");
                    echo "<br/>";
                    DUP_PRO_U::esc_html_e("WARNING: Only turn on this setting when asked to by support as tracing will impact performance.");
                    ?>
                </p><br />
                <button class="button" <?php disabled(DUP_PRO_Log::traceFileExists(), false); ?> onclick="DupPro.Pack.DownloadTraceLog(); return false">
                    <i class="fa fa-download"></i> <?php echo DUP_PRO_U::__('Download Trace Log') . ' (' . DUP_PRO_Log::getTraceStatus() . ')'; ?>
                </button>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Debugging"); ?></label></th>
            <td>
                <input
                    type="checkbox"
                    name="_debug_on"
                    id="_debug_on"
                    value="1"
                    <?php checked($global->debug_on); ?>
                >
                <label for="_debug_on"><?php DUP_PRO_U::esc_html_e("Enable debug options throughout plugin"); ?></label>
                <p class="description"><?php DUP_PRO_U::esc_html_e('Refresh page after saving to show/hide Debug menu.'); ?></p>
            </td>
        </tr>
    </table><br />

    <!-- ===============================
ADVANCED SETTINGS -->
    <h3 class="title"><?php DUP_PRO_U::esc_html_e('Advanced') ?> </h3>
    <hr size="1" />
    <table class="form-table">
        <tr>
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Settings"); ?></label></th>
            <td>
                <button id="dup-pro-reset-all" class="button" onclick="DupPro.Pack.ConfirmResetAll(); return false">
                    <i class="fas fa-redo fa-sm"></i> <?php echo DUP_PRO_U::__('Reset All Settings'); ?>
                </button>
                <p class="description">
                    <?php
                        DUP_PRO_U::esc_html_e("Reset all settings to their defaults.");
                        $tContent = __(
                            'Resets standard settings to defaults. Does not affect capabilities, license key, storage or schedules.',
                            'duplicator-pro'
                        );
                        ?>
                    <i 
                        class="fas fa-question-circle fa-sm" 
                        data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Reset Settings"); ?>" 
                        data-tooltip="<?php echo esc_attr($tContent); ?>"
                    >
                    </i>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Packages"); ?></label></th>
            <td>
                <button class="button" onclick="DupPro.Pack.ConfirmResetPackages(); return false;">
                    <i class="fas fa-redo fa-sm"></i> <?php DUP_PRO_U::esc_attr_e('Reset Incomplete Packages'); ?>
                </button>
                <p class="description">
                    <?php DUP_PRO_U::esc_html_e("Reset all packages."); ?>
                    <i 
                        class="fas fa-question-circle fa-sm" 
                        data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Reset packages"); ?>" 
                        data-tooltip="<?php DUP_PRO_U::esc_attr_e('Delete all unfinished packages. So those with error and being created.'); ?>"
                    >
                    </i>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Foreign JavaScript"); ?></label></th>
            <td>
                <input
                    type="checkbox"
                    name="_unhook_third_party_js"
                    id="_unhook_third_party_js"
                    value="1"
                    <?php checked($global->unhook_third_party_js); ?>
                >
                <label for="_unhook_third_party_js"><?php DUP_PRO_U::esc_html_e("Disable"); ?></label> <br />
                <p class="description">
                    <?php
                    DUP_PRO_U::esc_html_e("Check this option if JavaScript from the theme or other plugins conflicts with Duplicator Pro pages.");
                    ?>
                    <br>
                    <?php
                    DUP_PRO_U::esc_html_e("Do not modify this setting unless you know the expected result or have talked to support.");
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Foreign CSS"); ?></label></th>
            <td>
                <input
                    type="checkbox"
                    name="_unhook_third_party_css"
                    id="unhook_third_party_css"
                    value="1"
                    <?php checked($global->unhook_third_party_css); ?>
                >
                <label for="unhook_third_party_css"><?php DUP_PRO_U::esc_html_e("Disable"); ?></label> <br />
                <p class="description">
                    <?php
                    DUP_PRO_U::esc_html_e("Check this option if CSS from the theme or other plugins conflicts with Duplicator Pro pages.");
                    ?>
                    <br>
                    <?php
                    DUP_PRO_U::esc_html_e("Do not modify this setting unless you know the expected result or have talked to support.");
                    ?>
                </p>
            </td>
        </tr>
    </table>
    <p>
        <input 
            type="submit" name="submit" id="submit" 
            class="button-primary" 
            value="<?php DUP_PRO_U::esc_attr_e('Save General Settings') ?>"
        >
    </p>
</form>

<?php
$resetSettingsDialog                 = new DUP_PRO_UI_Dialog();
$resetSettingsDialog->title          = DUP_PRO_U::__('Reset Settings?');
$resetSettingsDialog->message        = DUP_PRO_U::__('Are you sure you want to reset settings to defaults?');
$resetSettingsDialog->progressText   = DUP_PRO_U::__('Resetting settings, Please Wait...');
$resetSettingsDialog->jsCallback     = 'DupPro.Pack.ResetAll()';
$resetSettingsDialog->progressOn     = false;
$resetSettingsDialog->okText         = DUP_PRO_U::__('Yes');
$resetSettingsDialog->cancelText     = DUP_PRO_U::__('No');
$resetSettingsDialog->closeOnConfirm = true;
$resetSettingsDialog->initConfirm();

$resetPackagesDialog                 = new DUP_PRO_UI_Dialog();
$resetPackagesDialog->title          = DUP_PRO_U::__('Reset Packages ?');
$resetPackagesDialog->message        = DUP_PRO_U::__('This will clear and reset all of the current temporary packages.  Would you like to continue?');
$resetPackagesDialog->progressText   = DUP_PRO_U::__('Resetting settings, Please Wait...');
$resetPackagesDialog->jsCallback     = 'DupPro.Pack.ResetPackages()';
$resetPackagesDialog->progressOn     = false;
$resetPackagesDialog->okText         = DUP_PRO_U::__('Yes');
$resetPackagesDialog->cancelText     = DUP_PRO_U::__('No');
$resetPackagesDialog->closeOnConfirm = true;
$resetPackagesDialog->initConfirm();

$msg_ajax_error                 = new DUP_PRO_UI_Messages(
    DUP_PRO_U::__('AJAX ERROR!') . '<br>' . __('Ajax request error', 'duplicator-pro'),
    DUP_PRO_UI_Messages::ERROR
);
$msg_ajax_error->hide_on_init   = true;
$msg_ajax_error->is_dismissible = true;
$msg_ajax_error->initMessage();

$msg_response_error                 = new DUP_PRO_UI_Messages(DUP_PRO_U::__('RESPONSE ERROR!'), DUP_PRO_UI_Messages::ERROR);
$msg_response_error->hide_on_init   = true;
$msg_response_error->is_dismissible = true;
$msg_response_error->initMessage();

$msg_response_success                 = new DUP_PRO_UI_Messages('', DUP_PRO_UI_Messages::NOTICE);
$msg_response_success->hide_on_init   = true;
$msg_response_success->is_dismissible = true;
$msg_response_success->initMessage();
?>

<script>
    jQuery(document).ready(function($) {

        // which: 0=installer, 1=archive, 2=sql file, 3=log
        DupPro.Pack.DownloadTraceLog = function() {
            var actionLocation = ajaxurl + '?action=duplicator_pro_get_trace_log&nonce=' + '<?php echo wp_create_nonce('duplicator_pro_get_trace_log'); ?>';
            location.href = actionLocation;
        };

        DupPro.Pack.ConfirmResetAll = function() {
            <?php $resetSettingsDialog->showConfirm(); ?>
        };

        DupPro.Pack.ConfirmResetPackages = function() {
            <?php $resetPackagesDialog->showConfirm(); ?>
        };

        DupPro.Pack.ResetAll = function() {
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'duplicator_pro_reset_user_settings',
                    nonce: '<?php echo wp_create_nonce('duplicator_pro_reset_user_settings'); ?>'
                },
                success: function(result) {
                    if (result.success) {
                        var message = '<?php DUP_PRO_U::_e('Settings successfully reset'); ?>';
                        <?php
                        $msg_response_success->updateMessage('message');
                        $msg_response_success->showMessage();
                        ?>
                    } else {
                        var message = '<?php DUP_PRO_U::_e('RESPONSE ERROR!'); ?>' + '<br><br>' + result.data.message;
                        <?php
                        $msg_response_error->updateMessage('message');
                        $msg_response_error->showMessage();
                        ?>
                    }
                },
                error: function(result) {
                    <?php $msg_ajax_error->showMessage(); ?>
                }
            });
        };

        DupPro.Pack.ResetPackages = function() {
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'duplicator_pro_reset_packages',
                    nonce: '<?php echo wp_create_nonce('duplicator_pro_reset_packages'); ?>'
                },
                success: function(result) {
                    if (result.success) {
                        var message = '<?php DUP_PRO_U::_e('Packages successfully reset'); ?>';
                        <?php
                        $msg_response_success->updateMessage('message');
                        $msg_response_success->showMessage();
                        ?>
                    } else {
                        var message = '<?php DUP_PRO_U::_e('RESPONSE ERROR!'); ?>' + '<br><br>' + result.data.message;
                        <?php
                        $msg_response_error->updateMessage('message');
                        $msg_response_error->showMessage();
                        ?>
                    }
                },
                error: function(result) {
                    <?php $msg_ajax_error->showMessage(); ?>
                }
            });
        };

        //Init
        $("#_trace_log_enabled").click(function() {
            $('#_send_trace_to_error_log').attr('disabled', !$(this).is(':checked'));
        });

    });
</script>
