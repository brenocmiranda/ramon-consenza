<?php

/**
 * @package Duplicator
 */

namespace Duplicator\Views;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;

class ViewHelper
{
    /**
     * Display Duplicator Logo on all pages
     *
     * @return void
     */
    public static function adminLogoHeader()
    {
        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        TplMng::getInstance()->render('parts/admin-logo-header');
    }

    /**
     * Add class to all Duplicator Pages
     *
     * @param string $classes Body classes separated by space
     *
     * @return string
     */
    public static function addBodyClass($classes)
    {
        if (ControllersManager::getInstance()->isDuplicatorPage()) {
            $classes .= ' duplicator-page';
        }
        return $classes;
    }
}
