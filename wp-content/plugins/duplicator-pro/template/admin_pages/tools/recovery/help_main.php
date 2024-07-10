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
<h3><?php _e('Recovery Point Description', 'duplicator-pro'); ?></h3>
<p>
    <?php _e(
        'The Recovery Point is a special package that allows one to quickly revert the system should ' .
        'it become corrupted during a maintenance operation such as a plugin/theme update or an experimental file change.',
        'duplicator-pro'
    ); ?>
</p>
<p>
    <?php _e(
        'The advantage of setting a Recovery Point is that you can very quickly restore a backup ' .
        'without having to worry about uploading a package and setting the parameters such as database credentials or site paths.',
        'duplicator-pro'
    ); ?>
</p>


<h3><?php _e('Using the Recovery Point', 'duplicator-pro'); ?></h3>
<p>
    <?php _e(
        'There can only be a single Recovery Point defined at any one time and must be associated ' .
        'with a package that retains all WordPress core files and all database tables.',
        'duplicator-pro'
    ); ?>
</p>
<p>
    <?php _e(
        'When you set a Recovery Point, the chosen package is prepared and a special URL (the "Recovery URL") is generated.',
        'duplicator-pro'
    ); ?>
</p>
<p>
    <?php _e(
        'The Recovery URL in turn, is used to launch a streamlined installer which will restore the system quickly in the event of a system catastrophe.',
        'duplicator-pro'
    ); ?>
</p>
<h3><?php _e('More Information', 'duplicator-pro'); ?></h3>
<p>
    <?php _e('For detailed information on the Recovery point see the additional sections of this help as well as the ', 'duplicator-pro');
    echo "<a class='dup-recovery-point-guide-link' href='" . DUPLICATOR_PRO_RECOVERY_GUIDE_URL . "' target='" . DUPLICATOR_PRO_HELP_TARGET . "'>";
    _e('Recovery Point Guide', 'duplicator-pro');
    echo '</a>';
    ?>
</p>
