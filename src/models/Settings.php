<?php

namespace barrelstrength\sproutcampaign\models;

use barrelstrength\sproutbase\base\SproutSettingsInterface;
use Craft;
use craft\base\Model;

/**
 *
 * @property array $settingsNavItems
 */
class Settings extends Model implements SproutSettingsInterface
{
    /**
     * @var string
     */
    public $pluginNameOverride = '';
    /**
     * @var bool
     */
    public $appendTitleValue = false;
    /**
     * @var string
     */
    public $localeIdOverride = '';
    /**
     * @var bool
     */
    public $displayFieldHandles = false;

    /**
     * @var bool
     */
    public $enableNotificationEmails = true;
    /**
     * @var bool
     */
    public $enableCampaignEmails = false;
    /**
     * @var bool
     */
    public $enableSentEmails = false;
    /**
     * @var null
     */
    public $emailTemplateId;
    /**
     * @var int
     */
    public $enablePerEmailEmailTemplateIdOverride = 0;

    /**
     * @inheritdoc
     */
    public function getSettingsNavItems(): array
    {
        return [
            'settingsHeading' => [
                'heading' => Craft::t('sprout-campaign', 'Settings'),
            ],
            'general' => [
                'label' => Craft::t('sprout-campaign', 'General'),
                'url' => 'sprout-campaign/settings/general',
                'selected' => 'general',
                'template' => 'sprout-campaign/settings/general'
            ],
            'emailHeading' => [
                'heading' => Craft::t('sprout-campaign', 'Email'),
            ],
            'campaigntypes' => [
                'label' => Craft::t('sprout-campaign', 'Campaigns'),
                'url' => 'sprout-campaign/settings/campaigntypes',
                'selected' => 'campaigntypes',
                'template' => 'sprout-base-email/settings/campaigntypes',
                'settingsForm' => false
            ],
        ];
    }
}