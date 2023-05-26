<?php

namespace Vormkracht10\LaravelOK\Listeners;

class SendCheckFailedNotification
{
    public function handle($event)
    {
        $notifiableClass = config('ok.notifications.notifiable');

        $notifiable = app($notifiableClass);

        $failedNotificationClass = config('ok.notifications.failed_notification');

        $notification = (new $failedNotificationClass($event->check, $event->result));

        $notifiable->notify($notification);
    }
}
