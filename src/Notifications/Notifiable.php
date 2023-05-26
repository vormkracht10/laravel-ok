<?php

namespace Vormkracht10\LaravelOK\Notifications;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    public function routeNotificationForMail(): string|array
    {
        return config('ok.notifications.mail.to');
    }

    public function routeNotificationForSlack(): string
    {
        return config('ok.notifications.slack.webhook_url');
    }

    public function routeNotificationsForDiscord(): string
    {
        return config('ok.notifications.discord.channel');
    }

    public function routeNotificationsForTelegram(): string
    {
        return config('ok.notifications.telegram.channel');
    }

    public function getKey(): int
    {
        return 1;
    }
}
