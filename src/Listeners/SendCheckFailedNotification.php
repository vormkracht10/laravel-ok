<?php

namespace Vormkracht10\LaravelOK\Listeners;

use Illuminate\Support\Facades\Cache;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Events\CheckFailed;
use Vormkracht10\LaravelOK\Facades\OK;

class SendCheckFailedNotification
{
    public function handle(CheckFailed $event)
    {
        if (! $this->shouldSendNotification($event->check)) {
            return;
        }

        $notifiableClass = config('ok.notifications.notifiable');

        $notifiable = app($notifiableClass);

        $failedNotificationClass = config('ok.notifications.failed_notification');

        $notification = new $failedNotificationClass($event->check, $event->result);

        $notifiable->notify($notification);

        $class = $event->check::class;

        Cache::driver('file')->set(
            "laravel-ok::runs::{$class}",
            now()->getTimestamp(),
        );
    }

    protected function shouldSendNotification(Check $check): bool
    {
        $lastRun = OK::lastRun($check::class);

        $interval = $check->getReportInterval();

        return $lastRun < $interval;
    }
}
