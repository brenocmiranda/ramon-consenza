<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!DUPX_InstallerState::isRecoveryMode()) {
    return;
}
$created     = DUPX_ArchiveConfig::getInstance()->created;
$packageLife = DUPX_ArchiveConfig::getInstance()->getPackageLife();
?>
<div class="overview-description recovery">
    <div class="details">
        <table>
            <tr>
                <td>Status:</td>
                <td>
                    <h2 class="overview-install-type">Recovery - <?php echo DUPX_InstallerState::installTypeToString(); ?></h2>
                    <div class="overview-subtxt-1">
                        Overwrite this site from the recovery point made on <b><?php echo $created; ?></b> [<?php echo $packageLife; ?> hour(s) old].
                    </div>
                    <?php dupxTplRender('pages-parts/step1/info-tabs/overviews/overwrite-message'); ?>
                </td>
            </tr>
            <tr>
                <td>Mode:</td>
                <td>Custom <i>(Recovery Install)</i></td>
            </tr>
        </table>
    </div>
</div>
