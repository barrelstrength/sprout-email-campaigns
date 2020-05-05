<?php

namespace barrelstrength\sproutcampaigns\controllers;

use barrelstrength\sproutcampaigns\elements\CampaignEmail;
use barrelstrength\sproutcampaigns\SproutCampaign;
use Craft;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;
use Throwable;
use Twig_Error_Loader;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class CopyPasteController extends Controller
{
    /**
     * Updates a Copy/Paste Campaign Email to add a Date Sent
     *
     * @return Response
     * @throws \Exception
     * @throws Throwable
     * @throws Twig_Error_Loader
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function actionMarkSent(): Response
    {
        $this->requirePostRequest();

        $emailId = Craft::$app->getRequest()->getBodyParam('emailId');

        /** @var  $campaignEmail CampaignEmail */
        $campaignEmail = SproutCampaign::$app->campaignEmails->getCampaignEmailById($emailId);

        $campaignEmail->dateSent = DateTimeHelper::currentUTCDateTime();

        if (SproutCampaign::$app->campaignEmails->saveCampaignEmail($campaignEmail)) {
            $html = Craft::$app->getView()->renderTemplate('sprout-base-email/_modals/response', [
                'success' => true,
                'email' => $campaignEmail,
                'message' => Craft::t('sprout-campaigns', 'Email marked as sent.')
            ]);

            return $this->asJson([
                'success' => true,
                'content' => $html
            ]);
        }

        $html = Craft::$app->getView()->renderTemplate('sprout-base-email/_modals/response', [
            'success' => true,
            'email' => $campaignEmail,
            'message' => Craft::t('sprout-campaigns', 'Unable to mark email as sent.')
        ]);

        return $this->asJson([
            'success' => true,
            'content' => $html
        ]);
    }
}
