<?php

namespace barrelstrength\sproutcampaign\controllers;

use barrelstrength\sproutbaseemail\base\Mailer;
use barrelstrength\sproutbaseemail\mailers\DefaultMailer;
use barrelstrength\sproutbaseemail\SproutBaseEmail;
use barrelstrength\sproutcampaign\elements\CampaignEmail;
use barrelstrength\sproutcampaign\models\CampaignType;
use barrelstrength\sproutcampaign\SproutCampaign;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use Craft;
use yii\base\InvalidArgumentException;
use yii\web\Response;

class CampaignTypeController extends Controller
{
    /**
     * Renders a Campaign Type settings template
     *
     * @param                   $campaignTypeId
     * @param CampaignType|null $campaignType
     *
     * @return Response
     * @throws \Exception
     */
    public function actionCampaignSettings($campaignTypeId, CampaignType $campaignType = null): Response
    {
        if ($campaignTypeId && $campaignType === null) {

            if ($campaignTypeId == 'new') {
                $campaignType = new CampaignType();
            } else {
                $campaignType = SproutCampaign::$app->campaignTypes->getCampaignTypeById($campaignTypeId);

                if ($campaignType->id === null) {
                    throw new InvalidArgumentException('Invalid campaign type id');
                }
            }
        }

        $mailerOptions = [];

        $mailers = SproutBaseEmail::$app->mailers->getMailers();

        if (!empty($mailers)) {
            foreach ($mailers as $key => $mailer) {
                /**
                 * @var $mailer Mailer
                 */
                $mailerOptions[$key]['value'] = get_class($mailer);
                $mailerOptions[$key]['label'] = $mailer::displayName();
            }
        }

        // Disable default mailer on campaign emails
        unset($mailerOptions[DefaultMailer::class]);

        // Load our template
        return $this->renderTemplate('sprout-campaign/settings/campaigntypes/_edit', [
            'mailers' => $mailerOptions,
            'campaignTypeId' => $campaignTypeId,
            'campaignType' => $campaignType
        ]);
    }

    /**
     * Saves a Campaign Type
     *
     * @throws \Exception
     * @throws \craft\errors\SiteNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveCampaignType(): Response
    {
        $this->requirePostRequest();

        $campaignTypeId = Craft::$app->getRequest()->getBodyParam('campaignTypeId');
        $campaignType = SproutCampaign::$app->campaignTypes->getCampaignTypeById($campaignTypeId);

        $campaignType->setAttributes(Craft::$app->getRequest()->getBodyParam('sproutCampaign'), false);

        // Set the field layout
        $fieldLayout = Craft::$app->getFields()->assembleLayoutFromPost();

        $fieldLayout->type = CampaignEmail::class;

        $campaignType->setFieldLayout($fieldLayout);

        if (!SproutCampaign::$app->campaignTypes->saveCampaignType($campaignType)) {
            Craft::$app->getSession()->setError(Craft::t('sprout-campaign', 'Unable to save campaign.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'campaignType' => $campaignType
            ]);

            $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setNotice(Craft::t('sprout-campaign', 'Campaign saved.'));

        $url = UrlHelper::cpUrl('sprout-campaign/settings/campaigntypes/edit/'.$campaignType->id);

        return $this->redirect($url);
    }

    /**
     * Deletes a Campaign Type
     *
     * @return Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDeleteCampaignType(): Response
    {
        $this->requirePostRequest();

        $campaignTypeId = Craft::$app->getRequest()->getBodyParam('id');

        $session = Craft::$app->getSession();

        if ($session AND $result = sproutcampaign::$app->campaignTypes->deleteCampaignType($campaignTypeId)) {
            $session->setNotice(Craft::t('sprout-email', 'Campaign Type deleted.'));

            return $this->asJson([
                'success' => true
            ]);
        }

        $session->setError(Craft::t('sprout-email', "Couldn't delete Campaign."));

        return $this->asJson([
            'success' => false
        ]);
    }
}
