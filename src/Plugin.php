<?php

namespace bartrylant\entrynotifications;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use bartrylant\entrynotifications\jobs\SendNotificationJob;
use bartrylant\entrynotifications\models\Settings;
use yii\base\Event;

/**
 * Entry Notifications plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();

        // Register CP template root so entry-notifications/_settings resolves correctly
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $event->roots['entry-notifications'] = __DIR__ . '/templates';
            }
        );

        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('entry-notifications/_settings', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
            'allSections' => Craft::$app->entries->getAllSections(),
        ]);
    }

    public function beforeSaveSettings(): bool
    {
        // When no section checkboxes are checked, the key is absent from POST.
        // Explicitly reset sections to empty array in that case.
        $request = Craft::$app->getRequest();
        if (!$request->isConsoleRequest) {
            $settingsData = $request->getBodyParam('settings', []);
            if (is_array($settingsData) && !array_key_exists('sections', $settingsData)) {
                $this->getSettings()->sections = [];
            }
        }

        return parent::beforeSaveSettings();
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            Entry::class,
            Entry::EVENT_AFTER_SAVE,
            function (ModelEvent $event) {
                /** @var Entry $entry */
                $entry = $event->sender;

                if ($entry->isDraft || !$entry->firstSave || $entry->section === null) {
                    return;
                }

                /** @var Settings $settings */
                $settings = $this->getSettings();
                $selectedSections = $settings->sections ?? [];
                $recipients = $settings->getRecipients();

                if (
                    empty($selectedSections) ||
                    empty($recipients) ||
                    !in_array($entry->section->handle, $selectedSections, true)
                ) {
                    return;
                }

                $author = $entry->getAuthor();

                $variables = [
                    '{sectionName}'  => $entry->section->name,
                    '{title}'        => $entry->title,
                    '{entryUrl}'     => $entry->url ?? '',
                    '{cpUrl}'        => $entry->cpEditUrl,
                    '{date}'         => $entry->dateCreated->format('d/m/Y'),
                    '{authorName}'   => $author ? ($author->fullName ?: $author->username) : '',
                    '{authorEmail}'  => $author ? ($author->email ?? '') : '',
                ];

                $subject = str_replace(array_keys($variables), array_values($variables), $settings->emailSubject);
                $body    = nl2br(str_replace(array_keys($variables), array_values($variables), $settings->emailBody));

                foreach ($recipients as $email) {
                    Craft::$app->queue->push(new SendNotificationJob([
                        'email'   => $email,
                        'subject' => $subject,
                        'body'    => $body,
                    ]));
                }
            }
        );
    }
}
