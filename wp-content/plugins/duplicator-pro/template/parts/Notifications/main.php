<?php
/**
 * Admin Notifications template.
 *
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

defined('ABSPATH') || exit;

?>
<div id="dup-notifications">
    <div class="dup-notifications-header">
        <div class="dup-notifications-bell">
        <img src="<?php echo DUPLICATOR_PRO_PLUGIN_URL; ?>assets/img/notification-bell.svg"/>
            <span class="wp-ui-notification dup-notifications-circle"></span>
        </div>
        <div class="dup-notifications-title"><?php esc_html_e('Notifications', 'duplicator-pro'); ?></div>
    </div>

    <div class="dup-notifications-body">
        <a class="dismiss" title="<?php esc_attr_e('Dismiss this message', 'duplicator-pro'); ?>"><i class="fa fa-times-circle" aria-hidden="true"></i></a>

        <?php if (count($tplData['notifications']) > 1) : ?>
            <div class="navigation">
                <a class="prev">
                    <span class="screen-reader-text"><?php esc_attr_e('Previous message', 'duplicator-pro'); ?></span>
                    <span aria-hidden="true">&lsaquo;</span>
                </a>
                <a class="next">
                    <span class="screen-reader-text"><?php esc_attr_e('Next message', 'duplicator-pro'); ?></span>
                    <span aria-hidden="true">&rsaquo;</span>
                </a>
            </div>
        <?php endif; ?>

        <div class="dup-notifications-messages">
            <?php foreach ($tplData['notifications'] as $notification) {
                $tplMng->render('parts/Notifications/single-message', $notification);
            } ?>
        </div>
    </div>
</div>
