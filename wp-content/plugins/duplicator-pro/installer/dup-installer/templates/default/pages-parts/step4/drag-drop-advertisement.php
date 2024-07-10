<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$archiveConfig = DUPX_ArchiveConfig::getInstance();
if ($archiveConfig->brand->isDefault) :
    ?>
<p class="text-center margin-top-2 margin-bottom-2">
    <b>
        Next time try "<a target='_blank' href='https://duplicator.com/how-to-migrate-wordpress-site/'>
            Drag and Drop
        </a>" 
        for a rapid install! 
    </b>
</p>
<?php endif; ?>
