<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Ajax;

use Duplicator\Ajax\AjaxWrapper;
use Duplicator\Core\Views\Notifications;

class ServicesNotifications extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init()
    {
        $this->addAjaxCall('wp_ajax_duplicator_notification_dismiss', 'setDissmisedNotifications');
    }

    /**
     * Dismiss notification
     *
     * @return bool
     */
    public static function dismissNotifications()
    {
        $id = sanitize_key($_POST['id']);
        return Notifications::dismiss($id);
    }

    /**
     * Set dismiss notification action
     *
     * @return void
     */
    public function setDissmisedNotifications()
    {
        AjaxWrapper::json(
            array(__CLASS__, 'dismissNotifications'),
            Notifications::DUPLICATOR_NOTIFICATION_NONCE_KEY,
            $_POST['nonce'],
            'manage_options'
        );
    }
}
