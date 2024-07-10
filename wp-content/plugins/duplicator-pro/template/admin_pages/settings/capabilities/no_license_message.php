<?php

/**
 * @package Duplicator
 */

use Duplicator\Addons\ProBase\License\License;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

if (License::can(License::CAPABILITY_CAPABILITIES_MNG)) {
    return;
}
?>
<p id="dup-no-license-message" class="dup-border-left-red-notice" >
    <b>
        <?php
        _e(
            'The current license does not allow to manage the capabilities. ',
            'duplicator-pro'
        );
        ?>
    </b>
    <br>
    <?php
    if (License::getLicenseStatus() === License::STATUS_VALID) {
        printf(
            _x(
                'If you want to manage the capabilities please %1$supgrade your license%2$s.',
                '1: <a> tag, 2: </a> tag',
                'duplicator-pro'
            ),
            '<a href="' . esc_url(License::getUpsellURL()) . '" target="_blank">',
            '</a>'
        );
    } else {
        printf(
            _x(
                'If you want to manage the capabilities please %1$srenew your license%2$s.',
                '1: <a> tag, 2: </a> tag',
                'duplicator-pro'
            ),
            '<a href="' . esc_url(License::getUpsellURL()) . '" target="_blank">',
            '</a>'
        );
    }
    ?>
    <br><br>
    <?php
    _e(
        'It\'s possible to reset the capabilities to the default values with "Reset to default" button.',
        'duplicator-pro'
    );
    ?>

</p>