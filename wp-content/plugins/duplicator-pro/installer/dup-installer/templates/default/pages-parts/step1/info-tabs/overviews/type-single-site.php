<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (DUPX_InstallerState::instTypeAvaiable(DUPX_InstallerState::INSTALL_SINGLE_SITE)) {
    $instTypeClass = 'install-type-' . DUPX_InstallerState::INSTALL_SINGLE_SITE;
} else {
    return;
}

$display = DUPX_InstallerState::getInstance()->isInstType(DUPX_InstallerState::INSTALL_SINGLE_SITE);
?>
<div class="overview-description <?php echo $instTypeClass . ($display ? '' : ' no-display'); ?>">
    <div class="details">
        <table>
            <tr>
                <td>Status:</td>
                <td>
                    <b>Install - Single Site</b>
                    <div class="overview-subtxt-1">
                        This will perform the installation of a single WordPress site.
                    </div>
                    <?php dupxTplRender('pages-parts/step1/info-tabs/overviews/overwrite-message'); ?>
                </td>
            </tr>
            <tr>
                <td>Mode:</td>
                <td>
                    <?php echo DUPX_InstallerState::getInstance()->getHtmlModeHeader(); ?>
                    <?php dupxTplRender('pages-parts/step1/info-tabs/overviews/no-db-actions-message'); ?>
                </td>
            </tr>
        </table>
    </div>
</div>
