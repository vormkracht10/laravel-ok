<?php

namespace Vormkracht10\LaravelOK\Notifications;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    public function routeNotificationForMail(): string|array
    {
        return config('ok.notifications.via.mail.to');
    }

    public function routeNotificationForSlack(): string
    {
        return config('ok.notifications.via.slack.webhook_url');
    }

    public function routeNotificationsForDiscord(): string
    {
        return config('ok.notifications.via.discord.channel');
    }

    public function routeNotificationsForTelegram(): string
    {
        return config('ok.notifications.via.telegram.channel');
    }

    public function getKey(): int
    {
        return 1;
    }
}
