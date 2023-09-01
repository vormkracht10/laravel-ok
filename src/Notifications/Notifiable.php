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
        return config('ok.notifications.via.slack.channel');
    }

    public function routeNotificationForDiscord(): string
    {
        return config('ok.notifications.via.discord.channel');
    }

    public function routeNotificationForTelegram(): string
    {
        return config('ok.notifications.via.telegram.channel');
    }

    public function getKey(): int
    {
        return 1;
    }
}
