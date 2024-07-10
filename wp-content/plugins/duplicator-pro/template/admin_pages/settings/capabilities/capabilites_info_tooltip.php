<?php

/**
 * @package Duplicator
 */

use Duplicator\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$info = $tplData['info'];
?>
<p>
    <?php echo esc_html($info['desc']); ?>
</p>
<?php if (strlen($info['parent']) > 0) { ?>
    <br>
    <?php _e('Parent', 'duplicator-pro'); ?>: <b><?php echo esc_html($tplData['pLabel']); ?></b>
<?php } ?>