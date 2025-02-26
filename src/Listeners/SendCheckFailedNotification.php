<?php

namespace Backstage\Laravel\OK\Listeners;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Events\CheckFailed;

class SendCheckFailedNotification
{
    public function handle(CheckFailed $event): void
    {
        if (! $this->shouldSendNotification($event->check)) {
            return;
        }

        $notifiableClass = config('ok.notifications.notifiable');

        $notifiable = app($notifiableClass);

        $failedNotificationClass = config('ok.notifications.failed_notification');

        $notification = new $failedNotificationClass($event->check, $event->result);

        $notifiable->notify($notification);

        $this->setNotificationTime($event->check);
    }

    protected function setNotificationTime(Check $check): bool
    {
        return Cache::driver('file')->forever(
            'laravel-ok::notifications::'.$check::class,
            now()->getTimestamp(),
        );
    }

    protected function getLastNotifiedTime(Check $check): ?Carbon
    {
        $timestamp = Cache::driver('file')
            ->get('laravel-ok::notifications::'.$check::class);

        if (! $timestamp) {
            return null;
        }

        return Carbon::createFromTimestamp($timestamp);
    }

    protected function shouldSendNotification(Check $check): bool
    {
        $lastNotified = $this->getLastNotifiedTime($check);

        if (! $lastNotified) {
            return true;
        }

        return now() >= $lastNotified->addMinutes($check->getNotificationInterval());
    }
}
