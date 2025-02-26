<?php

use Backstage\Laravel\OK\Notifications\CheckFailedNotification;
use Backstage\Laravel\OK\Notifications\Notifiable;

return [
    'notifications' => [
        'enabled' => env('OK_NOTIFICATIONS_ENABLED', true),

        'failed_notification' => CheckFailedNotification::class,

        'notifiable' => Notifiable::class,

        'interval_in_minutes' => env('OK_NOTIFICATION_INTERVAL', 60 * 24),

        'cache_driver' => env('OK_CACHE_DRIVER', 'file'),

        'via' => [
            // 'discord' => [
            //     'channel' => 123456790,
            // ],
            // 'mail' => [
            //     'to' => 'text@example.com',
            // ],
            // 'slack' => [
            //     'webhook' => 'webhook-url',
            // ],
            // 'telegram' => [
            //     'channel' => 1234567890,
            // ],
        ],
    ],

    'checks' => [
        'audit' => [
            'path' => [
                // '~/some/bin',
            ],
        ],
    ],
];
