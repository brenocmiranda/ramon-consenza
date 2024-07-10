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

use Duplicator\Installer\Models\ScanInfo;
use Duplicator\Installer\Package\PComponents;

class DUPX_Validation_test_importable extends DUPX_Validation_abstract_item
{
    /** @var string[] */
    protected $failMessages = [];

    /**
     * Run test
     *
     * @return int  test status enum
     */
    protected function runTest()
    {
        if (DUPX_InstallerState::isClassicInstall()) {
            return self::LV_SKIP;
        }

        $archiveConf = DUPX_ArchiveConfig::getInstance();

        $coreFoldersCheck  = false;
        $subsitesCheck     = false;
        $globalTablesCheck = false;
        $componentsCheck   = false;

        switch (DUPX_InstallerState::getInstType()) {
            case DUPX_InstallerState::INSTALL_SINGLE_SITE:
            case DUPX_InstallerState::INSTALL_RBACKUP_SINGLE_SITE:
            case DUPX_InstallerState::INSTALL_RECOVERY_SINGLE_SITE:
                $coreFoldersCheck  = true;
                $globalTablesCheck = true;
                $componentsCheck   = true;
                break;
            case DUPX_InstallerState::INSTALL_MULTISITE_SUBDOMAIN:
            case DUPX_InstallerState::INSTALL_MULTISITE_SUBFOLDER:
            case DUPX_InstallerState::INSTALL_RBACKUP_MULTISITE_SUBDOMAIN:
            case DUPX_InstallerState::INSTALL_RBACKUP_MULTISITE_SUBFOLDER:
            case DUPX_InstallerState::INSTALL_RECOVERY_MULTISITE_SUBDOMAIN:
            case DUPX_InstallerState::INSTALL_RECOVERY_MULTISITE_SUBFOLDER:
                $coreFoldersCheck  = true;
                $subsitesCheck     = true;
                $globalTablesCheck = true;
                $componentsCheck   = true;
                break;
            case DUPX_InstallerState::INSTALL_STANDALONE:
                $coreFoldersCheck = true;
                $subsitesCheck    = true;
                $componentsCheck  = true;
                break;
            case DUPX_InstallerState::INSTALL_SINGLE_SITE_ON_SUBDOMAIN:
            case DUPX_InstallerState::INSTALL_SINGLE_SITE_ON_SUBFOLDER:
                $globalTablesCheck = true;
                $componentsCheck   = true;
                break;
            case DUPX_InstallerState::INSTALL_SUBSITE_ON_SUBDOMAIN:
            case DUPX_InstallerState::INSTALL_SUBSITE_ON_SUBFOLDER:
                $subsitesCheck   = true;
                $componentsCheck = true;
                break;
            case DUPX_InstallerState::INSTALL_NOT_SET:
            default:
                throw new Exception('Unknown mode');
        }

        $result = self::LV_PASS;

        if ($componentsCheck) {
            foreach (PComponents::COMPONENTS_DEFAULT as $component) {
                if (
                    in_array($component, $archiveConf->components)
                ) {
                    $this->failMessages[] = 'Component <b>' . PComponents::getLabel($component) . '</b> ' .
                        '<i class="fas fa-check-circle green"></i>' . ' included.';
                } else {
                    $this->failMessages[] = 'Component <b>' . PComponents::getLabel($component) . '</b> ' .
                        '<i class="fas fa-times-circle maroon"></i>' . ' excluded.';
                    if ($component != PComponents::COMP_OTHER) {
                        $result = self::LV_HARD_WARNING;
                    }
                }
            }
        }

        if ($coreFoldersCheck) {
            if (ScanInfo::getInstance()->hasFilteredCoreFolders()) {
                $this->failMessages[] = 'Some Wordpress core folders are missing. (e.g. wp-admin, wp-content, wp-includes, uploads, plugins, and themes folders)';
                $result               = self::LV_HARD_WARNING;
            }
        }

        if ($subsitesCheck) {
            for ($i = 0; $i < count($archiveConf->subsites); $i++) {
                if (
                    empty($archiveConf->subsites[$i]->filteredTables) &&
                    empty($archiveConf->subsites[$i]->filteredPaths)
                ) {
                    break;
                }
            }

            if ($i >= count($archiveConf->subsites)) {
                $this->failMessages[] = 'The package does not have any importable subsite.';
                $result               = self::LV_HARD_WARNING;
            }
        }


        if ($globalTablesCheck && !DUPX_InstallerState::dbDoNothing()) {
            if ($archiveConf->dbInfo->tablesBaseCount != $archiveConf->dbInfo->tablesFinalCount) {
                $this->failMessages[] = 'The package is missing some of the site tables.';
                $result               = self::LV_HARD_WARNING;
            }
        }

        return $result;
    }

    /**
     * Get test title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Partial Package Check';
    }

    /**
     * Render fail content
     *
     * @return string
     */
    protected function hwarnContent()
    {
        return dupxTplRender(
            'parts/validation/tests/importable-package',
            array(
                'testResult'  => $this->testResult,
                'failMessages' => $this->failMessages
            ),
            false
        );
    }

    /**
     * Render pass content
     *
     * @return string
     */
    protected function passContent()
    {
        return $this->hwarnContent();
    }
}
