<?php

namespace barrelstrength\sproutcampaigns\controllers;

use barrelstrength\sproutbaseemail\base\Mailer;
use barrelstrength\sproutbaseemail\mailers\DefaultMailer;
use barrelstrength\sproutbaseemail\SproutBaseEmail;
use barrelstrength\sproutcampaigns\elements\CampaignEmail;
use barrelstrength\sproutcampaigns\models\CampaignType;
use barrelstrength\sproutcampaigns\SproutCampaign;
use Craft;
use craft\errors\MissingComponentException;
use craft\errors\SiteNotFoundException;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use yii\base\InvalidArgumentException;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
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

        $mailers = SproutBaseEmail::$app->mailers->getRegisteredMailers();

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
     * @throws SiteNotFoundException
     * @throws \yii\base\Exception
     * @throws Exception
     * @throws BadRequestHttpException
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
            Craft::$app->getSession()->setError(Craft::t('sprout-campaigns', 'Unable to save campaign.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'campaignType' => $campaignType
            ]);

            $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setNotice(Craft::t('sprout-campaigns', 'Campaign saved.'));

        $url = UrlHelper::cpUrl('sprout-campaign/settings/campaigntypes/edit/'.$campaignType->id);

        return $this->redirect($url);
    }

    /**
     * Deletes a Campaign Type
     *
     * @return Response
     * @throws MissingComponentException
     * @throws BadRequestHttpException
     */
    public function actionDeleteCampaignType(): Response
    {
        $this->requirePostRequest();

        $campaignTypeId = Craft::$app->getRequest()->getBodyParam('id');

        $session = Craft::$app->getSession();

        if ($session and $result = sproutcampaign::$app->campaignTypes->deleteCampaignType($campaignTypeId)) {
            $session->setNotice(Craft::t('sprout-campaigns', 'Campaign Type deleted.'));

            return $this->asJson([
                'success' => true
            ]);
        }

        $session->setError(Craft::t('sprout-campaigns', "Couldn't delete Campaign."));

        return $this->asJson([
            'success' => false
        ]);
    }
}
