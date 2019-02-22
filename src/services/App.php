<?php

namespace barrelstrength\sproutcampaign\services;

use craft\base\Component;

class App extends Component
{
    /**
     * @var CampaignEmails
     */
    public $campaignEmails;
    /**
     * @var CampaignTypes
     */
    public $campaignTypes;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->campaignTypes = new CampaignTypes();
        $this->campaignEmails = new CampaignEmails();
    }
}
