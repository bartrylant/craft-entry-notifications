<?php

namespace bartrylant\entrynotifications\jobs;

use Craft;
use craft\queue\BaseJob;

class SendNotificationJob extends BaseJob
{
    public string $email = '';
    public string $subject = '';
    public string $body = '';

    public function execute($queue): void
    {
        Craft::$app->getMailer()
            ->compose()
            ->setTo($this->email)
            ->setSubject($this->subject)
            ->setHtmlBody($this->body)
            ->send();
    }

    protected function defaultDescription(): ?string
    {
        return 'Send entry notification to ' . $this->email;
    }
}
