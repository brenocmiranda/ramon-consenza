<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Installer\Package;

use Exception;

class PComponents
{
    const COMP_DB             = 'package_component_db';
    const COMP_CORE           = 'package_component_core';
    const COMP_PLUGINS        = 'package_component_plugins';
    const COMP_PLUGINS_ACTIVE = 'package_component_plugins_active';
    const COMP_THEMES         = 'package_component_themes';
    const COMP_THEMES_ACTIVE  = 'package_component_themes_active';
    const COMP_UPLOADS        = 'package_component_uploads';
    const COMP_OTHER          = 'package_component_other';

    const COMPONENTS = [
        self::COMP_DB,
        self::COMP_CORE,
        self::COMP_PLUGINS,
        self::COMP_PLUGINS_ACTIVE,
        self::COMP_THEMES,
        self::COMP_THEMES_ACTIVE,
        self::COMP_UPLOADS,
        self::COMP_OTHER
    ];

    const COMPONENTS_DEFAULT = [
        self::COMP_DB,
        self::COMP_CORE,
        self::COMP_PLUGINS,
        self::COMP_THEMES,
        self::COMP_UPLOADS,
        self::COMP_OTHER
    ];

    const SUB_OPTIONS = [
        self::COMP_PLUGINS_ACTIVE,
        self::COMP_THEMES_ACTIVE
    ];

    /**
     * Get label by compoentent
     *
     * @param string $component The component
     *
     * @return string
     */
    public static function getLabel($component)
    {
        switch ($component) {
            case self::COMP_DB:
                return 'Database';
            case self::COMP_CORE:
                return 'Core';
            case self::COMP_PLUGINS:
                return 'Plugins';
            case self::COMP_PLUGINS_ACTIVE:
                return 'Only Active Plugins';
            case self::COMP_THEMES:
                return 'Themes';
            case self::COMP_THEMES_ACTIVE:
                return 'Only Active Themes';
            case self::COMP_UPLOADS:
                return 'Media';
            case self::COMP_OTHER:
                return 'Other';
            default:
                throw new Exception('Invalid component: ' . $component);
        }
    }

    /**
     * Returns the component labels imploded by seperator
     *
     * @param string[] $components array of components
     * @param string   $seperator  the seperator string
     *
     * @return string
     */
    public static function displayComponentsList($components, $seperator = ', ')
    {
        return implode($seperator, array_map(function ($component) {
            return self::getLabel($component);
        }, $components));
    }

    /**
     * Returns true if this is a DB only package
     *
     * @param string[] $components active components
     *
     * @return bool
     */
    public static function isDBOnly($components)
    {
        return count($components) === 1 && in_array(self::COMP_DB, $components);
    }

    /**
     * Returns true if the DB package component has been excluded
     *
     * @param string[] $activeComponents list of active components
     *
     * @return bool
     */
    public static function isDBExcluded($activeComponents)
    {
        return !in_array(self::COMP_DB, $activeComponents);
    }
}
