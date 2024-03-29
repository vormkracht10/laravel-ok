<?php

namespace Vormkracht10\LaravelOK\Checks\Base;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Vormkracht10\LaravelOK\Enums\Status;

abstract class Check
{
    use ManagesFrequencies;

    /**
     * The view name used for mail notifications if any.
     */
    public string $view;

    /**
     * The data passed to the view for mail notifications.
     */
    public array $data = [];

    protected string $expression = '* * * * *';

    protected int $repeatSeconds;

    protected int $notificationIntervalInMinutes;

    protected ?string $name = null;

    protected ?string $message = null;

    protected ?string $label = null;

    protected bool $shouldRun = true;

    protected int $timesToFailWithoutNotification = 1;

    public static function config(): static
    {
        $instance = app(static::class);

        $instance->everyMinute();

        return $instance;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getRepeatSeconds(): int
    {
        return $this->repeatSeconds;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $baseName = class_basename(static::class);

        return Str::of($baseName)->beforeLast('Check');
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function shouldRun(): bool
    {
        if (! $this->shouldRun) {
            return false;
        }

        $date = Date::now();

        return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
    }

    public function if(bool $condition)
    {
        $this->shouldRun = $condition;

        return $this;
    }

    public function unless(bool $condition)
    {
        $this->shouldRun = ! $condition;

        return $this;
    }

    abstract public function run(): Result;

    public function markAsCrashed(): Result
    {
        return new Result(Status::CRASHED);
    }

    public function getNotificationInterval(): int
    {
        return $this->notificationIntervalInMinutes ?? config('ok.notifications.interval_in_minutes');
    }

    public function setNotificationInterval(int $intervalInMinutes): static
    {
        $this->notificationIntervalInMinutes = $intervalInMinutes;

        return $this;
    }
}
