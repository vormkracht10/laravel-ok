<?php


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Events\CheckFailed;
use Vormkracht10\LaravelOK\Notifications\CheckFailedNotification;
use function Pest\Laravel\travel;

$failed = new class extends Check
{
    public function run(): Result
    {
        return Result::new()->failed('failed');
    }
};

it('sends a notification if a check has failed', function () use ($failed) {

    config()->set('ok.notifications.via.mail.to', 'test@example.com');

    $check = $failed->setNotificationInterval(1);

    $event = new CheckFailed($check, $check->run());

    Notification::fake();

    Cache::driver('file')->forget('laravel-ok::notifications::'.$check::class);

    event($event);

    Notification::assertSentTimes(
        CheckFailedNotification::class, 1
    );
});

it('doesn\'t send a notification if the notification interval has not yet passed', function () use ($failed) {
    config()->set('ok.notifications.via.mail.to', 'test@example.com');

    $check = $failed->setNotificationInterval(30);

    $event = new CheckFailed($check, $check->run());

    Notification::fake();

    Cache::driver('file')->forget('laravel-ok::notifications::'.$check::class);

    event($event);

    event($event);

    Notification::assertSentTimes(
        CheckFailedNotification::class, 1
    );
});

it('does send a notification when the notification interval has passed', function () use ($failed) {
    config()->set('ok.notifications.via.mail.to', 'test@example.com');

    $check = $failed->setNotificationInterval(30);

    $event = new CheckFailed($check, $check->run());

    Notification::fake();

    Cache::driver('file')->forget('laravel-ok::notifications::'.$check::class);

    event($event);

    travel(30)->minutes();

    event($event);

    Notification::assertSentTimes(
        CheckFailedNotification::class, 2
    );
});
