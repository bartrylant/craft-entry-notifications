# Entry Notifications for Craft CMS

Entry Notifications emails your chosen recipients the moment a new entry goes live — no config files, no webhooks, no code. Open the control panel, choose the sections, add the recipients, and you’re done.

This plugin is intentionally minimal. No complex condition builders, no scheduled entries, no cron jobs — just simple, reliable notifications when an entry is published.

## Features

- Select which sections trigger a notification
- Configure one or more email recipients
- Customise the email subject and body
- Use built-in variables to include entry details in your email

## Available variables

| Variable | Description |
|---|---|
| `{sectionName}` | Name of the section |
| `{title}` | Title of the entry |
| `{entryUrl}` | URL of the entry on the front-end |
| `{cpUrl}` | URL of the entry in the control panel |
| `{date}` | Date the entry was published (dd/mm/yyyy) |

## Requirements

- Craft CMS 5.0.0 or later

## Installation

Install via the Craft Plugin Store or with Composer:

```bash
composer require bartrylant/craft-entry-notifications
```

Then go to **Settings → Plugins** and install **Entry Notifications**.

## Configuration

Go to **Settings → Plugins → Entry Notifications** to configure:

1. **Recipients** — one email address per line
2. **Sections** — select which sections trigger a notification
3. **Subject** — customise the email subject
4. **Body** — customise the email body (plain text, line breaks are preserved)
