<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com/
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   http://sprout.barrelstrengthdesign.com/license
 */

namespace barrelstrength\sproutcampaigns\migrations;

use barrelstrength\sproutbase\SproutBase;
use barrelstrength\sproutcampaigns\SproutCampaigns;
use craft\db\Migration;
use ReflectionException;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\web\ServerErrorHttpException;

class Install extends Migration
{
    /**
     * @return bool|void
     * @throws ReflectionException
     * @throws ErrorException
     * @throws Exception
     * @throws NotSupportedException
     * @throws ServerErrorHttpException
     */
    public function safeUp()
    {
        SproutBase::$app->config->runInstallMigrations(SproutCampaigns::getInstance());
    }

    /**
     * @return bool|void
     * @throws ReflectionException
     */
    public function safeDown()
    {
        SproutBase::$app->config->runUninstallMigrations(SproutCampaigns::getInstance());
    }
}