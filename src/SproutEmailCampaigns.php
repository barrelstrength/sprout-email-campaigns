<?php

namespace BarrelStrength\SproutEmailCampaigns;

use BarrelStrength\Sprout\campaigns\CampaignsModule;
use BarrelStrength\Sprout\core\db\MigrationHelper;
use BarrelStrength\Sprout\core\db\SproutPluginMigrationInterface;
use BarrelStrength\Sprout\core\db\SproutPluginMigrator;
use BarrelStrength\Sprout\core\editions\Edition;
use BarrelStrength\Sprout\core\modules\Modules;
use BarrelStrength\Sprout\mailer\MailerModule;
use Craft;
use craft\base\Plugin;
use craft\db\MigrationManager;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;
use yii\base\Event;

class SproutEmailCampaigns extends Plugin implements SproutPluginMigrationInterface
{
    public string $schemaVersion = '4.44.444';

    public static function editions(): array
    {
        return [
            Edition::LITE,
            Edition::PRO,
        ];
    }

    public static function getSchemaDependencies(): array
    {
        return [
            MailerModule::class, // Install first
            CampaignsModule::class,
        ];
    }

    public function getMigrator(): MigrationManager
    {
        return SproutPluginMigrator::make($this);
    }

    public function init(): void
    {
        parent::init();

        Event::on(
            Modules::class,
            Modules::EVENT_REGISTER_SPROUT_AVAILABLE_MODULES,
            static function(RegisterComponentTypesEvent $event) {
                $event->types[] = CampaignsModule::class;
            }
        );

        $this->instantiateSproutModules();
        $this->grantModuleEditions();
    }

    protected function instantiateSproutModules(): void
    {
        CampaignsModule::isEnabled() && CampaignsModule::getInstance();
    }

    protected function grantModuleEditions(): void
    {
        if ($this->edition === Edition::PRO) {
            CampaignsModule::isEnabled() && CampaignsModule::getInstance()->grantEdition(Edition::PRO);
        }
    }

    protected function afterInstall(): void
    {
        MigrationHelper::runMigrations($this);

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            return;
        }

        // Redirect to welcome page
        $url = UrlHelper::cpUrl('sprout/welcome/campaigns');
        Craft::$app->getResponse()->redirect($url)->send();
    }

    protected function beforeUninstall(): void
    {
        MigrationHelper::runUninstallMigrations($this);
    }
}
