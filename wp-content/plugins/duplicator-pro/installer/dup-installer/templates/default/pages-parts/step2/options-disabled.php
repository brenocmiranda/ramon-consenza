<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<p>
    Database actions are disabled. 
    <?php if (DUPX_ArchiveConfig::getInstance()->isDBExcluded()) : ?>
        The database was excluded during the build of the package.
    <?php endif; ?>
</p>
