<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

use Duplicator\Controllers\ImportPageController;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\ControllersManager;

$global = DUP_PRO_Global_Entity::getInstance();

?>
<form id="dup-settings-form" action="<?php echo ControllersManager::getCurrentLink(); ?>" method="post" data-parsley-validate>
    <?php $tplData['actions'][SettingsPageController::ACTION_IMPORT_SAVE_SETTINGS]->getActionNonceFileds(); ?>

    <h3 id="duplicator-pro-import-settings" class="title"><?php DUP_PRO_U::esc_html_e("Import Settings"); ?></h3>
    <hr size="1" />
    <table class="form-table margin-top-1">
        <tr>
            <th scope="row">
                <label for="input_import_chunk_size" ><?php DUP_PRO_U::esc_html_e("Upload Chunk Size"); ?></label>
            </th>
            <td >
                <select name="import_chunk_size" id="input_import_chunk_size" class="postform">
                    <?php foreach (ImportPageController::getChunkSizes() as $size => $label) { ?>
                        <option value="<?php echo $size; ?>" <?php selected($global->import_chunk_size, $size); ?>><?php echo esc_html($label); ?></option>
                    <?php } ?>
                </select>
                <p class="description">
                    <?php
                        _e("If you have issue uploading a package start with a lower size.  The connection size is from slowest to fastest.", 'duplicator-pro');
                    ?><br/>
                    <small>
                        <?php
                            _e("Note: This setting only applies to the 'Import File' option.", 'duplicator-pro');
                        ?>
                    </small>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="import_custom_path" ><?php DUP_PRO_U::esc_html_e("Import custom path"); ?></label>
            </th>
            <td >
                <input 
                    class="large" 
                    type="text" 
                    name="import_custom_path" 
                    id="input_import_custom_path" 
                    value="<?php echo esc_attr($global->import_custom_path); ?>" 
                    placeholder=""
                >
                <p class="description">
                    <?php
                    esc_html_e(
                        "Setting a custom path does not change the folder where packages are uploaded but adds a folder to check for packages list.",
                        'duplicator-pro'
                    );
                    ?>
                    <br>
                    <?php
                    esc_html_e(
                        "This can be useful when you want to manually upload packages to another location " .
                        "which can also be a local storage of current or other site.",
                        'duplicator-pro'
                    );
                    ?>
                </p>
            </td>
        </tr>
    </table>

    <h3 class="title"><?php DUP_PRO_U::esc_html_e('Recovery') ?> </h3>
    <hr size="1" />

    <table class="form-table margin-top-1">
        <tr>
            <th scope="row">
                <label for="input_recovery_custom_path" ><?php DUP_PRO_U::esc_html_e("Recovery custom path"); ?></label>
            </th>
            <td>
                <input 
                    class="large" 
                    type="text" 
                    name="recovery_custom_path" 
                    id="input_recovery_custom_path" 
                    value="<?php echo esc_attr($global->getRecoveryCustomPath()); ?>" 
                    placeholder=""
                >
                <p class="description">
                    <?php
                    esc_html_e(
                        "Setting a custom path changes the location the recovery points are generated.",
                        'duplicator-pro'
                    );
                    ?>
                </p>
            </td>
        </tr>
    </table>

    <p class="submit dpro-save-submit">
        <input 
            type="submit" 
            name="submit" 
            id="submit" 
            class="button-primary" 
            value="<?php DUP_PRO_U::esc_attr_e('Save Import Settings') ?>" 
            style="display: inline-block;" 
        >
    </p>
</form>