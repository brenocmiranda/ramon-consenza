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

if (!isset($tplData['tmpCleanUpSuccess'])) {
    return;
}

$messageClasses = [
    'notice',
    'is-dismissible',
    'dpro-diagnostic-action-tmp-cache',
    'notice-success'
];

?>
<div id="message" class="<?php echo implode(' ', $messageClasses); ?>">
    <p>
        <?php _e('Build cache removed.', 'duplicator-pro'); ?>
    </p>
</div>
