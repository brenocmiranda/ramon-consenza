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

if (!isset($tplData['purgeOrphansSuccess'])) {
    return;
}

$messageClasses = [
    'notice',
    'is-dismissible',
    'dpro-diagnostic-action-purge-orphans',
    ($tplData['purgeOrphansSuccess'] ? 'notice-success' : 'notice-error')
];
?>
<div id="message" class="<?php echo implode(' ', $messageClasses); ?>">
    <p>
        <?php _e('Cleaned up orphaned package files!', 'duplicator-pro'); ?>
    </p>
    <?php
    foreach ($tplData['purgeOrphansFiles'] as $path => $deleted) {
        if ($deleted) {
            ?>
            <div class='success'>
                <i class='fa fa-check'></i> <?php echo esc_html($path); ?>
            </div>
        <?php } else { ?>
            <div class='failed'>
                <i class='fa fa-exclamation-triangle'></i> <?php echo esc_html($path); ?>
            </div>
            <?php
        }
    }
    ?>
    <p>
        <i>
            <?php DUP_PRO_U::esc_html_e('If any orphaned files didn\'t get removed then delete them manually') ?>. 
        </i>
    </p>
</div>
