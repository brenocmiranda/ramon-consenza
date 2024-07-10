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

switch ($tplData['viewMode']) {
    case ImportPageController::VIEW_MODE_ADVANCED:
        $viewModeClass = 'view-list-item';
        break;
    case ImportPageController::VIEW_MODE_BASIC:
    default:
        $viewModeClass = 'view-single-item';
        break;
}

?>
<div id="dpro-pro-import-available-packages" class="<?php echo $viewModeClass; ?>" >
    <table class="dup-import-avail-packs packages-list">
        <thead>
            <tr>
                <th class="name"><?php DUP_PRO_U::esc_html_e("Archives"); ?></th>
                <th class="size"><?php DUP_PRO_U::esc_html_e("Size"); ?></th>
                <th class="created"><?php DUP_PRO_U::esc_html_e("Created"); ?></th>
                <th class="funcs"><?php DUP_PRO_U::esc_html_e("Status"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $importObjs = DUP_PRO_Package_Importer::getArchiveObjects();
            if (count($importObjs) === 0) {
                $tplMng->render('admin_pages/import/step1/package-row-no-found');
            } else {
                foreach ($importObjs as $importObj) {
                    $tplMng->render(
                        'admin_pages/import/step1/package-row',
                        array(
                            'importObj' => $importObj,
                            'idRow'     => ''
                        )
                    );
                }
            }
            ?>
        </tbody>
    </table>
    <div class="no_display" >
        <table id="dup-pro-import-available-packages-templates">
            <?php
            $tplMng->render(
                'admin_pages/import/step1/package-row',
                array(
                    'importObj' => null,
                    'idRow'     => 'dup-pro-import-row-template'
                )
            );
            $tplMng->render('admin_pages/import/step1/package-row-no-found');
            ?>
        </table>
    </div>
</div>