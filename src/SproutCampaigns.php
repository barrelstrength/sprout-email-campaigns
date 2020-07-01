<?php
/**
 * Sprout Email plugin for Craft CMS 3.x
 *
 * Flexible, integrated email marketing and notifications.
 *
 * @link      https://barrelstrengthdesign.com
 * @copyright Copyright (c) 2017 Barrelstrength
 */

namespace barrelstrength\sproutcampaigns;

use barrelstrength\sproutbase\config\base\SproutBasePlugin;
use barrelstrength\sproutbase\config\configs\CampaignsConfig;
use barrelstrength\sproutbase\config\configs\EmailPreviewConfig;
use barrelstrength\sproutbase\config\configs\NotificationsConfig;
use barrelstrength\sproutbase\config\configs\ControlPanelConfig;
use barrelstrength\sproutbase\config\configs\ReportsConfig;
use barrelstrength\sproutbase\config\configs\SentEmailConfig;
use barrelstrength\sproutbase\SproutBaseHelper;
use Craft;

class SproutCampaigns extends SproutBasePlugin
{
    const EDITION_LITE = 'lite';
    const EDITION_PRO = 'pro';

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var string
     */
    public $minVersionRequired = '1.0.0';

    public static function editions(): array
    {
        return [
            self::EDITION_LITE,
            self::EDITION_PRO,
        ];
    }

    public static function getSproutConfigs(): array
    {
        return [
            CampaignsConfig::class,
            NotificationsConfig::class,
            SentEmailConfig::class,
            ReportsConfig::class
        ];
    }

    public function init()
    {
        parent::init();

        SproutBaseHelper::registerModule();
    }
}
