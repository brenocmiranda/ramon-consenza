<?php

/**
 * Duplicator package row in table packages list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Addons\ProBase\License\License;
use Duplicator\Package\Create\BuildComponents;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$activeComponents        = isset($tplData['components']) ? $tplData['components'] : [];
$archiveFilterOn         = isset($tplData['archiveFilterOn']) ? $tplData['archiveFilterOn'] : false;
$archiveFilterDirs       = isset($tplData['archiveFilterDirs']) ? $tplData['archiveFilterDirs'] : '';
$archiveFilterFiles      = isset($tplData['archiveFilterFiles']) ? $tplData['archiveFilterFiles'] : '';
$archiveFilterPaths      = strlen($archiveFilterDirs) > 0 && strlen($archiveFilterFiles) > 0
    ? $archiveFilterDirs  . ';' . $archiveFilterFiles : $archiveFilterDirs . $archiveFilterFiles;
$archiveFilterPaths      = str_replace(';', ";\n", $archiveFilterPaths);
$archiveFilterExtensions = isset($tplData['archiveFilterExtensions']) ? $tplData['archiveFilterExtensions'] : '';
$licenseDisabledClass    = License::can(License::CAPABILITY_PACKAGE_COMPONENTS_PLUS) ? '' : 'disabled';

$pathFiltersTooltip     = __("File filters allow you to exclude files and folders from the package. To enable path and extension filters check the " .
    "checkbox. Enter the full path of the files and folders you want to exclude from the package as a semicolon (;) seperated list.", 'duplicator-pro');
$extensionFilterTooltip = __("File extension filters allow you to exclude files with certain file extensions from the package e.g. zip;rar;pdf etc. " .
    "Enter the file extensions you want to exclude from the package as a semicolon (;) seperated list.", 'duplicator-pro');
?>

<div class="dup-package-components">
    <div class="component-section">
        <div class="section-title">
            <span>
                <?php _e('Components', 'duplicator-pro'); ?> 
                <i class="fas fa-question-circle fa-sm" 
                    data-tooltip-title="<?php _e('Package Components', 'duplicator-pro'); ?>" 
                    data-tooltip="<?php $tplMng->render('parts/filters/package_components_tootip'); ?>" 
                    aria-expanded="false"></i>
            </span>
        </div>
        <div class="components-shortcut-select">
            <div class="dup-radio-button-group-wrapper" >
                <?php foreach (BuildComponents::COMPONENTS_ACTIONS as $action) {
                    $inputId = 'dup-component-shortcut-action-' . $action;
                    ?>
                    <input 
                        type="radio"
                        id="<?php echo $inputId; ?>"
                        name="auto-select-components"
                        class="dup-components-shortcut-radio"
                        value="<?php echo esc_html($action); ?>"
                        <?php checked($action === BuildComponents::getActionFromComponents($activeComponents));  ?>
                    >
                    <label 
                        <?php echo in_array($action, BuildComponents::COMPONENTS_PLUS_ACTIONS) ? "class=\"{$licenseDisabledClass}\"" : ''; ?>
                        for="<?php echo $inputId; ?>"
                    >
                        <?php echo esc_html(BuildComponents::getActionLabel($action)); ?>
                    </label>
                <?php } ?>
            </div>
        </div>
        <ul class="custom-components-select">
            <?php foreach (BuildComponents::COMPONENTS as $component) { ?>
                <li>
                    <label class="<?php echo in_array($component, BuildComponents::SUB_OPTIONS) ? 'secondary license-disabled' : 'license-disabled' ?>">
                        <input
                                type="checkbox"
                                <?php echo !in_array($component, BuildComponents::SUB_OPTIONS) ? 'data-parsley-multiple="package_components"' : '' ?>
                                name="<?php echo $component ?>"
                                id="<?php echo $component ?>"
                                class="dup-components-checkbox"
                                <?php checked(in_array($component, $activeComponents));  ?>
                        > <?php echo esc_html(BuildComponents::getLabel($component)); ?>
                    </label>
                </li>
            <?php } ?>
        </ul>
        <span id="components_error_container" class="duplicator-error-container" ></span>
    </div>
    <div class="filter-section">
        <div class="filters">
            <div class="section-title">
            <span><?php _e('Filters', 'duplicator-pro'); ?> (&nbsp;<label>
                    <input id="filter-on"
                           name="filter-on"
                           type="checkbox" <?php checked($archiveFilterOn); ?>
                    > Enable
                </label>&nbsp;)
                <i 
                    class="fas fa-question-circle fa-sm" 
                    data-tooltip-title="<?php _e('File Filters', 'duplicator-pro'); ?>" 
                    data-tooltip="<?php echo $pathFiltersTooltip; ?>"" 
                    aria-expanded="false"
                >
                </i>
            </span>
                <div class="filter-links">
                    <a href="#" data-filter-path="<?php echo SnapIO::trailingslashit(DUP_PRO_Archive::getOriginalPaths('home')); ?>">[root path]</a>
                    <a href="#" data-filter-path="<?php echo SnapIO::trailingslashit(DUP_PRO_Archive::getOriginalPaths('wpcontent')); ?>">[wp-content]</a>
                    <a href="#" data-filter-path="<?php echo SnapIO::trailingslashit(DUP_PRO_Archive::getOriginalPaths('uploads')); ?>">[wp-uploads]</a>
                    <a href="#" data-filter-path="<?php echo SnapIO::trailingslashit(DUP_PRO_Archive::getOriginalPaths('wpcontent')) . 'cache/'; ?>">[cache]</a>
                    <a href="#" id="clear-path-filters">(clear)</a>
                </div>
            </div>
            <textarea
                    id="filter-paths"
                    name="filter-paths"
                    placeholder="/full_path/dir/;&#10;/full_path/file;"
                    readonly><?php echo $archiveFilterPaths; ?></textarea>
            <div class="section-title">
            <span>
                <?php _e('File Extensions', 'duplicator-pro'); ?>
                <i class="fas fa-question-circle fa-sm" 
                    data-tooltip-title="<?php _e('File Extensions', 'duplicator-pro'); ?>" 
                    data-tooltip="<?php echo $extensionFilterTooltip; ?>"
                    aria-expanded="false"></i>
            </span>
                <div class="filter-links">
                    <a href="#" data-filter-exts="avi;mov;mp4;mpeg;mpg;swf;wmv;aac;m3u;mp3;mpa;wav;wma">[media]</a>
                    <a href="#" data-filter-exts="zip;rar;tar;gz;bz2;7z">[archive]</a>
                    <a href="#" id="clear-extension-filters">(clear)</a>
                </div>
            </div>
            <textarea id="filter-exts" name="filter-exts" placeholder="ext1;ext2;ext3;" readonly><?php echo $archiveFilterExtensions; ?></textarea>
        </div>
        <div class="db-only-message">
            <?php
            echo wp_kses(
                DUP_PRO_U::__(
                    "<b>Overview:</b><br> This advanced option excludes all files from the archive.  Only the database and a copy of the installer.php "
                    . "will be included in the archive.zip file. The option can be used for backing up and moving only the database."
                ),
                array(
                    'b' => array(),
                    'br' => array(),
                )
            );
            echo '<br/><br/>';

            echo wp_kses(
                DUP_PRO_U::__(
                    "<b><i class='fa fa-exclamation-circle'></i> Notice:</b><br/> "
                    . "Installing only the database over an existing site may have unintended consequences.  "
                    . "Be sure to know the state of your system before installing the database without the associated files.  "
                ),
                array(
                    'b' => array(),
                    'i' => array('class'),
                    'br' => array()
                )
            );

            DUP_PRO_U::esc_html_e(
                "For example, if you have WordPress 5.6 on this site and you copy this site's database to a host that has WordPress 5.8 files "
                . "then the source code of the files  will not be in sync with the database causing possible errors. "
                . "This can also be true of plugins and themes.  "
                . "When moving only the database be sure to know the database will be compatible with "
                . "ALL source code files. Please use this advanced feature with caution!"
            );

            echo '<br/><br/>';

            echo wp_kses(
                DUP_PRO_U::__("<b>Install Time:</b><br> When installing a database only package please visit the "),
                array(
                    'b' => array(),
                    'br' => array(),
                )
            );
            ?>
            <a href="<?php echo DUPLICATOR_PRO_DUPLICATOR_DOCS_URL; ?>database-install" target="_blank">
                <?php DUP_PRO_U::esc_html_e('database only quick start'); ?>
            </a>
        </div>
    </div>
</div>
<div class="dup-tabs-opts-help">
    <?php
    esc_html_e("The directories, extensions and files above will be be exclude from the archive file if enable is checked.", "duplicator-pro");
    echo "<br/>";
    esc_html_e("Use full path for directories or specific files.", "duplicator-pro");
    echo " <b>";
    esc_html_e("Use filenames without paths to filter same-named files across multiple directories.", "duplicator-pro");
    echo "</b>";
    echo "<br/>";
    esc_html_e("Use semicolons to separate all items. Use # to comment a line.", "duplicator-pro");

    if (!License::can(License::CAPABILITY_PACKAGE_COMPONENTS_PLUS)) { ?>
        <div id="dup-upgrade-license-info" class="margin-top-1" >
            <?php
            printf(
                _x(
                    'The <b>%1$s</b> and <b>%2$s</b> options are not included in your license plan.',
                    '%1$s and %2$s are the options not included in the license.',
                    'duplicator-pro'
                ),
                BuildComponents::getActionLabel(BuildComponents::COMP_ACTION_MEDIA),
                BuildComponents::getActionLabel(BuildComponents::COMP_ACTION_CUSTOM)
            );
            ?>
            <br>
            <?php
            printf(
                _x(
                    'To enable advanced options please %1$supgrade your license%2$s.',
                    '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link.',
                    'duplicator-pro'
                ),
                '<a href="' . License::getUpsellURL() . '" target="_blank">',
                '</a>'
            );
            ?>
        </div>
    <?php } ?>
</div>
<script>
    jQuery(document).ready(function($)
    {
        $('#package_component_db').attr('data-parsley-required', 'true');
        $('#package_component_db').attr('data-parsley-mincheck', '1');
        $('#package_component_db').attr('data-parsley-errors-container', '#components_error_container');
        $('#package_component_db').attr('data-parsley-required-message', '<?php _e("Please select at least one component.", "duplicator-pro"); ?>');

        $('.dup-package-components .component-section input[type=checkbox]').on('change', function () {
            $('#package_component_db').parsley().validate();
        });

        DupPro.Pack.ToggleFileFilters = function ()
        {
            if ($("#filter-on").is(':checked')) {
                $('#dup-archive-filter-file').show();
                $('#filter-exts, #filter-paths').prop('readonly', false);
            } else {
                $('#dup-archive-filter-file').hide();
                $('#filter-exts, #filter-paths').prop('readonly', true);
            }
        };

        DupPro.Pack.ToggleDBExcluded = function () {
            if (!$('#package_component_db').is(":checked")) {
                $('.filter-db-tab').hide()
            } else {
                $('.filter-db-tab').show()
            }
        }

        DupPro.Pack.ToggleDBOnly = function () {
            let allComponentCheckboxes = $('.dup-package-components .component-section input[type=checkbox]');
            if (allComponentCheckboxes.filter(':checked').length === 1 && $('#package_component_db').is(":checked")) {
                $("#dup-archive-db-only").show();
                $('.filters').hide();
                $('.db-only-message').show();
            } else {
                $("#dup-archive-db-only").hide();
                $('.filters').show();
                $('.db-only-message').hide();
            }
        }

        DupPro.Pack.ToggleMediaOnly = function () {
            let allComponentCheckboxes = $('.dup-package-components .component-section input[type=checkbox]');
            if (allComponentCheckboxes.filter(':checked').length === 1 && $('#package_component_uploads').is(":checked")) {
                $("#dup-archive-media-only").show();
            } else {
                $("#dup-archive-media-only").hide();
            }
        }

        DupPro.Pack.SetDBOnly = function () {
            $('.dup-components-checkbox').prop('checked', false);
            $('.custom-components-select label').addClass('disabled');
            $('#package_component_db').prop('checked', true);
        }

        DupPro.Pack.SetMediaOnly = function () {
            $('.dup-components-checkbox').prop('checked', false);
            $('.custom-components-select label').addClass('disabled');
            $('#package_component_uploads').prop('checked', true);
        }

        DupPro.Pack.SetDefault = function () {
            $('.dup-components-checkbox').prop('checked', false);
            $('label:not(.secondary) .dup-components-checkbox').prop('checked', true);
            $('.custom-components-select label').addClass('disabled');
        }

        DupPro.Pack.SetCustom = function () {
            $('.custom-components-select label').removeClass('disabled');
        }

        DupPro.Pack.ToggleComponentsSelect = function () {
            let checkedSelect = $('.dup-components-shortcut-radio:checked').val();
            console.log(checkedSelect);
            switch (checkedSelect) {
                case 'all':
                    DupPro.Pack.SetDefault();
                    break;
                case 'database':
                    DupPro.Pack.SetDBOnly();
                    break;
                case 'media':
                    DupPro.Pack.SetMediaOnly();
                    break;
                case 'custom':
                    DupPro.Pack.SetCustom();
                    break;
            }

            $('.dup-components-checkbox').trigger('change');
        }


        DupPro.Pack.SetComponentsSelect = function () {
            let allComponentCheckboxes = $('.dup-components-checkbox');
            let checkedComponentsCount = allComponentCheckboxes.filter(':checked').length; 
            let activeOnlyDisabled     = (!$('#package_component_plugins_active').is(":checked") && !$('#package_component_themes_active').is(":checked"));

            if ( checkedComponentsCount === (allComponentCheckboxes.length - 2) && activeOnlyDisabled) {
                $('#dup-component-shortcut-action-all').prop('checked', true);
            } else if (checkedComponentsCount === 1 && $('#package_component_db').is(":checked")) {
                $('#dup-component-shortcut-action-database').prop('checked', true);
            } else if (checkedComponentsCount === 1 && $('#package_component_uploads').is(":checked")) {
                $('#dup-component-shortcut-action-media').prop('checked', true);
            } else {
                $('#dup-component-shortcut-action-custom').prop('checked', true);
            }

            DupPro.Pack.ToggleComponentsSelect();
        }
        
        DupPro.Pack.ToggleActivePlugins = function () {
            let activePluginsInput = $('#package_component_plugins_active'); 

            if (activePluginsInput.parent().hasClass('disabled')) {
                activePluginsInput.prop('disabled', true);
                return; 
            }

            if (!$('#package_component_plugins').is(':checked')) {
                activePluginsInput.prop('disabled', true);
                activePluginsInput.prop('checked', false);
            } else {
                activePluginsInput.prop('disabled', false);
            }
        }

        DupPro.Pack.ToggleActiveThemes = function () {
            let activeThemesInput = $('#package_component_themes_active'); 

            if (activeThemesInput.parent().hasClass('disabled')) {
                activeThemesInput.prop('disabled', true);
                return; 
            }

            if (!$('#package_component_themes').is(':checked')) {
                activeThemesInput.prop('disabled', true);
                activeThemesInput.prop('checked', false);
            } else {
                activeThemesInput.prop('disabled', false);
            }
        }

        DupPro.Pack.ToggleFileFilters();
        DupPro.Pack.ToggleActiveThemes();
        DupPro.Pack.ToggleActivePlugins();
        DupPro.Pack.ToggleDBExcluded();
        DupPro.Pack.ToggleDBOnly()
        DupPro.Pack.SetComponentsSelect();

        $('a[data-filter-path]').click(function (e) {
            e.preventDefault()

            if ($('#filter-paths').is("[readonly]")) {
                return;
            }

            let currentVal = $('#filter-paths').val()
            let newVal = currentVal.length > 0 ? currentVal + ";\n" + $(this).data('filter-path') : $(this).data('filter-path')

            $('#filter-paths').val(newVal)
        })

        $('#clear-path-filters').click(function (e) {
            e.preventDefault()

            if ($('#filter-paths').is("[readonly]")) {
                return;
            }

            $('#filter-paths').val('')
        })

        $('a[data-filter-exts]').click(function (e) {
            e.preventDefault()

            if ($('#filter-exts').is("[readonly]")) {
                return;
            }

            let currentVal = $('#filter-exts').val()
            let newVal = currentVal.length > 0 ? currentVal + ";" + $(this).data('filter-exts') : $(this).data('filter-exts')

            $('#filter-exts').val(newVal)
        })

        $('#clear-extension-filters').click(function (e) {
            e.preventDefault()

            if ($('#filter-exts').is("[readonly]")) {
                return;
            }

            $('#filter-exts').val('')
        })

        $('#filter-on').change(DupPro.Pack.ToggleFileFilters)

        $('#package_component_db').change(function () {
            DupPro.Pack.ToggleDBExcluded();
        })

        $('.dup-components-shortcut-radio').change(function () {
            DupPro.Pack.ToggleComponentsSelect();
        })

        $('.dup-components-checkbox').change(function(){
            DupPro.Pack.ToggleDBOnly()
            DupPro.Pack.ToggleMediaOnly()
        })

        $('#package_component_plugins').change(function () {
            DupPro.Pack.ToggleActivePlugins();
        });

        $('#package_component_themes').change(function () {
            DupPro.Pack.ToggleActiveThemes();
        });

    });
</script>
