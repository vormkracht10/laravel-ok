<?php

use Vormkracht10\LaravelOK\Notifications\CheckFailedNotification;
use Vormkracht10\LaravelOK\Notifications\Notifiable;

return [
    'notifications' => [
        'enabled' => env('LARAVEL_OK_NOTIFICATIONS_ENABLED', true),

        'notification' => CheckFailedNotification::class,
        'notifiable' => Notifiable::class,

        'mail' => [

        ],

        'slack' => [

        ],

        'discord' => [

        ],

        'telegram' => [

        ],
    ],
];
