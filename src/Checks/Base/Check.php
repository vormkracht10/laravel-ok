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

    protected string $expression = '* * * * *';

    protected ?string $name = null;

    protected ?string $message = null;

    protected ?string $label = null;

    protected bool $shouldRun = true;

    protected int $timesToFailWithoutNotification = 1;

    public function __construct()
    {
    }

    public static function config(): static
    {
        $instance = app(static::class);

        $instance->everyMinute();

        return $instance;
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
}
