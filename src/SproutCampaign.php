<?php
/**
 * Sprout Email plugin for Craft CMS 3.x
 *
 * Flexible, integrated email marketing and notifications.
 *
 * @link      https://barrelstrengthdesign.com
 * @copyright Copyright (c) 2017 Barrelstrength
 */

namespace barrelstrength\sproutcampaign;

use barrelstrength\sproutbase\base\BaseSproutTrait;
use barrelstrength\sproutbaseemail\events\RegisterMailersEvent;
use barrelstrength\sproutbaseemail\SproutBaseEmailHelper;
use barrelstrength\sproutcampaign\mailers\CopyPasteMailer;
use barrelstrength\sproutcampaign\models\Settings;
use barrelstrength\sproutcampaign\services\App;
use barrelstrength\sproutbaseemail\services\Mailers;
use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use barrelstrength\sproutbase\SproutBaseHelper;
use craft\web\UrlManager;
use yii\base\Event;

/**
 * Class SproutCampaign
 *
 * @author    Barrelstrength
 * @package   SproutEmail
 * @since     3
 *
 *
 * @property array $cpNavItem
 * @property array $cpUrlRules
 */
class SproutCampaign extends Plugin
{
    use BaseSproutTrait;

    /**
     * Enable use of SproutCampaign::$plugin-> in place of Craft::$app->
     *
     * @var \barrelstrength\sproutcampaign\services\App
     */
    public static $app;

    /**
     * @var string
     */
    public static $pluginHandle = 'sprout-campaign';

    /**
     * @var bool
     */
    public $hasSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = true;

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var string
     */
    public $minVersionRequired = '1.0.0';

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        SproutBaseHelper::registerModule();
        SproutBaseEmailHelper::registerModule();

        $this->setComponents([
            'app' => App::class
        ]);

        self::$app = $this->get('app');

        Craft::setAlias('@sproutcampaign', $this->getBasePath());

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, $this->getCpUrlRules());
        });

        Event::on(Mailers::class, Mailers::EVENT_REGISTER_MAILER_TYPES, function(RegisterMailersEvent $event) {
            $event->mailers[] = new CopyPasteMailer();
        });
    }

    /**
     * @return Settings
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @return array
     */
    public function getCpNavItem(): array
    {
        $parent = parent::getCpNavItem();

        // Allow user to override plugin name in sidebar
        if ($this->getSettings()->pluginNameOverride) {
            $parent['label'] = $this->getSettings()->pluginNameOverride;
        }

        $parent['url'] = 'sprout-campaign';

        $navigation = [];

        $navigation['subnav']['campaigns'] = [
            'label' => Craft::t('sprout-campaign', 'Campaigns'),
            'url' => 'sprout-campaign/campaigns'
        ];


        $navigation['subnav']['settings'] = [
            'label' => Craft::t('sprout-campaign', 'Settings'),
            'url' => 'sprout-campaign/settings/general'
        ];

        return array_merge($parent, $navigation);
    }

    private function getCpUrlRules(): array
    {
        return [
            // Campaigns
            'sprout-campaign/preview/<emailType:campaign|notification|sent>/<emailId:\d+>' => [
                'template' => 'sprout-base-email/_special/preview'
            ],
            'sprout-campaign/campaigns/<campaignTypeId:\d+>/<emailId:new>' =>
                'sprout-campaign/campaign-email/edit-campaign-email',

            'sprout-campaign/campaigns/edit/<emailId:\d+>' =>
                'sprout-campaign/campaign-email/edit-campaign-email',

            'sprout-campaign' => [
                'template' => 'sprout-campaign/index'
            ],

            // Settings
            'sprout-campaign/settings/campaigntypes/edit/<campaignTypeId:\d+|new>' =>
                'sprout-campaignl/campaign-type/campaign-settings',


            'sprout-campaign/settings/<settingsSectionHandle:.*>' =>
                'sprout/settings/edit-settings',

            'sprout-campaign/settings' =>
                'sprout/settings/edit-settings'
        ];
    }
}
