# Entry Notifications for Craft CMS

Receive an email notification whenever a new entry is published in selected sections. No config files. No code. No fuss. Just pick your sections, add some email addresses, and Entry Notifications takes care of the rest.

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
