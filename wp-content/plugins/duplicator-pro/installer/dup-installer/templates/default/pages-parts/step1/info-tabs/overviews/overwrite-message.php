<?php

/**
 *
 * @package templates/default
 */

$overwriteMode = (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL);
?>


<?php if ($overwriteMode) { ?>
    <div class="overview-subtxt-2 requires-db-hide">
        This will clear all site data and the current package will be installed.  This process cannot be undone!
    </div>
<?php } ?>
