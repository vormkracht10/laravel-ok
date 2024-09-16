<?php

namespace Vormkracht10\LaravelOK\Checks\Base;

use Exception;
use ReflectionClass;
use Cron\CronExpression;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Cache;
use Vormkracht10\LaravelOK\Enums\Status;
use Vormkracht10\LaravelOK\Events\CheckEnded;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Events\CheckFailed;
use Vormkracht10\LaravelOK\Events\CheckStarted;
use Vormkracht10\LaravelOK\Interfaces\Scheduled;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Vormkracht10\LaravelOK\Exceptions\CheckDidNotComplete;

abstract class Check implements Scheduled
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

    public function __invoke()
    {
        return $this->handle();
    }

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

    /// Default implementation for the `\Scheduled::schedule` method.
    /** @param CallbackEvent $callback */
    public static function schedule($callback)
    {
        if (!is_a(static::class, Scheduled::class, true)) {
            throw new \Exception("Can't schedule a cacher that does not implement the [" . Scheduled::class . '] interface');
        }

        $reflection = new ReflectionClass(static::class);

        $concrete = $reflection->getProperty('expression')->getDefaultValue();

        if (is_null($concrete)) {
            throw new \Exception('Either the Cached::$expression property or the [' . __METHOD__ . '] method must be overridden by the user.');
        }

        $callback->cron($concrete);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return (new ReflectionClass($this))->getName();
    }

    public function getShortName(): string
    {
        return (new ReflectionClass($this))->getShortName();
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

    public function getStatusCacheKey(): string
    {
        return 'ok::' . strtolower(str_replace('\\', '.', $this->getName()));
    }

    public function getLastUpdatedAt()
    {
        $cache = Cache::get($this->getStatusCacheKey());

        return $cache?->updated_at->diffForHumans();
    }

    final public function handle($event = null): Result
    {
        event(new CheckStarted($this));

        try {
            $result = $this->run();
        } catch (Exception $exception) {
            $exception = CheckDidNotComplete::make($this, $exception);

            report($exception);

            $result = $this->markAsCrashed();
        }

        $result->check($this)
            ->endedAt(now());

        if (
            $result->status === Status::FAILED ||
            $result->status === Status::CRASHED
        ) {
            event(new CheckFailed($this, $result));
        }

        event(new CheckEnded($this, $result));

        Cache::forever($this->getStatusCacheKey(), (object) [
            'updated_at' => now(),
        ]);

        return $result;
    }
}
