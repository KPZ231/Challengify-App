# Challengify Email Notification System

This document explains how to set up and use the email notification system in Challengify.

## Overview

The email notification system sends automated emails to users for the following events:

1. **New Follower Notifications**: When a user follows another user
2. **Challenge Ending Soon**: One hour before a challenge ends
3. **New Challenge Published**: When a new challenge is published

Emails are only sent to users who have enabled email notifications in their account settings.

## Configuration

The email system uses SMTP for sending emails. Configuration is done through the `.env` file:

```
# Mail settings
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@challengify.com
MAIL_FROM_NAME=Challengify
```

You should replace these values with your actual SMTP credentials.

## Automatic Notifications

### New Follower Notifications

These are automatically triggered when a user follows another user. The followed user will receive an email notification if they have enabled email notifications.

### New Challenge Notifications

These are automatically triggered when a challenge's status is changed to "published". All users with email notifications enabled will receive an email about the new challenge.

### Challenge Ending Soon Notifications

These require a cron job to run the notification script. Set up a cron job to run `bin/send-challenge-notifications.php` every hour:

```
0 * * * * php /path/to/your/app/bin/send-challenge-notifications.php
```

This script checks for challenges ending within the next hour and sends notifications to users who have email notifications enabled.

## Implementation Details

The notification system consists of the following components:

1. `MailService` - Handles the actual sending of emails through SMTP
2. `NotificationService` - Manages different types of notifications and who receives them
3. `User` model - Contains user preferences for notifications
4. `Challenge` model - Includes hooks for notification events
5. `bin/send-challenge-notifications.php` - CLI script for sending "challenge ending soon" notifications

## Testing

To test the email system, you can:

1. Use Mailtrap.io for development (configure in your .env file)
2. Follow another user to trigger a follower notification
3. Publish a challenge to trigger new challenge notifications
4. Run the CLI script manually to test challenge ending notifications:

```
php bin/send-challenge-notifications.php
``` 