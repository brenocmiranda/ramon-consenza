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
<br/><br/>
<div id='dpro-error' class="error">
    <p>
        <?php echo sprintf(
            __(
                "Unable to find package id %d.  The package does not exist or was deleted.",
                'duplicator-pro'
            ),
            $tplData['packageId']
        ); ?>
        <br/>
    </p>
</div>