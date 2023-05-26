<?php

use Vormkracht10\LaravelOK\Notifications\CheckFailedNotification;
use Vormkracht10\LaravelOK\Notifications\Notifiable;

return [
    'notifications' => [
        'enabled' => env('LARAVEL_OK_NOTIFICATIONS_ENABLED', true),

        'failed_notification' => CheckFailedNotification::class,

        'notifiable' => Notifiable::class,

        'via' => [
            // 'discord' => [
            //     'channel' => 1111586288131391548,
            // ],
            'mail' => [
                'to' => 'mark@vormkracht10.nl',
            ],
            // 'slack' => [
            //     'webhook_url' => 'webhook-url',
            // ],
            // 'telegram' => [
            //     'channel' => 1234567890,
            // ],
        ],
    ],
];
