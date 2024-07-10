<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

class DUPX_Validation_test_db_excluded extends DUPX_Validation_abstract_item
{
    protected function runTest()
    {
        if (!DUPX_InstallerState::dbDoNothing()) {
             return self::LV_PASS;
        }

        DUPX_Validation_database_service::getInstance()->setSkipOtherTests();

        return self::LV_HARD_WARNING;
    }

    public function getTitle()
    {
        return 'Extract only files';
    }

    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-excluded', array(
            'dbExcluded'   => DUPX_ArchiveConfig::getInstance()->isDBExcluded(),
            'isOk'         => false,
        ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-excluded', array(
            'dbExcluded'   => false,
            'isOk'         => true,
        ), false);
    }
}
