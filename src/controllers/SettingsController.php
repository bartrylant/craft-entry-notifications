<?php

namespace bartrylant\entrynotifications\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use bartrylant\entrynotifications\Plugin;

class SettingsController extends Controller
{
    public function actionSendTestEmail(): \yii\web\Response
    {
        $this->requirePostRequest();
        $this->requireLogin();

        /** @var \bartrylant\entrynotifications\models\Settings $settings */
        $settings = Plugin::getInstance()->getSettings();
        $recipients = $settings->getRecipients();

        if (empty($recipients)) {
            return $this->asJson([
                'success' => false,
                'message' => Craft::t('entry-notifications', 'No recipients configured.'),
            ]);
        }

        $currentUser = Craft::$app->getUser()->getIdentity();

        $variables = [
            '{sectionName}'  => Craft::t('entry-notifications', 'Test Section'),
            '{title}'        => Craft::t('entry-notifications', 'Test Entry Title'),
            '{entryUrl}'     => Craft::$app->getSites()->getPrimarySite()->getBaseUrl(),
            '{cpUrl}'        => UrlHelper::cpUrl(),
            '{date}'         => date('d/m/Y'),
            '{authorName}'   => $currentUser ? ($currentUser->fullName ?: $currentUser->username) : 'Unknown',
            '{authorEmail}'  => $currentUser ? ($currentUser->email ?? '') : '',
        ];

        $subject = '[TEST] ' . str_replace(array_keys($variables), array_values($variables), $settings->emailSubject);
        $body    = nl2br(str_replace(array_keys($variables), array_values($variables), $settings->emailBody));

        $sent = 0;
        foreach ($recipients as $email) {
            $result = Craft::$app->getMailer()
                ->compose()
                ->setTo($email)
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();

            if ($result) {
                $sent++;
            }
        }

        if ($sent > 0) {
            return $this->asJson([
                'success' => true,
                'message' => Craft::t('entry-notifications', 'Test email sent to {n} recipient(s).', ['n' => $sent]),
            ]);
        }

        return $this->asJson([
            'success' => false,
            'message' => Craft::t('entry-notifications', 'Failed to send test email.'),
        ]);
    }
}
