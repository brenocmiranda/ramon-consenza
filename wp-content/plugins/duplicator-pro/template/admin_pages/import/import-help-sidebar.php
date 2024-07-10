<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

?>
<div class="dpro-screen-hlp-info"><b><?php DUP_PRO_U::esc_html_e('Resources'); ?>:</b> 
    <ul>
        <?php echo DUP_PRO_UI_Screen::getHelpSidebarBaseItems(); ?>
        <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
            <li>
                <i class='fas fa-cog'></i> <a href='admin.php?page=duplicator-pro-settings&tab=import'>
                    <?php DUP_PRO_U::esc_html_e('Import Settings'); ?>
                </a>
            </li>
        <?php } ?>
        <li>
            <i class='fas fa-mouse-pointer'></i> 
                <a href="<?php echo esc_url(DUPLICATOR_PRO_DRAG_DROP_GUIDE_URL); ?>" target="_sc-ddguide">
                <?php DUP_PRO_U::esc_html_e('Drag and Drop Guide'); ?>
            </a>
        </li>                
    </ul>
</div>
