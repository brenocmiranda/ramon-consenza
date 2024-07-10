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
 * @var DUP_PRO_Package $package
 */

$package             = $tplData['package'];
$current_tab         = $tplData['current_tab'];
$enable_transfer_tab = (
    $package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Installer) !== false &&
    $package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Archive) !== false
);

?>
<h2 class="nav-tab-wrapper">  
    <a 
        href="?page=duplicator-pro&action=detail&tab=detail&id=<?php echo $package->ID ?>" 
        class="nav-tab <?php echo ($current_tab == 'detail') ? 'nav-tab-active' : '' ?>"
    > 
        <?php DUP_PRO_U::esc_html_e('Details'); ?>
    </a> 
    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
    <a <?php if ($enable_transfer_tab === false) {
        echo 'onclick="DupPro.Pack.TransferDisabled(); return false;"';
       } ?> href="?page=duplicator-pro&action=detail&tab=transfer&id=<?php echo $package->ID; ?>" 
       class="nav-tab <?php echo ($current_tab == 'transfer') ? 'nav-tab-active' : '' ?>"> 
        <?php DUP_PRO_U::esc_html_e('Transfer'); ?>
    </a>    
    <?php } ?>  
</h2>
<div class="dup-details-packages-list">
    <a href="admin.php?page=duplicator-pro">[<?php DUP_PRO_U::esc_html_e('Packages'); ?>]</a>
</div>

<?php
if ($package->Status == DUP_PRO_PackageStatus::ERROR) {
    $err_link_pack   = $package->get_log_url();
    $err_link_log    = "<a target='_blank' href=\"$err_link_pack\">" . DUP_PRO_U::__('package log') . '</a>';
    $err_link_faq    = '<a target="_blank" href="' . DUPLICATOR_PRO_TECH_FAQ_URL . '">' . DUP_PRO_U::__('FAQ pages') . '</a>';
    $err_link_ticket = '<a target="_blank" href="' . DUPLICATOR_PRO_BLOG_URL . 'my-account/support/">' . DUP_PRO_U::__('help ticket') . '</a>';
    ?>
<div id='dpro-error' class="error">
    <p>
        <b><?php echo DUP_PRO_U::__('Error encountered building package, please review ') . $err_link_log . DUP_PRO_U::__(' for details.')  ; ?> </b>
        <br/>
        <?php echo DUP_PRO_U::__('For more help read the ') . $err_link_faq . DUP_PRO_U::__(' or submit a ') . $err_link_ticket; ?>.
    </p>
</div>
    <?php
}

$alertTransferDisabled          = new DUP_PRO_UI_Dialog();
$alertTransferDisabled->title   = DUP_PRO_U::__('Transfer Error');
$alertTransferDisabled->message = DUP_PRO_U::__('No package in default location so transfer is disabled.');
$alertTransferDisabled->initAlert();
?>
<script>
    DupPro.Pack.TransferDisabled = function() {
        <?php $alertTransferDisabled->showAlert(); ?>
    }
</script>