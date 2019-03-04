<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com/
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   http://sprout.barrelstrengthdesign.com/license
 */

namespace barrelstrength\sproutcampaigns\migrations;

use craft\db\Migration;

class Install extends Migration
{
    private $campaignTypeTable = '{{%sproutemail_campaigntypes}}';
    private $campaignEmailTable = '{{%sproutemail_campaignemails}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTables();
    }

    public function createTables()
    {
        $campaignTypeTableExists = $this->getDb()->tableExists($this->campaignTypeTable);

        if ($campaignTypeTableExists == false) {
            $this->createTable($this->campaignTypeTable,
                [
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
                ]
            );
        }

        $campaignEmailTableExists = $this->getDb()->tableExists($this->campaignEmailTable);

        if ($campaignEmailTableExists == false) {
            $this->createTable($this->campaignEmailTable,
                [
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
                ]
            );

            $this->addForeignKey(null, $this->campaignEmailTable, ['id'], '{{%elements}}', ['id'], 'CASCADE', null);
        }

    }

    public function dropTables()
    {
        $campaignTypeTable = $this->getDb()->tableExists($this->campaignTypeTable);

        if ($campaignTypeTable) {
            $this->dropTable($this->campaignTypeTable);
        }

        $campaignEmailTable = $this->getDb()->tableExists($this->campaignEmailTable);

        if ($campaignEmailTable) {
            $this->dropTable($this->campaignEmailTable);
        }
    }
}