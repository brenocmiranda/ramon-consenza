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
?>
<p>
    <?php _e(
        '<b>CURL isn\'t enabled.</b> This module is far more reliable for remote communication.',
        'duplicator-pro'
    ); ?></b></br>
    <?php _e('A possible solution to the problem could be to activate it.', 'duplicator-pro'); ?></br>
    <?php
        printf(
            _x(
                'For detailed steps on how to enable cURL please see <b>Solution 3, Issue A</b> in %1$sthis FAQ Entry%2$s.</br>',
                '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                'duplicator-pro'
            ),
            '<a href="' . DUPLICATOR_PRO_DUPLICATOR_DOCS_URL . 'how-to-resolve-license-activation-issues/" target="_blank">',
            '</a>'
        );
        ?>
</p>
