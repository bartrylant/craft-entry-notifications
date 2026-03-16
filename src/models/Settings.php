<?php

namespace bartrylant\entrynotifications\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * Newline-separated list of email addresses.
     */
    public string $recipientsText = '';

    /**
     * Array of section handles that trigger notifications.
     *
     * @var string[]
     */
    public array $sections = [];

    public string $emailSubject = 'New entry published in {sectionName}: {title}';

    public string $emailBody = "A new entry has been published on the website.\n\nSection: {sectionName}\nTitle: {title}\nDate: {date}\nAuthor: {authorName} ({authorEmail})\n\nView on the website: {entryUrl}\nEdit in the control panel: {cpUrl}";

    public function defineRules(): array
    {
        return [
            [['recipientsText', 'emailSubject', 'emailBody'], 'string'],
            [['sections'], 'safe'],
        ];
    }

    /**
     * Returns validated email addresses parsed from $recipientsText.
     *
     * @return string[]
     */
    public function getRecipients(): array
    {
        return array_values(array_filter(
            array_map('trim', preg_split('/[\r\n,]+/', $this->recipientsText)),
            fn($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)
        ));
    }
}
