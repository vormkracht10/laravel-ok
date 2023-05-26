<?php

namespace Vormkracht10\LaravelOK\Checks;

use Carbon\CarbonInterface;
use Vormkracht10\LaravelOK\Enums\Status;

class Result
{
    public Check $check;

    public ?CarbonInterface $ended_at;

    public static function make(string $message = ''): self
    {
        return new self(Status::OK, $message);
    }

    public function __construct(
        public Status $status,
        public string $notificationMessage = '',
    ) {
    }

    public function check(Check $check): self
    {
        $this->check = $check;

        return $this;
    }

    public function notificationMessage(string $notificationMessage): self
    {
        $this->notificationMessage = $notificationMessage;

        return $this;
    }

    public function ok(string $message = ''): self
    {
        $this->notificationMessage = $message;

        $this->status = Status::ok();

        return $this;
    }

    public function warning(string $message = ''): self
    {
        $this->notificationMessage = $message;

        $this->status = Status::warning();

        return $this;
    }

    public function failed(string $message = ''): self
    {
        $this->notificationMessage = $message;

        $this->status = Status::failed();

        return $this;
    }

    /** @param  array<string, mixed>  $meta */
    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function endedAt(CarbonInterface $carbon): self
    {
        $this->ended_at = $carbon;

        return $this;
    }
}
