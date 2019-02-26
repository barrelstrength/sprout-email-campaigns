<?php

namespace barrelstrength\sproutcampaign\controllers;

use barrelstrength\sproutcampaign\elements\CampaignEmail;
use barrelstrength\sproutcampaign\SproutCampaign;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;
use Craft;
use yii\web\Response;

class CopyPasteController extends Controller
{
    /**
     * Updates a Copy/Paste Campaign Email to add a Date Sent
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     * @throws \yii\web\BadRequestHttpException
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
                'message' => Craft::t('sprout-campaign', 'Email marked as sent.')
            ]);

            return $this->asJson([
                'success' => true,
                'content' => $html
            ]);
        }

        $html = Craft::$app->getView()->renderTemplate('sprout-base-email/_modals/response', [
            'success' => true,
            'email' => $campaignEmail,
            'message' => Craft::t('sprout-campaign', 'Unable to mark email as sent.')
        ]);

        return $this->asJson([
            'success' => true,
            'content' => $html
        ]);
    }
}
