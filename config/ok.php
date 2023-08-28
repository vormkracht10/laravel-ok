<?php

use Vormkracht10\LaravelOK\Notifications\CheckFailedNotification;
use Vormkracht10\LaravelOK\Notifications\Notifiable;

return [
    'notifications' => [
        'enabled' => env('LARAVEL_OK_NOTIFICATIONS_ENABLED', true),

        'failed_notification' => CheckFailedNotification::class,

        'notifiable' => Notifiable::class,

        'via' => [
            //             'discord' => [
            //                 'channel' => 123456790,
            //             ],
            //             'mail' => [
            //                 'to' => 'text@example.com',
            //             ],
            //             'slack' => [
            //                 'webhook_url' => 'webhook-url',
            //             ],
            //             'telegram' => [
            //                 'channel' => 1234567890,
            //            ],
        ],
    ],

    'checks' => [
        'audit' => [
            'path' => [
                //                '~/some/bin',
            ],
        ],
    ],
];
