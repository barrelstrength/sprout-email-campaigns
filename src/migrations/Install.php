<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com/
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   http://sprout.barrelstrengthdesign.com/license
 */

namespace barrelstrength\sproutcampaigns\migrations;

use barrelstrength\sproutbase\base\SproutDependencyInterface;
use barrelstrength\sproutbase\migrations\Install as SproutBaseInstall;
use barrelstrength\sproutbaseemail\migrations\Install as SproutBaseEmailInstall;
use barrelstrength\sproutbasefields\migrations\Install as SproutBaseFieldsInstall;
use barrelstrength\sproutbasesentemail\migrations\Install as SproutBaseSentEmailInstall;
use barrelstrength\sproutcampaigns\elements\CampaignEmail;
use barrelstrength\sproutcampaigns\records\CampaignEmail as CampaignEmailRecord;
use barrelstrength\sproutcampaigns\records\CampaignType as CampaignTypeRecord;
use barrelstrength\sproutemail\SproutEmail;
use craft\db\Migration;
use craft\db\Table;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $migration = new SproutBaseInstall();
        ob_start();
        $migration->safeUp();
        ob_end_clean();

        if (!$this->getDb()->tableExists(CampaignTypeRecord::tableName())) {
            $this->createTable(CampaignTypeRecord::tableName(), [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'handle' => $this->string()->notNull(),
                'mailer' => $this->string()->notNull(),
                'emailTemplateId' => $this->string(),
                'titleFormat' => $this->string(),
                'urlFormat' => $this->string(),
                'hasUrls' => $this->boolean(),
                'hasAdvancedTitles' => $this->boolean(),
                'template' => $this->string(),
                'templateCopyPaste' => $this->string(),
                'fieldLayoutId' => $this->integer(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);
        }

        if (!$this->getDb()->tableExists(CampaignEmailRecord::tableName())) {
            $this->createTable(CampaignEmailRecord::tableName(), [
                'id' => $this->primaryKey(),
                'subjectLine' => $this->string()->notNull(),
                'campaignTypeId' => $this->integer()->notNull(),
                'recipients' => $this->text(),
                'emailSettings' => $this->text(),
                'defaultBody' => $this->text(),
                'listSettings' => $this->text(),
                'fromName' => $this->string(),
                'fromEmail' => $this->string(),
                'replyToEmail' => $this->string(),
                'enableFileAttachments' => $this->boolean(),
                'dateScheduled' => $this->dateTime(),
                'dateSent' => $this->dateTime(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);

            $this->addForeignKey(null, CampaignEmailRecord::tableName(), ['id'], Table::ELEMENTS, ['id'], 'CASCADE');
        }
    }

    public function safeDown()
    {
        /** @var SproutEmail $plugin */
        $plugin = SproutEmail::getInstance();

        $sproutBaseEmailInUse = $plugin->dependencyInUse(SproutDependencyInterface::SPROUT_BASE_EMAIL);
        $sproutBaseFieldsInUse = $plugin->dependencyInUse(SproutDependencyInterface::SPROUT_BASE_FIELDS);
        $sproutBaseSentEmailInUse = $plugin->dependencyInUse(SproutDependencyInterface::SPROUT_BASE_SENT_EMAIL);
        $sproutBaseInUse = $plugin->dependencyInUse(SproutDependencyInterface::SPROUT_BASE);

        if (!$sproutBaseFieldsInUse) {
            $migration = new SproutBaseFieldsInstall();

            ob_start();
            $migration->safeDown();
            ob_end_clean();
        }

        if (!$sproutBaseSentEmailInUse) {
            $migration = new SproutBaseSentEmailInstall();

            ob_start();
            $migration->safeDown();
            ob_end_clean();
        }

        if (!$sproutBaseInUse) {
            $migration = new SproutBaseInstall();

            ob_start();
            $migration->safeDown();
            ob_end_clean();
        }

        // Delete Notification Email Elements
        $this->delete(Table::ELEMENTS, ['type' => CampaignEmail::class]);

        $this->dropTableIfExists('{{%sproutemail_campaigntypes}}');
        $this->dropTableIfExists('{{%sproutemail_campaignemails}}');
//        $this->dropTableIfExists('{{%sproutemail_mailers}}');
    }
}